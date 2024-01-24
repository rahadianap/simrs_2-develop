<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'api.verify']], function () {

    /** MASTER VENDOR/SUPPLIER */
    Route::get('/vendors', 'MasterVendorController@list');
    Route::get('/vendors/{id}', 'MasterVendorController@data');
    Route::post('/vendors', 'MasterVendorController@create');
    Route::put('/vendors', 'MasterVendorController@update');
    Route::delete('/vendors/{id}', 'MasterVendorController@destroy');

    /** MASTER DEPO */
    Route::get('/depos', 'MasterDepoController@lists');
    Route::get('/depos/{id}', 'MasterDepoController@data');
    Route::post('/depos', 'MasterDepoController@create');
    Route::put('/depos', 'MasterDepoController@update');
    Route::delete('/depos/{id}', 'MasterDepoController@delete');

    /** MASTER PASIEN */
    Route::get('/patients/{patientId}', 'MasterPasienController@data');
    Route::get('/patients', 'MasterPasienController@lists');
    Route::post('/patients', 'MasterPasienController@create');
    Route::put('/patients', 'MasterPasienController@update');
    Route::delete('/patients/{patientsId}', 'MasterPasienController@delete');

    /** MASTER KEBANGSAAN */
    Route::get('/citizenships', 'KebangsaanController@lists');
    Route::get('/citizenships/{kebangsaanId}', 'KebangsaanController@data');
    Route::post('/citizenships', 'KebangsaanController@create');
    Route::put('/citizenships', 'KebangsaanController@update');
    Route::delete('/citizenships/{kebangsaanId}', 'KebangsaanController@delete');

    /** MASTER PENJAMIN */
    Route::get('/guarantors', 'PenjaminController@lists');
    Route::get('/guarantors/{PenjaminId}', 'PenjaminController@data');
    Route::post('/guarantors', 'PenjaminController@create');
    Route::put('/guarantors', 'PenjaminController@update');
    Route::delete('/guarantors/{PenjaminId}', 'PenjaminController@delete');

    /** MASTER PEKERJAAN */
    Route::get('/occupations', 'PekerjaanController@lists');
    Route::get('/occupations/{pekerjaanId}', 'PekerjaanController@data');
    Route::post('/occupations', 'PekerjaanController@create');
    Route::put('/occupations', 'PekerjaanController@update');
    Route::delete('/occupations/{pekerjaanId}', 'PekerjaanController@delete');

    /** MASTER SUKU */
    Route::get('/tribes', 'SukuController@lists');
    Route::get('/tribes/{sukuId}', 'SukuController@data');
    Route::post('/tribes', 'SukuController@create');
    Route::put('/tribes', 'SukuController@update');
    Route::delete('/tribes/{sukuId}', 'SukuController@delete');

    /** MASTER PROVINCE */
    Route::get('/bpjs/provinces', 'PropinsiController@PropBpjsLists');
    Route::put('/bpjs/provinces', 'PropinsiController@updateBridging');
    
    Route::get('/provinces/{propinsiId}', 'PropinsiController@data');
    Route::get('/provinces', 'PropinsiController@lists');
    Route::post('/provinces', 'PropinsiController@create');
    Route::put('/provinces', 'PropinsiController@update');
    Route::delete('/provinces/{propinsiId}', 'PropinsiController@delete');
    
    /** MASTER CITIES */
    Route::put('/bpjs/cities', 'KotaController@updateBridging');
    Route::get('/bpjs/cities', 'KotaController@kabBpjsLists');
    
    Route::get('/cities/{kotaId}', 'KotaController@data');
    Route::get('/cities', 'KotaController@lists');
    Route::post('/cities', 'KotaController@create');
    Route::put('/cities', 'KotaController@update');
    Route::delete('/cities/{kotaId}', 'KotaController@delete');

    /** MASTER COUNTIES - Kecamatan */
    Route::get('/bpjs/counties/{kecamatanId}', 'KecamatanController@kecBpjsLists');
    Route::put('/bpjs/counties', 'KecamatanController@updateBridging');
    
    Route::get('/counties/{kecamatanId}', 'KecamatanController@data');
    Route::get('/counties', 'KecamatanController@lists');
    Route::post('/counties', 'KecamatanController@create');
    Route::put('/counties', 'KecamatanController@update');
    Route::delete('/counties/{kecamatanId}', 'KecamatanController@delete');

    /** MASTER DISTRICT - kelurahan */
    Route::get('/districts/{kelurahanId}', 'KelurahanController@data');
    Route::get('/districts', 'KelurahanController@lists');
    Route::post('/districts', 'KelurahanController@create');
    Route::put('/districts', 'KelurahanController@update');
    Route::delete('/districts/{kelurahanId}', 'KelurahanController@delete');
    Route::get('/zipcodes', 'KelurahanController@kodepos');

    /** MASTER ICD 9 */
    Route::get('/icd9', 'Icd9Controller@lists');
    Route::get('/icd9/{kode_icd}', 'Icd9Controller@data');
    Route::post('/icd9', 'Icd9Controller@create');
    Route::put('/icd9', 'Icd9Controller@update');
    Route::POST('/icd9/importExcel', 'Icd9Controller@importExcel');
    Route::delete('/icd9/{kode_icd}', 'Icd9Controller@delete');

    /** MASTER ICD 10 */
    Route::get('/icd10', 'Icd10Controller@lists');
    Route::get('/icd10/{kode_icd}', 'Icd10Controller@data');
    Route::post('/icd10', 'Icd10Controller@create');
    Route::put('/icd10', 'Icd10Controller@update');
    Route::POST('/icd10/importExcel', 'Icd10Controller@importExcel');
    Route::delete('/icd10/{kode_icd}', 'Icd10Controller@delete');

    /** MASTER DTD Diagnosa */
    Route::get('/dtd', 'DtdController@lists');
    Route::get('/dtd/{no_dtd}', 'DtdController@data');
    Route::post('/dtd', 'DtdController@create');
    Route::put('/dtd', 'DtdController@update');
    Route::POST('/dtd/importExcel', 'DtdController@importExcel');
    Route::delete('/dtd/{no_dtd}', 'DtdController@delete');

    // Master Data Spesialisasi
    Route::GET('/specializations', 'SpesialisasiController@lists');
    Route::GET('/specializations/{spesialisasiId}', 'SpesialisasiController@data');
    Route::POST('/specializations', 'SpesialisasiController@create');
    Route::PUT('/specializations', 'SpesialisasiController@update');
    Route::DELETE('/specializations/{spesialisasiId}', 'SpesialisasiController@delete');

    // Master Data SMF
    Route::GET('/smf', 'SMFController@lists');
    Route::GET('/smf/{smfId}', 'SMFController@data');
    Route::POST('/smf', 'SMFController@create');
    Route::PUT('/smf', 'SMFController@update');
    Route::DELETE('/smf/{smfId}', 'SMFController@delete');

    // Master Data Dokter
    Route::GET('/doctors', 'DokterController@lists');
    Route::GET('/doctors/unit/{id}', 'DokterController@listsDokUnit');
    Route::GET('/doctors/{doctorId}', 'DokterController@data');
    Route::POST('/doctors/avatar', 'DokterController@avatar');
    Route::POST('/doctors', 'DokterController@create');
    Route::PUT('/doctors', 'DokterController@update');
    Route::DELETE('/doctors/{doctorId}', 'DokterController@delete');

    // Master Data : Jadwal Dokter untuk pembuatan jadwal
    Route::GET('schedule/units', 'DokterJadwalController@listByPoli');
    Route::GET('schedule/doctors', 'DokterJadwalController@listByDokter');

    // Master Data : Jadwal Dokter
    Route::GET('doctors/schedule/{id}', 'DokterJadwalController@data');
    Route::GET('doctors/schedule', 'DokterJadwalController@lists');
    Route::POST('doctors/schedule', 'DokterJadwalController@create');
    Route::PUT('doctors/schedule', 'DokterJadwalController@update');
    Route::DELETE('doctors/schedule/{id}', 'DokterJadwalController@delete');

    // Master Data : Kelas Pelayanan
    Route::get('/bpjs/classes', 'KelasController@KelasBpjsLists');

    Route::GET('/services/classes/{classId}', 'KelasController@data');
    Route::GET('/services/classes', 'KelasController@lists');
    Route::POST('/services/classes', 'KelasController@create');
    Route::PUT('/services/classes', 'KelasController@update');
    Route::DELETE('/services/classes/{id}', 'KelasController@delete');

    // Master Data : Ruang
    Route::get('/bpjs/rooms', 'RuangPelayananController@RuangBpjsLists');

    Route::GET('rooms', 'RuangPelayananController@lists');
    Route::GET('rooms/{id}', 'RuangPelayananController@data');
    Route::POST('rooms', 'RuangPelayananController@create');
    Route::PUT('rooms', 'RuangPelayananController@update');
    Route::DELETE('rooms/{id}', 'RuangPelayananController@delete');

    // Master Data : Tempat tidur
    Route::GET('beds', 'BedPelayananController@lists');
    Route::GET('beds/{id}', 'BedPelayananController@data');
    Route::POST('beds', 'BedPelayananController@create');
    Route::PUT('beds', 'BedPelayananController@update');
    Route::DELETE('beds/{id}', 'BedPelayananController@delete');

    // Route : Master Data Kelompok Tindakan
    Route::GET('/acts/groups/{id}', 'KelompokTindakanController@data');
    Route::GET('/acts/groups', 'KelompokTindakanController@list');
    Route::POST('/acts/groups', 'KelompokTindakanController@create');
    Route::PUT('/acts/groups', 'KelompokTindakanController@update');
    Route::DELETE('/acts/groups/{id}', 'KelompokTindakanController@delete');

    // Route : Master Data Tindakan
    Route::POST('/acts/units', 'TindakanController@addUnit');
    
    // Route : Master Data Bhp Tindakan
    Route::POST('/acts/products', 'TindakanController@addProduct');
    Route::DELETE('/acts/products/{act_product_id}', 'TindakanController@deleteProduct');
    
    // Route : Master Data Item Lab Tindakan
    Route::POST('/acts/labs/templates', 'TindakanController@addLabTemplate');
    Route::PUT('/acts/labs/templates', 'TindakanController@updateLabTemplate');
    Route::DELETE('/acts/labs/templates/{templateId}', 'TindakanController@deleteLabTemplate');

    Route::GET('/acts/data/{tindakan_id}', 'TindakanController@data');
    Route::GET('/acts/{klasifikasi?}', 'TindakanController@list');
    Route::POST('/acts', 'TindakanController@create');
    Route::PUT('/acts', 'TindakanController@update');
    Route::DELETE('/acts/{tindakan_id}', 'TindakanController@delete');

    // Master Data : Unit Dokter
    Route::GET('/units/{unitId}/doctors', 'DokterUnitController@lists');
    Route::GET('/units/doctors/{unitDokterId}', 'DokterUnitController@data');
    Route::POST('/units/doctors', 'DokterUnitController@create');
    Route::PUT('/units/doctors', 'DokterUnitController@update');
    Route::DELETE('/units/doctors/{unitDokterId}', 'DokterUnitController@delete');

    // Route : Master Data Unit Tindakan
    Route::GET('/units/acts/{unit_tindakan_id}', 'UnitTindakanController@data');
    Route::GET('/units/{unitId}/acts/pricelist', 'UnitTindakanController@priceLists');
    Route::GET('/units/{unitId}/acts', 'UnitTindakanController@list');
    Route::POST('/units/acts', 'UnitTindakanController@create');
    Route::DELETE('/units/acts/{unit_tindakan_id}', 'UnitTindakanController@delete');

    // Route : Master Data Unit Tindakan
    Route::GET('/units/depos/{unitDepoId}', 'UnitDepoController@data');
    Route::GET('/units/{unitId}/depos/', 'UnitDepoController@list');
    Route::POST('/units/depos/', 'UnitDepoController@create');
    Route::PUT('/units/depos/', 'UnitDepoController@update');
    Route::DELETE('/units/depos/{unitDepoId}', 'UnitDepoController@delete');

    // Master Data : Unit Pelayanan
    Route::get('/bpjs/units', 'UnitPelayananController@UnitBpjsLists');

    Route::GET('/units/{id}', 'UnitPelayananController@data');
    Route::GET('/units', 'UnitPelayananController@lists');
    Route::POST('/units/icon', 'UnitPelayananController@unitIcon');
    Route::POST('/units', 'UnitPelayananController@create');
    Route::PUT('/units', 'UnitPelayananController@update');
    Route::DELETE('/units/{id}', 'UnitPelayananController@delete');
    


    // Route : Master Produk
    /**
     * tipe produk diisi dengan :
     * 1. medical -> untuk item medis dan alkes 
     * 2. general -> untuk item umum
     * 3. kitchen -> bahan dapur
     */
    Route::GET('/products/items/{produk_id}', 'ProdukController@data');
    Route::GET('/products/{tipe_produk}/items', 'ProdukController@list');
    Route::POST('/products/items', 'ProdukController@create');
    Route::PUT('/products/items', 'ProdukController@update');
    Route::DELETE('/products/items/{produk_id}', 'ProdukController@delete');

    // Route : Master Produk Account
    Route::GET('/products/accounts/{produk_account_id}', 'ProdukAccountController@data');
    Route::GET('/products/accounts', 'ProdukAccountController@lists');
    Route::POST('/products/accounts', 'ProdukAccountController@create');
    Route::PUT('/products/accounts', 'ProdukAccountController@update');
    Route::DELETE('/products/accounts/{produk_account_id}', 'ProdukAccountController@delete');

    // Route : Master Golongan Produk
    // Route::GET('/products/groups/{golongan_produk_id}', 'GolonganProdukController@data');
    // Route::GET('/products/groups', 'GolonganProdukController@list');
    // Route::POST('/products/groups', 'GolonganProdukController@create');
    // Route::PUT('/products/groups', 'GolonganProdukController@update');
    // Route::DELETE('/products/groups/{golongan_produk_id}', 'GolonganProdukController@delete');

    Route::GET('/products/groups/{ref_id}', 'ReferensiProdukController@data');
    Route::GET('/products/groups', 'ReferensiProdukController@lists');
    Route::POST('/products/groups', 'ReferensiProdukController@create');
    Route::PUT('/products/groups', 'ReferensiProdukController@update');
    Route::DELETE('/products/groups/{ref_id}', 'ReferensiProdukController@delete');

    // Route : Master Depo Produk
    Route::GET('/depos/products/{depo_products_id}', 'DepoProdukController@data');
    Route::GET('/depos/{depos_id}/products', 'DepoProdukController@list');
    Route::POST('/depos/{depos_id}/products', 'DepoProdukController@create');
    Route::PUT('/depos/products', 'DepoProdukController@update');
    Route::DELETE('/depos/products/{depo_products_id}', 'DepoProdukController@delete');

    // Route : Master Satuan
    Route::GET('/uoms/{satuan_id}', 'SatuanController@data');
    Route::GET('/uoms', 'SatuanController@list');
    Route::POST('/uoms', 'SatuanController@create');
    Route::PUT('/uoms', 'SatuanController@update');
    Route::DELETE('/uoms/{satuan_id}', 'SatuanController@delete');

    // Route : Master Sediaan
    Route::GET('/forms/{sediaan_id}', 'SediaanController@data');
    Route::GET('/forms', 'SediaanController@list');
    Route::POST('/forms', 'SediaanController@create');
    Route::PUT('/forms', 'SediaanController@update');
    Route::DELETE('/forms/{sediaan_id}', 'SediaanController@delete');

    // Route : Master Signa
    Route::GET('/signas/{signa_id}', 'SignaController@data');
    Route::GET('/signas', 'SignaController@list');
    Route::POST('/signas', 'SignaController@create');
    Route::PUT('/signas', 'SignaController@update');
    Route::DELETE('/signas/{signa_id}', 'SignaController@delete');

    // Route : Master COA
    Route::GET('/coa/headers/{level}', 'CoaController@coaHeader');
    Route::GET('/coa/accounts/{id}', 'CoaController@data');
    Route::GET('/coa/accounts', 'CoaController@lists');
    Route::GET('/coa/exportExcel', 'CoaController@exportExcel');
    Route::POST('/coa/accounts', 'CoaController@create');
    Route::POST('/coa/importExcel', 'CoaController@importExcel');
    Route::PUT('/coa/accounts', 'CoaController@update');
    Route::DELETE('/coa/accounts/{coa_id}', 'CoaController@delete');

    // Route : Master Data Kelompok Tagihan
    Route::GET('/billings/groups/{id}', 'KelompokTagihanController@data');
    Route::GET('/billings/groups', 'KelompokTagihanController@list');
    Route::POST('/billings/groups', 'KelompokTagihanController@create');
    Route::PUT('/billings/groups', 'KelompokTagihanController@update');
    Route::DELETE('/billings/groups/{id}', 'KelompokTagihanController@delete');

    // Route : Master Data Kelompok Vklaim
    Route::GET('/vclaim/groups/{id}', 'KelompokVclaimController@data');
    Route::GET('/vclaim/groups', 'KelompokVclaimController@list');
    Route::POST('/vclaim/groups', 'KelompokVclaimController@create');
    Route::PUT('/vclaim/groups', 'KelompokVclaimController@update');
    Route::DELETE('/vclaim/groups/{id}', 'KelompokVclaimController@delete');

    // Route : Master Data Komponen harga
    Route::GET('/tariffs/components/{id}', 'KomponenHargaController@data');
    Route::GET('/tariffs/components', 'KomponenHargaController@list');
    Route::POST('/tariffs/components', 'KomponenHargaController@create');
    Route::PUT('/tariffs/components', 'KomponenHargaController@update');
    Route::DELETE('/tariffs/components/{id}', 'KomponenHargaController@delete');
    
    // Route : Template Master Data
    Route::GET('/templatemaster', 'TemplateMasterController@list');
    Route::GET('/templatemaster/coa', 'TemplateMasterController@exportCoa');
    Route::GET('/templatemaster/dtd', 'TemplateMasterController@exportDtd');
    Route::GET('/templatemaster/icd9', 'TemplateMasterController@exportICD9');
    Route::GET('/templatemaster/icd10', 'TemplateMasterController@exportICD10');
    Route::GET('/templatemaster/unit', 'TemplateMasterController@exportUnit');
    Route::GET('/templatemaster/ruang', 'TemplateMasterController@exportRuang');
    Route::GET('/templatemaster/bed', 'TemplateMasterController@exportBed');
    Route::GET('/templatemaster/penjamin', 'TemplateMasterController@exportPenjamin');

    // Route : Master Data Group RL
    Route::GET('/rl/groups/codes/{groupId}', 'KodeRLController@listGroupItems');
    Route::GET('/rl/groups/{id}', 'KodeRLController@dataGroup');
    Route::GET('/rl/groups', 'KodeRLController@listGroup');
    Route::POST('/rl/groups', 'KodeRLController@createGroup');
    Route::PUT('/rl/groups', 'KodeRLController@updateGroup');
    Route::DELETE('/rl/groups/{id}', 'KodeRLController@deleteGroup');

    // Route : Master Data Kode RL
    Route::GET('/rl/codes/{id}', 'KodeRLController@data');
    Route::GET('/rl/codes', 'KodeRLController@list');
    Route::POST('/rl/codes', 'KodeRLController@create');
    Route::PUT('/rl/codes', 'KodeRLController@update');
    Route::DELETE('/rl/codes/{id}', 'KodeRLController@delete');

    // Route : Master Data Referensi
    Route::GET('/ancillary/references', 'ReferensiController@listsRefPenunjang');
    
    Route::GET('/references/{refId}', 'ReferensiController@data');
    Route::GET('/references', 'ReferensiController@lists');
    Route::PUT('/references', 'ReferensiController@update');

    Route::GET('/meals/groups', 'KelompokMakananController@list');
    Route::POST('/meals/groups', 'KelompokMakananController@create');
    Route::GET('/meals/groups/{meals_group_id}', 'KelompokMakananController@data');
    Route::PUT('/meals/groups', 'KelompokMakananController@update');
    Route::DELETE('/meals/groups/{meals_group_id}', 'KelompokMakananController@delete');

    // Route : Master Data Makanan
    Route::GET('/meals', 'MakananController@list');
    Route::POST('/meals', 'MakananController@create');
    Route::GET('/meals/{meals_id}', 'MakananController@data');
    Route::PUT('/meals', 'MakananController@update');
    Route::DELETE('/meals/{meals_id}', 'MakananController@delete');

    // Route : Master Data Kelompok Menu
    Route::GET('/menu/groups', 'KelompokMenuController@list');
    Route::POST('/menu/groups', 'KelompokMenuController@create');
    Route::GET('/menu/groups/{menu_group_id}', 'KelompokMenuController@data');
    Route::PUT('/menu/groups', 'KelompokMenuController@update');
    Route::DELETE('/menu/groups/{menu_group_id}', 'KelompokMenuController@delete');

    // Route : Master Data Menu
    Route::GET('/menu', 'MenuController@list');
    Route::POST('/menu', 'MenuController@create');
    Route::GET('/menu/{menu_id}', 'MenuController@data');
    Route::PUT('/menu', 'MenuController@update');
    Route::DELETE('/menu/{menu_id}', 'MenuController@delete');

    // Route : Master Data Menu Makanan
    Route::GET('/mealsmenu', 'MenuMakananController@list');
    Route::POST('/mealsmenu', 'MenuMakananController@create');
    Route::GET('/mealsmenu/{meals_menu_id}', 'MenuMakananController@data');
    Route::PUT('/mealsmenu', 'MenuMakananController@update');
    Route::DELETE('/mealsmenu/{meals_menu_id}', 'MenuMakananController@delete');

    // Route : Master Data Diet
    Route::GET('/diet', 'DietController@list');
    Route::POST('/diet', 'DietController@create');
    Route::GET('/diet/{diet_id}', 'DietController@data');
    Route::PUT('/diet', 'DietController@update');
    Route::DELETE('/diet/{diet_id}', 'DietController@delete');
    Route::POST('/diet/{diet_id}/menu', 'DietController@createDietMenu');
    Route::GET('/diet/{diet_id}/menu', 'DietController@listDietMenu');

    // Route : Master Data Bahan Makanan
    Route::GET('/mealsigd', 'MakananBahanController@list');
    Route::POST('/mealsigd', 'MakananBahanController@create');
    Route::GET('/mealsigd/{makanan_bahan_id}', 'MakananBahanController@data');
    Route::PUT('/mealsigd', 'MakananBahanController@update');
    Route::DELETE('/mealsigd/{makanan_bahan_id}', 'MakananBahanController@delete');

    // Route : Master Data Template Gizi
    Route::GET('/template/nutrient', 'TemplateGiziController@list');
    Route::POST('/template/nutrient', 'TemplateGiziController@create');
    Route::GET('/template/nutrient/{temp_gizi_id}', 'TemplateGiziController@data');
    Route::PUT('/template/nutrient', 'TemplateGiziController@update');
    Route::DELETE('/template/nutrient/{temp_gizi_id}', 'TemplateGiziController@delete');

    // Route : Master Data Distribusi Gizi
    Route::GET('/distributions/nutrient', 'DistribusiGiziController@list');
    Route::PUT('/distributions/nutrient/{diet_id}/distribute', 'DistribusiGiziController@distributeDiet');
    Route::POST('/distributions/nutrient', 'DistribusiGiziController@create');
    Route::GET('/distributions/nutrient/{dist_gizi_id}', 'DistribusiGiziController@data');
    Route::PUT('/distributions/nutrient', 'DistribusiGiziController@update');
    Route::DELETE('/distributions/nutrient/{dist_gizi_id}', 'DistribusiGiziController@delete');

    // Route : Master Data Detail Distribusi Gizi
    Route::GET('/distributions/detail/nutrient', 'DistribusiGiziDetailController@list');
    Route::POST('/distributions/detail/nutrient', 'DistribusiGiziDetailController@create');
    Route::GET('/distributions/detail/nutrient/{dist_gizi_id}', 'DistribusiGiziDetailController@data');
    Route::PUT('/distributions/detail/nutrient', 'DistribusiGiziDetailController@update');
    Route::DELETE('/distributions/detail/nutrient/{dist_gizi_id}', 'DistribusiGiziDetailController@delete');

    
    // Route : Master Data Buku Harga
    //Route::GET('/tariffs/groups/{groupId}/items', 'BukuHargaController@listHargaTindakan');
    
    Route::GET('/tariffs/groups/{groupId}', 'BukuHargaController@data');
    Route::GET('/tariffs/groups', 'BukuHargaController@lists');
    Route::POST('/tariffs/groups', 'BukuHargaController@create');
    Route::PUT('/tariffs/groups', 'BukuHargaController@update');
    Route::PUT('/tariffs/groups/{groupId}/approve', 'BukuHargaController@approve');
    Route::DELETE('/tariffs/groups/{groupId}', 'BukuHargaController@delete');

    Route::GET('/tariffs/items/lists/{groupId?}', 'HargaController@lists');
    Route::GET('/tariffs/items/{tarifId}', 'HargaController@data');
    Route::POST('/tariffs/items', 'HargaController@create');
    Route::PUT('/tariffs/items', 'HargaController@update');
    Route::PUT('/tariffs/items/{tarifId}/approve', 'HargaController@approve');
    Route::DELETE('/tariffs/items/{tarifId}', 'HargaController@delete');

    // Route : Master Informed Consent Detail
    Route::GET('/informed/detail/{detailId}', 'InformedConsentDetailController@data');
    Route::GET('/informed/detail', 'InformedConsentDetailController@lists');
    Route::POST('/informed/detail', 'InformedConsentDetailController@create');
    Route::PUT('/informed/detail', 'InformedConsentDetailController@update');
    Route::DELETE('/informed/detail/{detailId}', 'InformedConsentDetailController@delete');

    // Route : Master Informed Consent Template
    Route::GET('/informed/unit/{unitId}', 'UnitAssesmenController@data');
    Route::GET('/informed/unit', 'UnitAssesmenController@lists');
    Route::POST('/informed/unit', 'UnitAssesmenController@create');
    Route::DELETE('/informed/unit/{mapId}', 'UnitAssesmenController@delete');

    // Route : Master Informed Consent Template
    Route::GET('/informed/{inConId}', 'InformedConsentController@data');
    Route::GET('/informed', 'InformedConsentController@lists');
    Route::POST('/informed', 'InformedConsentController@create');
    Route::PUT('/informed', 'InformedConsentController@update');
    Route::DELETE('/informed/item/{informMapId}', 'InformedConsentController@deleteItem');
    Route::DELETE('/informed/{inConId}', 'InformedConsentController@delete');

    // Route : Testing HTML ke PDF    
    Route::GET('/test/pdf/patologi', 'TestingPDFController@dataPatologi');
    Route::GET('/test/pdf/rawat_inap', 'TestingPDFController@dataRawatInap');
    Route::GET('/test/pdf/rekap/pasienAntiHIV', 'TestingPDFController@dataRekapPasienAntiHIV');
    Route::GET('/test/pdf/rekap/pasienRujukan', 'TestingPDFController@dataRekapPasienRujukan');
    Route::GET('/test/pdf/laporan/pendapatan_farmasi', 'TestingPDFController@dataLaporanPendapatanFarmasi');
    Route::GET('/test/pdf/laporan/penggunaan_obat/karyawan', 'TestingPDFController@dataLaporanPenggunaanObatKaryawan');
    Route::GET('/test/pdf/laporan/jumlah_resep', 'TestingPDFController@dataLaporanJumlahResep');
    
    Route::get('/test/pdf/render_blade', function () {
        return view('example');
    });
});

