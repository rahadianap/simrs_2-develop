
import auth from '@/Stores/auth';
import client from '@/Stores/client';
import profile from '@/Stores/profile';
import role from '@/Stores/role';
import depo from '@/Stores/depo';
import asuransi from '@/Stores/asuransi';
import vendor from '@/Stores/vendor';
import spesialisasi from '@/Stores/spesialisasi';
import dokter from '@/Stores/dokter';
import ksm from '@/Stores/ksm';

import icd9 from '@/Stores/icd9';
import icd10 from '@/Stores/icd10';
import dtd from '@/Stores/dtd';

import signa from '@/Stores/signa';
import sediaan from '@/Stores/sediaan';
import coa from '@/Stores/coa';
import kelompokTindakan from '@/Stores/kelompokTindakan';
import kelompokTagihan from '@/Stores/kelompokTagihan';
import kelompokVclaim from '@/Stores/kelompokVclaim';
import komponenHarga from '@/Stores/komponenHarga';
import satuan from '@/Stores/satuan';
import kodeRL from '@/Stores/kodeRL';

import propinsi from '@/Stores/propinsi';
import kabupaten from '@/Stores/kabupaten';
import kecamatan from '@/Stores/kecamatan';
import kelurahan from '@/Stores/kelurahan';

import tindakan from '@/Stores/tindakan';
import kelas from '@/Stores/kelas';

import unitPelayanan from '@/Stores/unitPelayanan';
import ruang from '@/Stores/ruang';
import bed from '@/Stores/bed';
import referensi from '@/Stores/referensi';

import kelompokMakanan from './kelompokMakanan';
import pasien from './pasien';
import makanan from './makanan';
import menu from './menu';
import menuMakanan from './menuMakanan';
import kelompokMenu from './kelompokMenu';
import templateGizi from './templateGizi';

import diet from './diet';
import dietPasien from './dietPasien';
import dietMenu from './dietMenu';
import cetakanMaster from './cetakanMaster';
// import cetakanDataDokter from './cetakanDokter';
// import cetakanPasien from './cetakanPasien';

import jadwalDokter from './jadwalDokter';
import refProduk from './refProduk';
import produk from './produk';
import produkAkun from './produkAkun';

import importExcel from '@/Stores/importExcel';
import templateMaster from '@/Stores/templatemaster';
import distribusiGizi from '@/Stores/distribusiGizi';
import distribusiGiziDetail from './distribusiGiziDetail';

import purchaseRequest from './purchaseRequest';
import cetakanDataPR from './cetakanPurchaseRequest';
import purchaseOrder from './purchaseOrder';
import cetakanDataPO from './cetakanPurchaseOrder';
import purchaseReceive from './purchaseReceive';
import purchaseReturn from './purchaseReturn';
import refPenunjang from './refPenunjang';
import itemLab from './itemLab';
import stockOpname from './stockOpname';
import produksi from './produksi';
import distribusi from './distribusi';
import users from './users';
import adjustment from './adjustment';
import inventoryIssue from './inventoryIssue';
import pendaftaran from './pendaftaran';

import linen from './linen';
import cssd from './cssd';
import bankDarah from './bankDarah';
import radiologi from './radiologi';
import patologi from './patologi';

import produkDarah from './produkDarah';
import asalDarah from './asalDarah';

import bukuHarga from './bukuHarga';
import purchaseManagement from './purchaseManagement';
import harga from './harga';
import rawatInap from './rawatInap';

import labRegistrasi from './labRegistrasi';
import hasilLab from './hasilLab';
import tindakanLab from './tindakanLab';

import cetakan from './cetakan';
import cetakanDataDistribusi from './cetakanDistribusi';
import cetakanKartuStock from './cetakanKartuStock';
import cetakanDataPOR from './cetakanPurchaseReceive';

import cetakanLab from './cetakanLab';
import cetakanPendaftaran from './cetakanPendaftaran';
import cetakanBilling from './cetakanBilling';

//import informConsent from './informConsent';
import informedConsent from './informedConsent';
import informedConsentDetail from './informedConsentDetail';
import operasi from './operasi';
import invitation from './invitation';
import satusehatDokter from './satusehatDokter';
import satusehatPasien from './satusehatPasien';
import satusehatOrganisasi from './satusehatOrganisasi';
import satusehatLokasi from './satusehatLokasi';

import report from './report';
import bpjsReferensi from './bpjsReferensi';
import bpjsRujukan from './bpjsRujukan';
import jknAntrian from './jknAntrian';
import kiosk from './kiosk';
import booking from './booking';
import rawatJalan from './rawatJalan';
import asset from './asset';
import assetOverview from './assetoverview';
import assetMaintenance from './assetmaintenance';
import schedule from './schedule';
import lokasi from './lokasi';

import searchOption from './searchOption';
import mealOrder from './mealOrder';
import resep from './resep';
import pemeriksaan from './pemeriksaan';
import asesmen from './asesmen';
import radTemplate from './radTemplate';
import pembayaran from './pembayaran';
import spareparts from './spareparts';
import patologiTemplate from './patologiTemplate';
import refAset from './refAset';


import jabatan from './jabatan';
import karyawan from './karyawan';
import hrdokumen from './hrdokumen';
import hrkeluarga from './hrkeluarga';
import hrpendidikan from './hrpendidikan';
import hrjabatan from './hrjabatan';
import medicalrecord from './medicalrecord';

import farmasi from './farmasi';
import apotek from './apotek';
import cetakanResep from './cetakanResep';

import praktekDokter from './praktekdokter';
import pencatatankas from './pencatatankas';

import pdf from './pdf';

import { createStore } from 'vuex';

const store = createStore({
    namespace: true,
    modules: {
        auth,
        client,
        profile,
        role,
        depo,
        asuransi,
        vendor,
        spesialisasi,
        dokter,
        ksm,
        icd10,
        icd9,
        dtd,
        signa,
        sediaan,
        satuan,
        coa,
        kelompokTindakan,
        kelompokTagihan,
        kelompokVclaim,
        komponenHarga,
        importExcel,
        templateMaster,
        kodeRL,
        propinsi,
        kabupaten,
        kecamatan,
        kelurahan,
        tindakan,
        kelas,
        unitPelayanan,
        ruang,
        bed,
        referensi,
        kelompokMakanan,
        pasien,
        makanan,
        menu,
        menuMakanan,
        kelompokMenu,
        diet,
        dietPasien,
        templateGizi,
        // cetakanDataDokter,
        jadwalDokter,
        produk,
        refProduk,
        produkAkun,
        distribusiGizi,
        distribusiGiziDetail,
        purchaseRequest,
        cetakanDataPR,
        purchaseOrder,
        cetakanDataPO,
        purchaseReceive,
        purchaseReturn,
        refPenunjang,
        itemLab,
        stockOpname,
        produksi,
        distribusi,
        users,
        adjustment,
        inventoryIssue,
        pendaftaran,
        linen,
        cssd,
        bankDarah,
        radiologi,
        bukuHarga,
        produkDarah,
        asalDarah,
        purchaseManagement,
        harga,
        cetakanDataDistribusi,
        cetakanKartuStock,
        cetakanDataPOR,
        rawatInap,
        satusehatDokter,
        satusehatPasien,
        satusehatOrganisasi,
        satusehatLokasi,
        labRegistrasi,
        hasilLab,
        tindakanLab,
        informedConsent,
        informedConsentDetail,
        operasi,
        invitation,
        cetakanLab,
        report,
        cetakanPendaftaran,
        bpjsReferensi,
        bpjsRujukan,
        jknAntrian,
        kiosk,
        booking,
        searchOption,
        rawatJalan,
        // cetakanPasien,
        mealOrder,
        dietMenu,
        resep,
        pemeriksaan,
        // cetakanPasien,
        patologi,
        asesmen,
        radTemplate,
        asset,
        pembayaran,
        assetOverview,
        assetMaintenance,
        schedule,
        spareparts,
        patologiTemplate,
        pdf,
        refAset,
        lokasi,
        jabatan,
        karyawan,
        hrdokumen,
        hrjabatan,
        hrkeluarga,
        hrpendidikan,
        farmasi,
        medicalrecord,
        apotek,
        cetakanBilling,
        cetakanResep,
        praktekDokter,
        pencatatankas,
        cetakanMaster,
        cetakan
    },

    state : {
        errors : [],
    },

    getters : {
        /**get errors data */
        errors : state => {
            return state.errors;
        }
    },
    
    mutations : {        
        SET_ERRORS(state, payload) { state.errors = payload },
        CLR_ERRORS(state) { state.errors = [] },
    }
});

export default store;
