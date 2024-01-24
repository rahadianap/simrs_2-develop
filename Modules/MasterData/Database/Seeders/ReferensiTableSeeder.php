<?php

namespace Modules\MasterData\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\MasterData\Entities\Referensi;
use DB;

class ReferensiTableSeeder extends Seeder
{
    protected $clientId = 'CL2022-0002';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Model::unguard();
        // $this->call("OthersTableSeeder");
        DB::connection('dbclient')->table('tb_referensi')->insert([  
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'GOL_DARAH','ref_nama'=>'Golongan Darah','deskripsi'=>'Referensi data untuk golongan darah','json_data'=>'["A","B","AB","O","-"]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'RHESUS','ref_nama'=>'Rhesus Darah','deskripsi'=>'Referensi data rhesus darah','json_data'=>'[{"value":"Positif", "text":"Positif(+)"},{"value":"Negatif", "text":"Negatif(-)"},{"value":"Tidak Tahu", "text":"Tidak Tahu"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'SALUT','ref_nama'=>'Salut (Salutation)','deskripsi'=>'Referensi data salut (salutation)','json_data'=>'[{"value":"Ny.", "text":"Nyonya"},{"value":"Tn.", "text":"Tuan"},{"value":"Nn.", "text":"Nona"},{"value":"An.", "text":"Anak"},{"value":"By.", "text":"Bayi"},{"value":"By.Ny.", "text":"Bayi Nyonya"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'PENDIDIKAN','ref_nama'=>'Pendidikan','deskripsi'=>'Referensi data pilihan pendidikan','json_data'=>'[{"value":"KB", "text":"PAUD/KB/Sederajat"},{"value":"TK", "text":"TK atau sederajat"},{"value":"SD", "text":"SD/MI atau sederajat"},{"value":"SMP", "text":"SMP/MTs atau sederajat"},{"value":"SMA", "text":"SMA/SMK/MA atau sederajat"},{"value":"D1", "text":"Diploma satu atau sederajat"},{"value":"D2", "text":"Diploma dua atau sederajat"},{"value":"D3", "text":"Diploma tiga atau sederajat"},{"value":"S1", "text":"S1/Sarjana/Sederajat"},{"value":"S2", "text":"S2/Magister/Sederajat"},{"value":"S3", "text":"S3/Doktor/Sederajat"},{"value":"BELUM", "text":"Belum Sekolah"},{"value":"TIDAK", "text":"Tidak Sekolah"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'PEKERJAAN','ref_nama'=>'Pekerjaan','deskripsi'=>'Referensi data pilihan pekerjaan','json_data'=>'[{"value":"IRT", "text":"Ibu Rumah Tangga"},{"value":"SWASTA", "text":"Karyawan Swasta"},{"value":"PNS", "text":"Pegawai Negeri Sipil"},{"value":"WIRASWASTA", "text":"Wiraswasta atau Pengusaha"},{"value":"PEDAGANG", "text":"Pedagang"},{"value":"PETANI", "text":"Petani atau pemilik usaha agrikultur"},{"value":"PENSIUNAN", "text":"Pensiunan PNS/POLRI/TNI"},{"value":"POLRI/TNI", "text":"Anggota kesatuan aktif POLRI atau TNI"},{"value":"GURU", "text":"Guru atau Pengajar"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'AGAMA','ref_nama'=>'Agama dan Keyakinan','deskripsi'=>'Referensi data pilihan agama dan keyakinan','json_data'=>'[{"value":"ISLAM", "text":"Islam"},{"value":"KRISTEN", "text":"Kristen(Protestan)"},{"value":"KATOLIK", "text":"Katolik"},{"value":"HINDU", "text":"Hindu"},{"value":"BUDHA", "text":"Budha"},{"value":"KHONGHUCU", "text":"Khonghucu"},{"value":"PENGHAYAT", "text":"Penghayat dan kepercayaan lokal"},{"value":"LAIN_LAIN", "text":"Lain-lain"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'STATUS_KAWIN','ref_nama'=>'Status Perkawinan','deskripsi'=>'Referensi data pilihan status_kawin dan keyakinan','json_data'=>'[{"value":"BELUM", "text":"Belum Kawin"},{"value":"KAWIN", "text":"Kawin tercatat/tidak tercatat"},{"value":"CERAI HIDUP", "text":"Janda/duda cerai hidup"},{"value":"CERAI MATI", "text":"Janda/duda cerai mati"},{"value":"LAIN_LAIN", "text":"Lain-lain"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'JENIS_PENJAMIN','ref_nama'=>'Jenis Penjamin Asuransi','deskripsi'=>'Referensi data pilihan jenis penjamin','json_data'=>'[{"value":"BPJS", "text":"BPJS dan turunannya","is_bpjs":"true"},{"value":"PRIBADI", "text":"Umum/Pribadi","is_bpjs":"0"},{"value":"PERUSAHAAN", "text":"Instansi / Perusahaan","is_bpjs":"0"},{"value":"ASURANSI", "text":"Asuransi (Non BPJS)","is_bpjs":"0"},{"value":"LAIN_LAIN", "text":"Lain-lain","is_bpjs":"0"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'HUB_KELUARGA','ref_nama'=>'Hubungan kekerabatan pasien','deskripsi'=>'Referensi data pilihan hubungan kekerabatan (keluarga) pasien','json_data'=>'[{"value":"DIRI SENDIRI", "text":"Diri Sendiri"},{"value":"AYAH", "text":"Ayah"},{"value":"IBU", "text":"Ibu"},{"value":"ANAK", "text":"Anak"},{"value":"PASANGAN", "text":"Suami / Istri"},{"value":"SAUDARA", "text":"Kerabat / Saudara"},{"value":"LAIN_LAIN", "text":"Lain-lain"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'SUKU_BANGSA','ref_nama'=>'Suku Bangsa','deskripsi'=>'Referensi data pilihan suku bangsa','json_data'=>'[{"value":"JAWA", "text":"Jawa"},{"value":"SUNDA", "text":"Sunda"},{"value":"SUNDA", "text":"Sunda"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'KEBANGSAAN','ref_nama'=>'Kebangsaan (Kewarganegaraan)','deskripsi'=>'Referensi data pilihan kebangsaan (kewarganegaraan)','json_data'=>'[{"value":"INDONESIA", "text":"Indonesia"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'JNS_REGISTRASI','ref_nama'=>'Jenis Pelayanan','deskripsi'=>'Referensi jenis pelayanan','json_data'=>'[{"value":"RJ", "text":"RAWAT JALAN"},{"value":"IGD", "text":"GAWAT DARURAT"},{"value":"RI", "text":"RAWAT INAP"},{"value":"BDS", "text":"BEDAH"},{"value":"LAB", "text":"LABORATORIUM"},{"value":"RAD", "text":"RADIOLOGI"},{"value":"PNJ", "text":"PENUNJANG"},{"value":"LAIN", "text":"LAIN_LAIN"}]','is_aktif'=>'1','allow_user_edit'=>'0','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'CARA_REGISTRASI','ref_nama'=>'Cara Registrasi','deskripsi'=>'Referensi cara_registrasi','json_data'=>'[{"value":"OL", "text":"ONLINE"},{"value":"LG", "text":"LANGSUNG"},{"value":"TL", "text":"TELEPON"}]','is_aktif'=>'1','allow_user_edit'=>'0','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'JNS_PELAYANAN','ref_nama'=>'Jenis Pelayanan','deskripsi'=>'Referensi jenis pelayanan','json_data'=>'[{"value":"RJ", "text":"RAWAT JALAN"},{"value":"IGD", "text":"GAWAT DARURAT"},{"value":"RAWAT_INAP", "text":"RAWAT INAP"},{"value":"BEDAH", "text":"BEDAH"},{"value":"PENUNJANG", "text":"PENUNJANG"},{"value":"PNJ", "text":"PENUNJANG"},{"value":"LAIN", "text":"LAIN_LAIN"}]','is_aktif'=>'1','allow_user_edit'=>'0','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'STATUS_BED_INAP','ref_nama'=>'Status Bed Inap','deskripsi'=>'Referensi data pilihan status ketersediaan bed inap','json_data'=>'[{"value":"TERISI", "text":"Sedang digunakan","is_tersedia":"false"},{"value":"KOSONG", "text":"Tidak ada pasien","is_tersedia":"true"},{"value":"PERBAIKAN", "text":"Sedang dalam perbaikan","is_tersedia":"false"},{"value":"STERILISASI", "text":"Sedang proses sterilisasi","is_tersedia":"false"},{"value":"DIPESAN", "text":"Dipesan untuk pasien berikutnya","is_tersedia":"false"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'STATUS_PULANG','ref_nama'=>'Status Pulang','deskripsi'=>'Referensi data pilihan status pulang pasien','json_data'=>'[{"value":"HIDUP", "text":"Pasien hidup","is_meninggal":"false"},{"value":"MENINGGAL < 48 JAM", "text":"Pasien meninggal <= 48 Jam","is_meninggal":"true"},{"value":"MENINGGAL >= 48 JAM", "text":"Pasien meninggal lebih dari 48 Jam","is_meninggal":"true"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'JNS_PENGUNJUNG','ref_nama'=>'Jenis Pengunjung','deskripsi'=>'Referensi data pilihan jenis pengunjung','json_data'=>'[{"value":"BARU", "text":"Pasien Baru"},{"value":"LAMA", "text":"Pasien Lama"}]','is_aktif'=>'1','allow_user_edit'=>'0','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'CARA_PULANG','ref_nama'=>'Cara Pulang Pasien','deskripsi'=>'Referensi data pilihan cara pulang pasien','json_data'=>'[{"value":"ATAS PERSETUJUAN", "text":"Atas persetujuan dokter"},{"value":"PERMINTAAN SENDIRI", "text":"Atas permintaan pasien/keluarga"},{"value":"RUJUK RS LAIN", "text":"Dirujuk ke Rumah Sakit Lain"},{"value":"KEMBALI ASAL", "text":"Dikembalikan ke Asal Rujukan"},{"value":"RAWAT INAP", "text":"Dirujuk ke Rawat Inap"},{"value":"LAIN-LAIN", "text":"Lain-lain"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'ASAL_PASIEN','ref_nama'=>'Asal Rujukan Pasien','deskripsi'=>'Referensi data pilihan asal rujukan pasien','json_data'=>'[{"value":"DATANG SENDIRI", "text":"Atas kemauan sendiri","is_internal":"false"},{"value":"PUSKESMAS", "text":"Rujukan dari puskesmas","is_internal":"false"},{"value":"FASKES LAIN", "text":"Rujukan dari faskes lain","is_internal":"false"},{"value":"RS LAIN", "text":"Rujukan RS Lain","is_internal":"false"},{"value":"BIDAN", "text":"Rujukan dari bidan mitra RS","is_internal":"false"},{"value":"RUJUK KEMBALI", "text":"Terima kembali pasien yang dirujuk","is_internal":"false"},{"value":"RAWAT JALAN", "text":"Rujukan dari Rawat Jalan","is_internal":"true"},{"value":"IGD", "text":"Rujukan dari IGD","is_internal":"true"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'JENIS_ALERGI','ref_nama'=>'Jenis Alergi','deskripsi'=>'Referensi data pilihan jenis alergi','json_data'=>'[{"value":"OBAT", "text":"Alergi Obat-obatan"},{"value":"MAKANAN", "text":"Alergi terhadap makanan"},{"value":"BINATANG", "text":"Alergi terhadap binatang"},{"value":"UDARA", "text":"Alergi terhadap kondisi udara"},{"value":"LAIN-LAIN", "text":"Alergi yang lain"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PASIEN','ref_id'=>'JENIS_BAYAR','ref_nama'=>'Jenis Bayar','deskripsi'=>'Referensi data pilihan jenis bayar transaksi','json_data'=>'[{"value":"TUNAI", "text":"Tunai"},{"value":"DEBIT", "text":"Debit"},{"value":"KREDIT", "text":"Kredit"},{"value":"ASURANSI", "text":"Asuransi"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],

            ['client_id'=>$this->clientId,'ref_group'=>'PERSEDIAAN','ref_id'=>'STATUS_VENDOR','ref_nama'=>'Status vendor / pemasok','deskripsi'=>'Referensi data pilihan status  vendor / pemasok','json_data'=>'[{"value":"APPROVED", "text":"Disetujui"},{"value":"UNAPPROVED", "text":"Tidak Disetujui"},{"value":"-", "text":""}]','is_aktif'=>'1','allow_user_edit'=>'0','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PERSEDIAAN','ref_id'=>'KLASIFIKASI_PRODUK','ref_nama'=>'Klasifikasi Produk','deskripsi'=>'Referensi data pilihan klasifikasi produk','json_data'=>'[{"value":"HERBAL","desc":"Obat herbal (tradisional)","jenis_produk":"MEDIS","aktif":true},{"value":"OBAT BEBAS","desc":"Obat tanpa resep dokter","jenis_produk":"MEDIS","aktif":true},{"value":"OBAT KERAS","desc":"Obat harus dengan resep dokter","jenis_produk":"MEDIS","aktif":true},{"value":"NARKOTIKA","desc":"Obat masuk golongan narkotika","jenis_produk":"MEDIS","aktif":true},{"value":"PSIKOTROPIKA","desc":"Obat dengan efek ke syaraf","jenis_produk":"MEDIS","aktif":true},{"value":"ATK","desc":"alat tulis fantor","jenis_produk":"UMUM","aktif":true},{"value":"CETAKAN","desc":"form dan kertas cetakan","jenis_produk":"UMUM","aktif":true}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PERSEDIAAN','ref_id'=>'GOLONGAN_PRODUK','ref_nama'=>'Golongan Produk','deskripsi'=>'Referensi data pilihan golongan produk','json_data'=>'[{"value":"-","desc":"contoh data","jenis_produk":"MEDIS","aktif":false}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PERSEDIAAN','ref_id'=>'LABEL_OBAT','ref_nama'=>'Label item obat','deskripsi'=>'Pilihan pelabelan etiket cara pakai item medis (obat)','json_data'=>'[{"value":"KOCOK DAHULU", "text":"KOCOK DAHULU"},{"value":"DIMINUM 1 JAM SEBELUM MAKAN", "text":"DIMINUM 1 JAM SEBELUM MAKAN"},{"value":"DIMINUM SESUDAH MAKAN", "text":"DIMINUM SESUDAH MAKAN"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PERSEDIAAN','ref_id'=>'JENIS_PRODUK','ref_nama'=>'Jenis Produk','deskripsi'=>'Pilihan jenis produk - Medis, umum, jasa, dan dapur','json_data'=>'[{"value":"MEDIS", "text":"MEDIS"},{"value":"UMUM", "text":"UMUM (NON MEDIS)"},{"value":"JASA", "text":"JASA(PIHAK KE-3)"},{"value":"DAPUR", "text":"DAPUR"}]','is_aktif'=>'1','allow_user_edit'=>'0','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PERSEDIAAN','ref_id'=>'SATUAN_PRODUK','ref_nama'=>'Unit satuan persediaan','deskripsi'=>'Referensi satuan dan kemasan item persediaan','json_data'=>'[{"value":"Box", "text":"Box (kotak)"},{"value":"Kantong", "text":"Kantong"},{"value":"Pcs", "text":"Pieces (Biji)"},{"value":"Kg", "text":"Kilogram"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PERSEDIAAN','ref_id'=>'SEDIAAN_OBAT','ref_nama'=>'Bentuk sediaan dari item medis','deskripsi'=>'Referensi bentuk sediaan item medis','json_data'=>'[{"value":"Kapsul (capsule)", "text":"sediaan padat yang terdiri dari obat dalam cangkang keras atau lunak yang dapat larut. "},{"value":"Kaplet (kapsul tablet) ", "text":"sedian padat kompak dibuat secara kempa cetak, bentuknya oval seperti kapsul. "},{"value":"Larutan (solutiones)", "text":"sedian cair yang mengandung satu atau lebih zat kimia yang dapat larut"},{"value":"Injeksi (injectiones)", "text":"sediaan steril berupa larutan,emulsi atau suspensi atau serbuk yang harus dilarutkan atau disuspensikan terlebih dahulu sebelum digunakan, yang disuntikan dengan cara merobek jaringan ke dalam kulit atau melalui kulit atau selaput lendir. "}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PERSEDIAAN','ref_id'=>'ATURAN_PAKAI_OBAT','ref_nama'=>'aturan pakai (signa) item medis','deskripsi'=>'Referensi aturan pakai (signa) item medis','json_data'=>'[]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],

            ['client_id'=>$this->clientId,'ref_group'=>'PELAYANAN','ref_id'=>'KASUS_IGD','ref_nama'=>'Kasus IGD','deskripsi'=>'Referensi data pilihan kasus kejadian IGD','json_data'=>'[{"value":"BEDAH", "text":"Kasus dengan tindakan bedah"},{"value":"NON BEDAH", "text":"Kasus tanpa tindakan bedah"},{"value":"KEBIDANAN", "text":"Kasus Kebidanan"},{"value":"PSIKIATRIK", "text":"Kasus Psikiatrik"},{"value":"ANAK", "text":"Kasus pasien anak"},{"value":"LAIN-LAIN", "text":"Lain-lain"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PELAYANAN','ref_id'=>'STATUS_REGISTRASI','ref_nama'=>'Status Pendaftaran','deskripsi'=>'Referensi data pilihan status registrasi dan pelayanan pasien','json_data'=>'[{"value":"BOOKING", "text":"Booking Antrian"},{"value":"DAFTAR", "text":"Daftar antrian(hadir)"},{"value":"ANTRI", "text":"Antrian poli"},{"value":"PERIKSA", "text":"Sudah diperiksa"},{"value":"LENGKAP", "text":"Sudah lengkap"}]','is_aktif'=>'1','allow_user_edit'=>'0','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PELAYANAN','ref_id'=>'STATUS_KERJASAMA','ref_nama'=>'Status Kerjasama','deskripsi'=>'Referensi data pilihan status kerjasama RS dengan dokter','json_data'=>'[{"value":"MITRA", "text":"Mitra RS"},{"value":"KARYAWAN", "text":"Karyawan RS"},{"value":"-", "text":""}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'PELAYANAN','ref_id'=>'JENIS_FOTO','ref_nama'=>'Jenis Foto Radiologi','deskripsi'=>'Jenis Foto dalam penunjang Radiologi','json_data'=>'[{"value":"CAT Scan", "text":"CT/CAT Scan"},{"value":"Foto Rontgen", "text":"Foto Rontgen"},{"value":"Fluoroskopi", "text":"Foto Fluoroskopi"},{"value":"MRI", "text":"Magnetic Resonance Imaging"},{"value":"MRA", "text":"Magnetic Resonance Angiography"},{"value":"Mamografi", "text":"Foto Mamografi"},{"value":"PET Scan", "text":"Positron Emission Tomography"},{"value":"USG", "text":"Ultrasonografi"}]','is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],

            ['client_id'=>$this->clientId,'ref_group'=>'SYSTEM','ref_id'=>'NAMA_HARI','ref_nama'=>'Nama Hari Sistem','deskripsi'=>'Referensi data pilihan nama hari','json_data'=>'[{"value":"1", "text":"SENIN"},{"value":"2", "text":"SELASA"},{"value":"3", "text":"RABU"},{"value":"4", "text":"KAMIS"},{"value":"5", "text":"JUMAT"},{"value":"6", "text":"SABTU"},{"value":"7", "text":"MINGGU"}]','is_aktif'=>'1','allow_user_edit'=>'0','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            
            ['client_id'=>$this->clientId,'ref_group'=>'GIZI','ref_id'=>'BENTUK_MAKANAN','ref_nama'=>'Bentuk Makanan','deskripsi'=>'Referensi data pilihan bentuk makanan','json_data'=>json_encode('[]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'GIZI','ref_id'=>'KELOMPOK_MAKANAN','ref_nama'=>'Kelompok Makanan','deskripsi'=>'Referensi data pilihan kelompok makanan','json_data'=>json_encode('[]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'GIZI','ref_id'=>'WAKTU_PENYAJIAN','ref_nama'=>'Waktu Penyajian','deskripsi'=>'Referensi data pilihan waktu penyajian','json_data'=>json_encode('[]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'LAB','ref_id'=>'GROUP_NILAI_NORMAL','ref_nama'=>'Group nilai normal hasil lab','deskripsi'=>'Referensi data pilihan kelompok nilai normal','json_data'=>json_encode('[]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'LAB','ref_id'=>'SATUAN_NILAI_NORMAL','ref_nama'=>'Satuan nilai normal hasil lab','deskripsi'=>'Referensi data pilihan satuan nilai normal','json_data'=>json_encode('[]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'RAD','ref_id'=>'RAD_MODALITY','ref_nama'=>'Modality pemeriksaan radiologi','deskripsi'=>'Referensi data pilihan modality pemeriksaan radiologi','json_data'=>json_encode('[]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'RAD','ref_id'=>'MEDIA_HASIL','ref_nama'=>'Bentuk (media) penyimpanan hasil radiologi','deskripsi'=>'Referensi data pilihan media simpan hasil pemeriksaan radiologi','json_data'=>json_encode('[]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            
            ['client_id'=>$this->clientId,'ref_group'=>'BANK_DARAH','ref_id'=>'ASAL_DARAH','ref_nama'=>'asal persediaan darah','deskripsi'=>'Referensi asal persediaan darah','json_data'=>json_encode('[{"value":"KELUARGA", "text":"KELUARGA"},{"value":"PMI", "text":"PMI"},{"value":"INTERNAL", "text":"INTERNAL"}]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            ['client_id'=>$this->clientId,'ref_group'=>'BANK_DARAH','ref_id'=>'GROUP_DARAH','ref_nama'=>'kelompok persediaan darah','deskripsi'=>'Referensi kelompok persediaan darah','json_data'=>json_encode('[{"value":"WB-WHOLE BLOOD", "text":"WB-WHOLE BLOOD"},{"value":"PRC-PACKED RED CELL", "text":"PRC-PACKED RED CELL"},{"value":"WASHED ERYTHROCYTE", "text":"WE-WASHED ERYTHROCYTE"},{"value":"LP-LIQUITE PLASMA", "text":"LP-LIQUITE PLASMA"},{"value":"FFP-FRESH FROZEN PLASMA", "text":"FFP-FRESH FROZEN PLASMA"},{"value":"TPK-PLASMA KONVALESEN", "text":"TPK-PLASMA KONVALESEN"},{"value":"AHF-CRYOPRECIPITATE", "text":"AHF-CRYOPRECIPITATE"},{"value":"BC- BUFFICOAT GRANULOCYTE", "text":"BC- BUFFICOAT GRANULOCYTE"},{"value":"TC- THROMBOCYTE CONCETRATE", "text":"TC- THROMBOCYTE CONCETRATE"}]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
            
            ['client_id'=>$this->clientId,'ref_group'=>'INFORMED_CONSENT','ref_id'=>'JENIS_JAWABAN','ref_nama'=>'Jenis Jawaban','deskripsi'=>'jenis jawaban informed consent','json_data'=>json_encode('[{"value":"check", "text":"Checkbox"},{"value":"text", "text":"Text"},{"value":"combo", "text":"Combobox"},{"value":"area", "text":"Textarea"},{"value":"pict", "text":"Picture"},{"value":"number", "text":"Number"}]'),'is_aktif'=>'1','allow_user_edit'=>'1','created_by'=>'system','updated_by'=>'system','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}