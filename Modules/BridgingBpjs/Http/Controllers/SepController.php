<?php

namespace Modules\BridgingBpjs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ManajemenUser\Entities\Client as ClientWs;

use BpjsHelper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use LZCompressor\LZString;

class SepController extends Controller
{
    public $clientId;
    public $credential;
    
    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        
        $this->clientId = $request->header('X-cid');
        /**get data bpjs credential */
        $data = ClientWs::where('client_id',$this->clientId)->where('is_aktif',1)
            ->select('client_id','client_nama','bpjs_cons_id','bpjs_secret','bpjs_user_key','is_bpjs_aktif')
            ->first();
        
        if(!$data) { return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']); }   
        
        $this->credential = [
            'cons_id'       => $data->bpjs_cons_id,
            'secret_key'    => $data->bpjs_secret,
            'user_key'      => $data->bpjs_user_key 
        ];
    }

    public function httpGetRequest($url){
        try {
            $result = BpjsHelper::BpjsHttpGet('https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/'.$url, $this->credential);
            return $result;
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function httpPostRequest($url, $data){
        try {
            $result = BpjsHelper::BpjsHttpPost('https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/'.$url, $this->credential, $data);
            return $result; 
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function httpDeleteRequest($url, $data){
        try {
            $result = BpjsHelper::BpjsHttpDelete('https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/'.$url, $this->credential, $data);
            return $result; 
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data', 'error' => $e->getMessage()]);
        }
    }

    public function cariSep(Request $request, $noSep) {
        try {
            $result = $this->httpGetRequest('SEP/'.$noSep);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data SEP. error: '.$e->getMessage()]);
        }
    }

    public function insertSep(Request $request) {
        try {
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses simpan data SEP. Data pasien tidak ditemukan.']);
            }

            /**
             * contoh insert :
             *  {
             *      "request":{
             *          "t_sep":{
             *              "noKartu":"0001105689835",
             *              "tglSep":"2021-07-30",
             *              "ppkPelayanan":"0301R011",
             *              "jnsPelayanan":"1",
             *              "klsRawat":{
             *                  "klsRawatHak":"2",
             *                  "klsRawatNaik":"1",
             *                  "pembiayaan":"1",
             *                  "penanggungJawab":"Pribadi"
             *              },
             *              "noMR":"MR9835",
             *              "rujukan":{
             *                  "asalRujukan":"2",
             *                  "tglRujukan":"2021-07-23",
             *                  "noRujukan":"RJKMR9835001",
             *                  "ppkRujukan":"0301R011"
             *              },
             *              "catatan":"testinsert RI",
             *              "diagAwal":"E10",
             *              "poli":{
             *                  "tujuan":"",
             *                  "eksekutif":"0"
             *              },
             *              "cob":{
             *                  "cob":"0"
             *              },
             *              "katarak":{
             *                  "katarak":"0"
             *              },
             *              "jaminan":{
             *                  "lakaLantas":"0",
             *                  "noLP":"12345",
             *                  "penjamin":{
                *                  "tglKejadian":"",
                *                  "keterangan":"",
                *                  "suplesi":{
                *                      "suplesi":"0",
                *                      "noSepSuplesi":"",
                *                      "lokasiLaka":{
                *                          "kdPropinsi":"",
                *                          "kdKabupaten":"",
                *                          "kdKecamatan":""
                *                      }
                *                  }
             *                   }
             *              },
             *              "tujuanKunj":"0",
             *              "flagProcedure":"",
             *              "kdPenunjang":"",
             *              "assesmentPel":"",
             *              "skdp":{
             *                  "noSurat":"0301R0110721K000021",
             *                  "kodeDPJP":"31574"
             *              },
             *              "dpjpLayan":"",
             *              "noTelp":"081111111101",
             *              "user":"Coba Ws"
             *          }
             *      }
             *  }
             * 
             */

            /**
             * Format Data :
             * {
             *      "request":{
             *          "t_sep":{
             *              "noKartu":"{nokartu BPJS}",
             *              "tglSep":"{tanggal penerbitan sep format yyyy-mm-dd}",
             *              "ppkPelayanan":"{kode faskes pemberi pelayanan}",
             *              "jnsPelayanan":"{jenis pelayanan = 1. r.inap 2. r.jalan}",
             *              "klsRawat":{
             *                  "klsRawatHak":"{sesuai kelas rawat peserta, 1. Kelas 1, 2. Kelas 2, 3. Kelas 3}",
             *                  "klsRawatNaik":"{diisi jika naik kelas rawat, 1. VVIP, 2. VIP, 3. Kelas 1, 4. Kelas 2, 5. Kelas 3, 6. ICCU, 7. ICU, 8. Diatas Kelas 1}",
             *                  "pembiayaan":"{1. Pribadi, 2. Pemberi Kerja, 3. Asuransi Kesehatan Tambahan. diisi jika naik kelas rawat}",
             *                  "penanggungJawab":"{Contoh: jika pembiayaan 1 maka penanggungJawab=Pribadi. diisi jika naik kelas rawat}"
             *              },
             *              "noMR":"{nomor medical record RS}",
             *              "rujukan":{
             *                  "asalRujukan":"{asal rujukan ->1.Faskes 1, 2. Faskes 2(RS)}",
             *                  "tglRujukan":"{tanggal rujukan format: yyyy-mm-dd}",
             *                  "noRujukan":"{nomor rujukan}",
             *                  "ppkRujukan":"{kode faskes rujukam -> baca di referensi faskes}"
             *              },
             *              "catatan":"{catatan peserta}",
             *              "diagAwal":"{diagnosa awal ICD10 -> baca di referensi diagnosa}",
             *              "poli":{
             *                  "tujuan":"{kode poli -> baca di referensi poli}",
             *                  "eksekutif":"{poli eksekutif -> 0. Tidak 1.Ya}""
             *              },
             *              "cob":{
             *                  "cob":"{cob -> 0.Tidak 1. Ya}"
             *              },
             *              "katarak":{
             *                  "katarak":"{katarak --> 0.Tidak 1.Ya}"
             *              },
             *              "jaminan":{
             *                  "lakaLantas":" 0 : Bukan Kecelakaan lalu lintas [BKLL], 1 : KLL dan bukan kecelakaan Kerja [BKK], 2 : KLL dan KK, 3 : KK",
             *                  "noLP":"{No. LP}",
             *                  "penjamin":{
             *                  "tglKejadian":"{tanggal kejadian KLL format: yyyy-mm-dd}",
             *                  "keterangan":"{Keterangan Kejadian KLL}",
             *                  "suplesi":{
             *                      "suplesi":"{Suplesi --> 0.Tidak 1. Ya}",
             *                      "noSepSuplesi":"{No.SEP yang Jika Terdapat Suplesi}",
             *                      "lokasiLaka":{
             *                          "kdPropinsi":"{Kode Propinsi}",
             *                          "kdKabupaten":"{Kode Kabupaten}",
             *                          "kdKecamatan":"{Kode Kecamatan}"
             *                      }
             *                  }
             *                  }
             *              },
             *              "tujuanKunj":{"0": Normal, 
             *                          "1": Prosedur, 
             *                          "2": Konsul Dokter},
             *              "flagProcedure":{"0": Prosedur Tidak Berkelanjutan, 
             *                              "1": Prosedur dan Terapi Berkelanjutan} ==> diisi "" jika tujuanKunj = "0",
             *              "kdPenunjang":{"1": Radioterapi, 
             *                              "2": Kemoterapi, 
             *                              "3": Rehabilitasi Medik, 
             *                              "4": Rehabilitasi Psikososial, 
             *                              "5": Transfusi Darah, 
             *                              "6": Pelayanan Gigi, 
             *                              "7": Laboratorium, 
             *                               "8": USG, 
             *                               "9": Farmasi, 
             *                               "10": Lain-Lain, 
             *                               "11": MRI, 
             *                               "12": HEMODIALISA} ==> diisi "" jika tujuanKunj = "0",
             *               "assesmentPel":{"1": Poli spesialis tidak tersedia pada hari sebelumnya, 
             *                               "2": Jam Poli telah berakhir pada hari sebelumnya, 
             *                               "3": Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya, 
             *                               "4": Atas Instruksi RS} ==> diisi jika tujuanKunj = "2" atau "0" (politujuan beda dengan poli rujukan dan hari beda),
             *                               "5": Tujuan Kontrol,
             *               "skdp":{
             *                   "noSurat":"{Nomor Surat Kontrol}",
             *                   "kodeDPJP":"{kode dokter DPJP --> baca di referensi dokter DPJP}"
             *               },
             *               "dpjpLayan":"000002", (tidak diisi jika jnsPelayanan = "1" (RANAP),
             *               "noTelp":"{nomor telepon}",
             *               "user":"{user pembuat SEP}"
             *           }
             *       }
             *       }
             * 
             **/
            
                        
            //coding    
            $data = [
                'request' => [
                    't_sep' => [
                        'noKartu' => $request->no_kepesertaan,
                        'tglSep' => $request->tgl_sep,
                        'ppkPelayanan' => '0301R011',
                        'jnsPelayanan' => $request->jns_pelayanan,
                        'klsRawat' => [
                            'klsRawatHak' => $request->kls_rawat_hak,
                            'klsRawatNaik' => $request->kls_rawat_naik,
                            'pembiayaan' => $request->pembiayaan,
                            'penanggungJawab' => $request->penanggung_jawab
                        ],
                        'noMR' => $request->no_rm,
                        'rujukan' => [
                            'asalRujukan' => $request->asal_rujukan,
                            'tglRujukan' => $request->tgl_rujukan,
                            'noRujukan' => $request->no_rujukan,
                            'ppkRujukan' => $request->ppk_rujukan
                        ],
                        'catatan' => $request->catatan,
                        'diagAwal' => $request->diag_awal,
                        'poli' => [
                            'tujuan' => $request->unit_nama,
                            'eksekutif' => $request->is_eksekutif
                        ],
                        'cob' => [
                            'cob' => $request->cob
                        ],
                        'katarak' => [
                            'katarak' => $request->katarak
                        ],
                        'jaminan' => [
                            'lakaLantas' => $request->lakalantas->is_lakalantas,
                            'noLP' => $request->lakalantas->noLP,
                            'penjamin' => [
                                'tglKejadian' => $request->lakalantas->tgl_kejadian,
                                'keterangan' => $request->lakalantas->keterangan,
                                'suplesi' => [
                                    'suplesi' => $request->lakalantas->suplesi,
                                    'noSepSuplesi' => $request->lakalantas->no_suplesi,
                                    'lokasiLaka' => [
                                        'kdPropinsi' => $request->lakalantas->kode_propinsi,
                                        'kdKabupaten' => $request->lakalantas->kode_kabupaten,
                                        'kdKecamatan' => $request->lakalantas->kode_kecamatan,
                                    ]                                    
                                ]
                            ]
                        ],
                        'tujuanKunj' => $request->tujuan_kunjungan,
                        'flagProcedure' => $request->flag_prosedur,
                        'kdPenunjang' => $request->kode_penunjang,
                        'assesmentPel' => $request->assesment_pel,
                        'skdp' => [
                            'noSurat' => $request->no_surat,
                            'kodeDPJP' => $request->dokter_dpjp_id,
                        ],
                        'dpjpLayan' => $request->dpjp_layanan,
                        'noTelp' => $request->no_telepon,
                        'user' => Auth::user()->username
                    ]
                ]
            ];
            
             $result = $this->httpPostRequest('SEP/2.0/insert', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data SEP. error: '.$e->getMessage()]);
        }
    }

    public function updateSep(Request $request) {
        try {
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses simpan data SEP. Data pasien tidak ditemukan.']);
            }

            /**
             * contoh update :
             *  {
             *      "request":{
             *          "t_sep":{
             *              "noSep":"0001105689835",
             *              "klsRawat":{
             *                  "klsRawatHak":"2",
             *                  "klsRawatNaik":"1",
             *                  "pembiayaan":"1",
             *                  "penanggungJawab":"Pribadi"
             *              },
             *              "noMR":"MR9835",
             *              "catatan":"testinsert RI",
             *              "diagAwal":"E10",
             *              "poli":{
             *                  "tujuan":"",
             *                  "eksekutif":"0"
             *              },
             *              "cob":{
             *                  "cob":"0"
             *              },
             *              "katarak":{
             *                  "katarak":"0"
             *              },
             *              "jaminan":{
             *                  "lakaLantas":"0",
             *                  "noLP":"12345",
             *                  "penjamin":{
                *                  "tglKejadian":"",
                *                  "keterangan":"",
                *                  "suplesi":{
                *                      "suplesi":"0",
                *                      "noSepSuplesi":"",
                *                      "lokasiLaka":{
                *                          "kdPropinsi":"",
                *                          "kdKabupaten":"",
                *                          "kdKecamatan":""
                *                      }
                *                  }
             *                   }
             *              },
             *              "dpjpLayan":"",
             *              "noTelp":"081111111101",
             *              "user":"Coba Ws"
             *          }
             *      }
             *  }
             * 
             */

            /**
             * Format Data :
             * {
             *      "request":{
             *          "t_sep":{
             *              "noSep":"{no SEP}",
             *              "klsRawat":{
             *                  "klsRawatHak":"{sesuai kelas rawat peserta, 1. Kelas 1, 2. Kelas 2, 3. Kelas 3}",
             *                  "klsRawatNaik":"{diisi jika naik kelas rawat, 1. VVIP, 2. VIP, 3. Kelas 1, 4. Kelas 2, 5. Kelas 3, 6. ICCU, 7. ICU, 8. Diatas Kelas 1}",
             *                  "pembiayaan":"{1. Pribadi, 2. Pemberi Kerja, 3. Asuransi Kesehatan Tambahan. diisi jika naik kelas rawat}",
             *                  "penanggungJawab":"{Contoh: jika pembiayaan 1 maka penanggungJawab=Pribadi. diisi jika naik kelas rawat}"
             *              },
             *              "noMR":"{nomor medical record RS}",
             *              "catatan":"{catatan peserta}",
             *              "diagAwal":"{diagnosa awal ICD10 -> baca di referensi diagnosa}",
             *              "poli":{
             *                  "tujuan":"{kode poli -> baca di referensi poli}",
             *                  "eksekutif":"{poli eksekutif -> 0. Tidak 1.Ya}""
             *              },
             *              "cob":{
             *                  "cob":"{cob -> 0.Tidak 1. Ya}"
             *              },
             *              "katarak":{
             *                  "katarak":"{katarak --> 0.Tidak 1.Ya}"
             *              },
             *              "jaminan":{
             *                  "lakaLantas":" 0 : Bukan Kecelakaan lalu lintas [BKLL], 1 : KLL dan bukan kecelakaan Kerja [BKK], 2 : KLL dan KK, 3 : KK",
             *                  "noLP":"{No. LP}",
             *                  "penjamin":{
             *                  "tglKejadian":"{tanggal kejadian KLL format: yyyy-mm-dd}",
             *                  "keterangan":"{Keterangan Kejadian KLL}",
             *                  "suplesi":{
             *                      "suplesi":"{Suplesi --> 0.Tidak 1. Ya}",
             *                      "noSepSuplesi":"{No.SEP yang Jika Terdapat Suplesi}",
             *                      "lokasiLaka":{
             *                          "kdPropinsi":"{Kode Propinsi}",
             *                          "kdKabupaten":"{Kode Kabupaten}",
             *                          "kdKecamatan":"{Kode Kecamatan}"
             *                      }
             *                  }
             *                  }
             *              },
             *               "dpjpLayan":"000002", (tidak diisi jika jnsPelayanan = "1" (RANAP),
             *               "noTelp":"{nomor telepon}",
             *               "user":"{user pembuat SEP}"
             *           }
             *       }
             *       }
             * 
             **/
            
                        
            //coding    
            $data = [
                'request' => [
                    't_sep' => [
                        'noSep' => $request->no_sep,
                        'klsRawat' => [
                            'klsRawatHak' => $request->kls_rawat_hak,
                            'klsRawatNaik' => $request->kls_rawat_naik,
                            'pembiayaan' => $request->pembiayaan,
                            'penanggungJawab' => $request->penanggung_jawab
                        ],
                        'noMR' => $request->no_rm,
                        'catatan' => $request->catatan,
                        'diagAwal' => $request->diag_awal,
                        'poli' => [
                            'tujuan' => $request->unit_nama,
                            'eksekutif' => $request->is_eksekutif
                        ],
                        'cob' => [
                            'cob' => $request->cob
                        ],
                        'katarak' => [
                            'katarak' => $request->katarak
                        ],
                        'jaminan' => [
                            'lakaLantas' => $request->lakalantas->is_lakalantas,
                            'noLP' => $request->lakalantas->noLP,
                            'penjamin' => [
                                'tglKejadian' => $request->lakalantas->tgl_kejadian,
                                'keterangan' => $request->lakalantas->keterangan,
                                'suplesi' => [
                                    'suplesi' => $request->lakalantas->suplesi,
                                    'noSepSuplesi' => $request->lakalantas->no_suplesi,
                                    'lokasiLaka' => [
                                        'kdPropinsi' => $request->lakalantas->kode_propinsi,
                                        'kdKabupaten' => $request->lakalantas->kode_kabupaten,
                                        'kdKecamatan' => $request->lakalantas->kode_kecamatan,
                                    ]                                    
                                ]
                            ]
                        ],
                        'dpjpLayan' => $request->dpjp_layanan,
                        'noTelp' => $request->no_telepon,
                        'user' => Auth::user()->username
                    ]
                ]
            ];
            
             $result = $this->httpPutRequest('SEP/2.0/update', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data SEP. error: '.$e->getMessage()]);
        }
    }

    public function deleteSep(Request $request, $noSep) {
        try {
            $data = [
                'request' => [
                    't_sep' => [
                        'no_sep' => $noSep,
                        'user' => Auth::user()->username,
                    ]
                ]
            ];
            $result = $this->httpDeleteRequest('SEP/2.0/delete', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Ada kesalahan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data SEP. error: '.$e->getMessage()]);
        }
    }

    public function suplesiJasaRaharja(Request $request) {
        try {
            $noKepesertaan = $request->no_kepesertaan;
            $tglPelayanan = $request->tanggal;
            
            $result = $this->httpGetRequest('sep/JasaRaharja/Suplesi/'.$noKepesertaan.'/tglPelayanan/'.$tglPelayanan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. error: '.$e->getMessage()]);
        }
    }

    public function dataIndukKecelakaan(Request $request) {
        try {
            $noKepesertaan = $request->no_kepesertaan;
            
            $result = $this->httpGetRequest('sep/KllInduk/List/'.$noKepesertaan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. error: '.$e->getMessage()]);
        }
    }

    public function pengajuanSep(Request $request) {
        try {
            /** 
             * Contoh data :
             *  {
             *      "request": {
             *          "t_sep": {
             *              "noKartu": "0001300759569",
             *              "tglSep": "2021-03-26",
             *              "jnsPelayanan": "1",
             *              "jnsPengajuan": "2",
             *              "keterangan": "Hari libur",
             *              "user": "Coba Ws"
             *          }
             *      }
             *  }
             * 
             */

            /**
             * Format data :
             *  {
             *      "request": {
             *          "t_sep": {
             *              "noKartu": "{nomor kartu BPJS}",
             *              "tglSep": "{tanggal penerbitan sep format yyyy-mm-dd}",
             *              "jnsPelayanan": "{}jenis pelayanan (1.R.Inap 2.R.Jalan)}",
             *              "jnsPengajuan": "{}jenis pengajuan (1. pengajuan backdate, 2. pengajuan finger print)}"
             *              "keterangan": "{keterangan}",
             *              "user": "{user pemakai}"
             *          }
             *      }
             *  } 
             * 
            */

            $data = [
                'request' => [
                    't_sep' => [
                        'noKartu' => $request->no_kepesertaan,
                        'tglSep' => $request->tgl_sep,
                        'jnsPelayanan' => $request->jenis_pelayanan,
                        'jnsPengajuan' => $request->jenis_pengajuan,
                        'keterangan' => $request->keterangan,
                        'user' => Auth::user()->username,
                    ]
                ]
            ];
            $result = $this->httpPostRequest('Sep/pengajuanSEP', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Ada kesalahan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data SEP. error: '.$e->getMessage()]);
        }
    }

    public function approvalSep(Request $request) {
        try {
            /** 
             * Contoh data :
             * Tanpa parameter jnsPengajuan maka default jnsPengajuan="1" (Approval SEP Backdate) 

             *   {
             *       "request": {
             *           "t_sep": {
             *               "noKartu": "0003814312013",
             *               "tglSep": "2017-10-26",
             *               "jnsPelayanan": "1",
             *               "keterangan": "Hari libur",
             *               "user": "Coba Ws"
             *           }
             *       }
             *   }		
             * 
             *  Jika parameter jnsPengajuan ada nilai, maka approval sesuai jnsPengajuan  
             *  {
             *      "request": {
             *          "t_sep": {
             *              "noKartu": "0003814312013",
             *              "tglSep": "2017-10-26",
             *              "jnsPelayanan": "1",
             *              "jnsPengajuan": "1",
             *              "keterangan": "Hari libur",
             *              "user": "Coba Ws"
             *          }
             *      }
             *  }
             * 
             */

            /**
             * Format data :
             *  {
             *      "request": {
             *          "t_sep": {
             *              "noKartu": "{nomor kartu BPJS}",
             *              "tglSep": "{tanggal penerbitan sep format yyyy-mm-dd}",
             *              "jnsPelayanan": "{}jenis pelayanan (1.R.Inap 2.R.Jalan)}",
             *              "jnsPengajuan": "{}jenis pengajuan (1. pengajuan backdate, 2. pengajuan finger print)}",
             *              "keterangan": "{keterangan}",
             *              "user": "{user pemakai}"
             *           }
             *      }
             *  }
             * 
            */

            $data = [
                'request' => [
                    't_sep' => [
                        'noKartu' => $request->no_kepesertaan,
                        'tglSep' => $request->tgl_sep,
                        'jnsPelayanan' => $request->jenis_pelayanan,
                        'jnsPengajuan' => $request->jenis_pengajuan,
                        'keterangan' => $request->keterangan,
                        'user' => Auth::user()->username,
                    ]
                ]
            ];
            $result = $this->httpPostRequest('Sep/approvalSEP', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Ada kesalahan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data SEP. error: '.$e->getMessage()]);
        }
    }

    public function listDataPersetujuanSep(Request $request) {
        try {
            $bulan = $request->bulan; //(1-12)
            $tahun = $request->tahun;
            
            $result = $this->httpGetRequest('Sep/persetujuanSEP/list/bulan/'.$bulan.'/tahun/'.$tahun);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. error: '.$e->getMessage()]);
        }
    }

    public function updateTanggalPulang(Request $request) {
        try {
            /** 
             * Contoh data :
             *  {
             *      "request":{
             *          "t_sep":{
             *              "noSep": "0301R0110121V000829",
             *              "statusPulang":"4",
             *              "noSuratMeninggal":"325/K/KMT/X/2021",
             *              "tglMeninggal":"2021-02-10",
             *              "tglPulang":"2021-02-14",
             *              "noLPManual":"",
             *              "user":"coba"
             *          }
             *      }
             *  }
             * 
             */

            /**
             * Format data :
             *  {
             *      "request":{
             *          "t_sep":{
             *              "noSep": "{nosep}",
             *              "statusPulang":"{1:Atas Persetujuan Dokter, 3:Atas Permintaan Sendiri, 4:Meninggal, 5:Lain-lain}",
             *              "noSuratMeninggal":"{diisi jika statusPulang 4, selain itu kosong}",
             *              "tglMeninggal":"{diisi jika statusPulang 4, selain itu kosong. format yyyy-MM-dd}",
             *              "tglPulang":"{format yyyy-MM-dd}",
             *              "noLPManual":"{diisi jika SEPnya adalah KLL}",
             *              "user":"{user}"
             *          }
             *      }
             *  }
             * 
            */

            $data = [
                'request' => [
                    't_sep' => [
                        'noSep' => $request->no_sep,
                        'statusPulang' => $request->status_pulang,
                        'noSuratMeninggal' => $request->no_surat_meninggal,
                        'tglMeninggal' => $request->tgl_meninggal,
                        'tglPulang' => $request->tgl_pulang,
                        'noLPManual' => $request->no_lp_manual,
                        'user' => Auth::user()->username,
                    ]
                ]
            ];
            $result = $this->httpPostRequest('SEP/2.0/updtglplg', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Ada kesalahan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah tanggal pulang. error: '.$e->getMessage()]);
        }
    }

    public function listDataUpdateTanggalPulang(Request $request) {
        try {
            $bulan = $request->bulan; //(1-12)
            $tahun = $request->tahun;
            $pencarian = $request->pencarian;
            
            
            $result = $this->httpGetRequest('Sep/updtglplg/list/bulan/'.$bulan.'/tahun/'.$tahun.'/'.$pencarian);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. error: '.$e->getMessage()]);
        }
    }

    public function integrasiSepDenganInacbg(Request $request,$noSep) {
        try {
            $result = $this->httpGetRequest('sep/cbg/'.$noSep);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->pesertasep]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. error: '.$e->getMessage()]);
        }
    }

    public function dataSepInternal(Request $request,$noSep) {
        try {
            $result = $this->httpGetRequest('SEP/internal/'.$noSep);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. error: '.$e->getMessage()]);
        }
    }

    public function deleteSepInternal(Request $request) {
        try {
            $data = [
                'request' => [
                    't_sep' => [
                        'noSep' => $request->no_sep,
                        'noSurat' => $request->no_surat,
                        'tglRujukanInternal' => $request->tgl_rujukan_internal,
                        'kdPoliTuj' => $request->kode_poli,
                        'user' => Auth::user()->username,
                    ]
                ]
            ];
            $result = $this->httpDeleteRequest('SEP/Internal/delete', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Ada kesalahan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data SEP. error: '.$e->getMessage()]);
        }
    }

    public function getFingerPrint(Request $request) {
        try {
            $noPeserta = $request->no_kepesertaan;
            $tglPelayanan = $request->tgl_pelayanan;
            
            $result = $this->httpGetRequest('SEP/FingerPrint/Peserta/'.$noPeserta.'/TglPelayanan/'.$tglPelayanan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. error: '.$e->getMessage()]);
        }
    }

    public function getListFingerPrint(Request $request) {
        try {
            $tglPelayanan = $request->tgl_pelayanan;
            
            $result = $this->httpGetRequest('SEP/FingerPrint/List/Peserta/TglPelayanan/'.$tglPelayanan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. error: '.$e->getMessage()]);
        }
    }
    
}
