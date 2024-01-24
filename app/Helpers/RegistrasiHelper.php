<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use App\Models\Antrian;
use App\Models\Registrasi;
use App\Models\Pasien;
use Modules\Transaksi\Entities\Transaksi;
use Modules\Pendaftaran\Entities\RawatJalan;
use Modules\Pendaftaran\Entities\RawatInap;
use Modules\Pendaftaran\Entities\TrxOperasi;
use Modules\Transaksi\Entities\TrxResep;
use Modules\Transaksi\Entities\Pemeriksaan;
use Modules\Radiologi\Entities\TrxRad;
use Modules\PatologiAnatomi\Entities\TrxPA;
use Modules\Laboratorium\Entities\TrxLab;

 
class RegistrasiHelper {   
    
    /**
     * Registrasi ID diperuntukkan untuk semua transaksi yang 
     * diperlukan pendaftaran :
     * Rawat Jalan
     * Rawat Inap
     * Laboratorium
     * Radiologi
     * Apotek
     */
    function RegistrasiId($clientId) 
    {
        try {
            $id = $clientId.'-'.date('Ymd').'-REG00001';
            $maxId = Registrasi::withTrashed()->where('client_id', $clientId)
                ->where('reg_id', 'LIKE', $clientId.'-'.date('Ymd').'-REG%')
                ->max('reg_id');

            if (!$maxId) { $id = $clientId.'-'.date('Ymd').'-REG00001'; } 
            else {
                $max = str_replace($clientId.'-'.date('Ymd').'-REG', '', $maxId);
                $count = (int)$max + 1;
                if ($count < 10) { $id = $clientId.'-'.date('Ymd').'-REG0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-'.date('Ymd').'-REG000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-'.date('Ymd').'-REG00' . $count; } 
                elseif ($count >= 1000 && $count < 10000 ) { $id = $clientId.'-'.date('Ymd').'-REG0' . $count; } 
                else { $id = $clientId.'-'.date('Ymd').'-REG' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) { return null; }
    }

    /**
     * booking code digunakan untuk membuat kode booking registrasi
     * agar bisa berbentuk simple
     * untuk keperluan Rawat Jalan
     * Nantinya dapat digunakan untuk LAB, RADIOLOGI, dsb (unit yang melayani untuk booking antrian) 
     */
    function BookingCode($clientId) 
    {
        try {
            $id = date('ymd').'001';
            $maxId = Registrasi::where('client_id', $clientId)->where('is_aktif',1)
                ->where('kode_booking', 'LIKE', date('ymd').'%')
                ->max('kode_booking');
            
            if (!$maxId) { $id = date('ymd').'001'; } 
            else {
                $maxId = str_replace( date('ymd'), '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = date('ymd').'00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = date('ymd').'0' . $count; } 
                else { $id = date('ymd'). $count; }
            }
            return $id;
        } 
        catch (\Exception $e) { return null; }
    }

    /**
     * no antrian dibuat untuk pemeriksaan yang membutuhkan no antrian.
     * perhitungan didasarkan sesuai dengan jadwal ID (praktek dokter), 
     * prefix no (berdasarkan master tb_dokter_unit), tanggal periksa
     * untuk lab dan radiologi hanya berdasarkan tanggal periksa
     */
    public function NoAntrian($clientId, $prefixNo, $tglPeriksa, $jadwalId)
    {
        try {
            $id = $prefixNo.'001';
            $angka = 1;
            $maxId = Registrasi::where('client_id', $clientId)->withTrashed()
                ->where('no_antrian','ILIKE',$prefixNo.'%')
                ->where('tgl_periksa',$tglPeriksa)
                ->where('jadwal_id',$jadwalId)
                ->max('no_antrian');
            
            if ($maxId == null) { 
                $id = $prefixNo.'001'; $angka = 1;
            } 
            else {
                $maxId = str_replace( $prefixNo, '', $maxId);
                $count = $maxId + 1;
                $angka = $count;
                if ($count < 10) { $id = $prefixNo.'00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $prefixNo.'0' . $count; } 
                else { $id = $prefixNo . $count; } 
            }

            $data['id'] = $id;
            $data['angka'] = $angka;
            
            return $data;
        } 
        catch (\Exception $e) { return $e->getMessage(); }
    }

    /**
     * no pendaftaran digunakan untuk no urutan tanpa melihat jenis 
     * layanan, yang ditujukan untuk keperluan bagian administrasi
     * diberikan pada saat pasien (booking) terkonfirmasi hadir
     */
    public function NoPendaftaran($clientId, $tglPeriksa)
    {
        try {
            $id = '001'; $angka = null;
            $maxId = Registrasi::where('client_id', $clientId)
                ->withTrashed()->where('tgl_periksa',$tglPeriksa)
                ->max('no_pendaftaran');
            
            if (!$maxId) { $id = '001'; $angka = 1;} 
            else {
                $count = $maxId + 1; $angka = $count;
                if ($count < 10) { $id = '00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = '0' . $count; } 
                else { $id = $count; } 
            }

            $data['id'] = $id; $data['angka'] = $angka;            
            return $data;
        } 
        catch (\Exception $e) { 
            return null; 
        }
    }

    /**
     * fungsi ini obsolete.
     * setiap transaksi ID akan berasal dari masing-masing 
     * table yang berkenaan dengan transaksi
     */
    public function TransactionId($clientId, $prefix)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-'.$prefix.'00001';
            $maxId = Transaksi::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-'.$prefix.'%')
                ->max('trx_id');
                
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-'.$prefix.'00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-'.$prefix, '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-'.$prefix.'0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-'.$prefix.'000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-'.$prefix.'00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-'.$prefix.'0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'.'.$prefix. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * pengechekan mode Registrasi
     * BOOKING atau WALKIN
     */
    public function ModeReg($tglPeriksa) {
        try {
            $data = null;
            $tgl1 = strtotime(date('Y-m-d'));
            $tgl2 = strtotime($tglPeriksa);
            $jarak = $tgl2 - $tgl1;
            $selisihHari = $jarak / 60 / 60 / 24;

            if($selisihHari <= 0) { $data = "WALKIN"; }
            else { $data = "BOOKING"; }
            return $data;
        } 
        catch (\Exception $e) { return null; }
    }

    public function HitungUsia($tglLahir) {
        try {
            $tgl_lahir = $tglLahir;
            $tgl_lahir = explode("-", $tgl_lahir);
            $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
                ? ((date("Y") - $tgl_lahir[0]) - 1)
                : (date("Y") - $tgl_lahir[0]));

            return $usia;
        }
        catch(\Exception $e){
            return null;
        }
    }

    /**
     * digunakan untuk menggenerate No RM
     */
    public function NoRM($clientId, $isPasienLuar) {
        try {
            $rm =  null;
            if($isPasienLuar == true) {
                $maxId = Pasien::withTrashed()->where('client_id', $clientId)
                    ->where("no_rm","ILIKE","PL%")->max('no_rm');
                
                if (!$maxId) { $rm = 'PL000001'; } 
                else {
                    $maxId = str_replace('PL', '', $maxId);
                    $rm = (int)$maxId + 1;
                    while (strlen($rm) < 6) { $rm = '0' . $rm; }
                    $rm = 'PL'.$rm;
                }
                return $rm;
            }

            else {
                $maxId = Pasien::withTrashed()->where('client_id',$clientId)
                    ->where('no_rm', '!~', '[^0-9]')
                    ->max('no_rm');
                if(!$maxId) { $rm = '00000001'; }
                else {
                    if((int)$maxId > 0) {
                        $rm = (int)$maxId + 1;
                        while (strlen($rm) < 8) { $rm = '0'.$rm; }
                    }
                }
                return $rm;
            }
            
            //return $rm;
        } catch (\Exception $e) {
            throw e;
        }
    }

    /**
     * digunakan untuk menggenerate pasien ID
     */
    public function PasienId($clientId) {
        try {
            $id = $clientId.'-PSN' . date('Ym') . '-00001';
            $maxId = Pasien::withTrashed()->where('client_id',$clientId)
                ->where("pasien_id", 'LIKE', $clientId.'-PSN' . date('Ym') . '-%')
                ->max("pasien_id");

            if (!$maxId) {
                $id = $clientId.'-PSN' . date('Ym') . '-00001';
            } else {
                $maxId = str_replace($clientId.'-PSN'.date('Ym').'-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $clientId.'-PSN' . date('Ym') . '-0000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $clientId.'-PSN' . date('Ym') . '-000' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $clientId.'-PSN' . date('Ym') . '-00' . $count;
                } elseif ($count >= 1000 && $count < 10000) {
                    $id = $clientId.'-PSN' . date('Ym') . '-0' . $count;
                } else {
                    $id = $clientId.'-PSN' . date('Ym') . '-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * setiap transaksi ID akan berasal dari masing-masing 
     * table yang berkenaan dengan transaksi
     * Ini untuk menggenerate Trx ID dari Poli
     */
    public function PoliTransactionId($clientId)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-RJ00001';
            $maxId = RawatJalan::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-RJ%')
                ->max('trx_id');
                
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-RJ00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-RJ','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-RJ0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-RJ000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-RJ00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-RJ0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'-RJ'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * setiap transaksi ID akan berasal dari masing-masing 
     * table yang berkenaan dengan transaksi
     * Ini untuk menggenerate Trx ID dari Rawat Inap
     */
    public function InapTransactionId($clientId)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-RI00001';
            $maxId = RawatInap::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-RI%')
                ->max('trx_id');
                
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-RI00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-RI','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-RI0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-RI000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-RI00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-RI0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'-RI'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * setiap transaksi ID akan berasal dari masing-masing 
     * table yang berkenaan dengan transaksi
     * Ini untuk menggenerate Trx ID dari Lab
     */
    public function LabTransactionId($clientId)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-LAB00001';
            $maxId = TrxLab::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-LAB%')
                ->max('trx_id');
                
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-LAB00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-LAB','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-LAB0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-LAB000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-LAB00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-LAB0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'-LAB'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * setiap transaksi ID akan berasal dari masing-masing 
     * table yang berkenaan dengan transaksi
     * Ini untuk menggenerate Trx ID dari Lab
     */
    public function RadTransactionId($clientId)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-RAD00001';
            $maxId = TrxRad::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-RAD%')
                ->max('trx_id');
                
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-RAD00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-RAD','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-RAD0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-RAD000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-RAD00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-RAD0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'-RAD'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * setiap transaksi ID akan berasal dari masing-masing 
     * table yang berkenaan dengan transaksi
     * Ini untuk menggenerate Trx ID dari Lab
     */
    public function OperasiTransactionId($clientId)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-OPR00001';
            $maxId = TrxOperasi::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-OPR%')
                ->max('trx_id');
                
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-OPR00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-OPR','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-OPR0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-OPR000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-OPR00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-OPR0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'-OPR'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    public function PemeriksaanId($clientId)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-PMR00001';
            $maxId = Pemeriksaan::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-PMR%')
                ->max('trx_id');
                
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-PMR00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-PMR','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-PMR0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-PMR000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-PMR00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-PMR0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'-PMR'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    public function ResepTransactionId($clientId)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-RSP00001';
            $maxId = TrxResep::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-RSP%')
                ->max('trx_id');
                
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-RSP00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-RSP','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-RSP0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-RSP000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-RSP00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-RSP0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'-RSP'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    public function ApotekTransactionId($clientId)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-RSP00001';
            $maxId = TrxResep::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-APT%')
                ->max('trx_id');
                
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-APT00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-APT','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-APT0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-APT000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-APT00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-APT0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'-APT'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * setiap transaksi ID akan berasal dari masing-masing 
     * table yang berkenaan dengan transaksi
     * Ini untuk menggenerate Trx ID dari Lab
     */
    public function PaTransactionId($clientId)
    {
        try {
            $id = $clientId.'.'.date('Ym').'-PA00001';
            $maxId = TrxPA::withTrashed()->where('client_id', $clientId)
                ->where('trx_id', 'ILIKE', $clientId.'.'.date('Ym').'-PA%')
                ->max('trx_id');
            
            if (!$maxId) {
                $id = $clientId.'.'.date('Ym').'-PA00001';
            } 
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-PA','',$maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-PA0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-PA000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-PA00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-PA0' . $count; } 
                else { $id = $clientId.'-'.date('Ym').'-PA'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    public static function instance() {
        return new RegistrasiHelper();
    }

}
