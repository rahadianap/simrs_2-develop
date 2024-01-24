const masterRouter = [
    // {
    //     name: 'masterTesting',
    //     path: '/master/test/pdf',
    //     component: () => import('@/Pages/MasterData/TestingPDF'),
    //     meta: { requiresAuth: true, layout:'AppLayout' },
    // },
    {
        name: 'masterReferensi',
        path: '/master/referensi',
        component: () => import('@/Pages/MasterData/Referensi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterCoa',
        path: '/master/coa',
        component: () => import('@/Pages/MasterData/Coa'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterAsuransi',
        path: '/master/asuransi',
        component: () => import('@/Pages/MasterData/Asuransi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterPemasok',
        path: '/master/pemasok',
        component: () => import('@/Pages/MasterData/Pemasok'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterKelompokTagihan',
        path: '/master/kelompok/tagihan',
        component: () => import('@/Pages/MasterData/KelompokTagihan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterKelompokHarga',
        path: '/master/kelompok/harga',
        component: () => import('@/Pages/MasterData/KelompokHarga'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterKelompokVklaim',
        path: '/master/kelompok/vklaim',
        component: () => import('@/Pages/MasterData/KelompokVklaim'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterKomponenTariff',
        path: '/master/komponen/tariff',
        component: () => import('@/Pages/MasterData/KomponenTariff'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterTindakanMedis',
        path: '/master/tindakan/medis',
        component: () => import('@/Pages/MasterData/Tindakan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterTindakanLab',
        path: '/master/tindakan/lab',
        component: () => import('@/Pages/MasterData/TindakanLab'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterTindakanRadiologi',
        path: '/master/tindakan/radiologi',
        component: () => import('@/Pages/MasterData/TindakanRadiologi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterTindakanPenunjang',
        path: '/master/tindakan/penunjang',
        component: () => import('@/Pages/MasterData/TindakanPenunjang'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterPaketTindakan',
        path: '/master/paket/tindakan',
        component: () => import('@/Pages/MasterData/PaketTindakan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    
    {
        name: 'masterRL',
        path: '/master/rl',
        component: () => import('@/Pages/MasterData/LaporanRL'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect : '/master/rl/group',
        children:[
            {
                name: 'rlgroup',
                path: 'group',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/LaporanRL/GroupRL'),
            },
            {
                name: 'rlcode',
                path: 'kode/:groupId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/LaporanRL/KodeRL'),
            }
        ]
    },
    {
        name: 'masterWilayah',
        path: '/master/wilayah',
        component: () => import('@/Pages/MasterData/Wilayah'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'propinsi' },
        children:[
            {
                name: 'propinsi',
                path: 'propinsi',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Wilayah/Propinsi/index.vue'),
            },
            {
                name: 'kabupaten',
                path: 'kabupaten/:propinsiId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Wilayah/Kabupaten/index.vue'),
            },
            {
                name: 'kecamatan',
                path: 'kecamatan/:kabupatenId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Wilayah/Kecamatan/index.vue'),
            },
            {
                name: 'kelurahan',
                path: 'kelurahan/:kecamatanId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Wilayah/Kelurahan/index.vue'),
            }
        ]
    },
    {
        name: 'masterIcd',
        path: '/master/icd',
        component: () => import('@/Pages/MasterData/ICD'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterDTD',
        path: '/master/dtd',
        component: () => import('@/Pages/MasterData/Dtd'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterReferensiProduk',
        path: '/master/referensi/produk',
        component: () => import('@/Pages/MasterData/GolonganProduk'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterAkunProduk',
        path: '/master/akun/produk',
        component: () => import('@/Pages/MasterData/AkunProduk'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'masterSigna',
        path: '/master/signa',
        component: () => import('@/Pages/MasterData/Signa'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'masterProdukMedis',
        path: '/master/produk/medis',
        component: () => import('@/Pages/MasterData/Produk/ItemMedis'),
        meta: { requiresAuth: true, layout:'AppLayout' },        
        redirect: { name: 'listItemMedis' },
        children:[
            {
                name: 'listItemMedis',
                path: '',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Produk/ItemMedis/ListItemMedis.vue'),
            },
            {
                name: 'dataItemMedis',
                path: ':produkId',
                props : true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Produk/ItemMedis/FormItemMedis.vue'),
            },
        ]
    },

    {
        name: 'masterProdukUmum',
        path: '/master/produk/umum',
        component: () => import('@/Pages/MasterData/Produk/ItemUmum'),
        meta: { requiresAuth: true, layout:'AppLayout' },        
        redirect: { name: 'listItemUmum' },
        children:[
            {
                name: 'listItemUmum',
                path: '',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Produk/ItemUmum/ListItemUmum.vue'),
            },
            {
                name: 'dataItemUmum',
                path: ':produkId',
                props : true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Produk/ItemUmum/FormItemUmum.vue'),
            },
        ]
    },
    {
        name: 'masterProdukDapur',
        path: '/master/produk/dapur',
        component: () => import('@/Pages/MasterData/Produk/ItemDapur'),
        meta: { requiresAuth: true, layout:'AppLayout' },        
        redirect: { name: 'listItemDapur' },
        children:[
            {
                name: 'listItemDapur',
                path: '',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Produk/ItemDapur/ListItemDapur.vue'),
            },
            {
                name: 'dataItemDapur',
                path: ':produkId',
                props : true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Produk/ItemDapur/FormItemDapur.vue'),
            },
        ]
    },
    {
        name: 'masterDepo',
        path: '/master/depo',
        component: () => import('@/Pages/MasterData/Depo'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterKsm',
        path: '/master/ksm',
        component: () => import('@/Pages/MasterData/KSM'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterSpesialisasi',
        path: '/master/spesialisasi',
        component: () => import('@/Pages/MasterData/Spesialisasi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterDokter',
        path: '/master/dokter',
        component: () => import('@/Pages/MasterData/Dokter'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listDokter' },
        children:[
            {
                name: 'listDokter',
                path: '',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Dokter/main'),
            },
            {
                name: 'editDataDokter',
                path: ':dokterId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Dokter/form/index.vue'),
            },
        ]
    },
    {
        name: 'masterKelas',
        path: '/master/kelas',
        component: () => import('@/Pages/MasterData/Kelas'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterUnitPelayanan',
        path: '/master/unit/pelayanan',
        component: () => import('@/Pages/MasterData/UnitPelayanan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listUnitPelayanan' },
        children:[
            {
                name: 'listUnitPelayanan',
                path: 'list',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/UnitPelayanan/main'),
            },
            {
                name: 'dataUnitPelayanan',
                path: 'data/:unitId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/UnitPelayanan/form'),
            },
        ]
    },
    {
        name: 'masterRuang',
        path: '/master/ruang',
        component: () => import('@/Pages/MasterData/Ruang'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listRuang' },
        children:[
            {
                name: 'listRuang',
                path: '',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Ruang/main'),
            },
            {
                name: 'dataRuang',
                path: ':ruangId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Ruang/form'),
            },
        ]
    },
    
    {
        name: 'masterKelompokTindakan',
        path: '/master/kelompok/tindakan',
        component: () => import('@/Pages/MasterData/KelompokTindakan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    
    {
        name: 'masterJadwalDokter',
        path: '/master/jadwal/dokter',
        component: () => import('@/Pages/MasterData/JadwalDokter'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'masterTemplateLab',
        path: '/master/template/lab',
        component: () => import('@/Pages/MasterData/TemplateLab'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterTemplateRad',
        path: '/master/template/rad',
        component: () => import('@/Pages/MasterData/TemplateRadiologi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterItemLab',
        path: '/master/item/lab',
        component: () => import('@/Pages/MasterData/ItemLaboratorium'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },


    {
        name: 'masterMakanan',
        path: '/master/makanan',
        component: () => import('@/Pages/MasterData/Makanan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterDietPasien',
        path: '/master/diet',
        component: () => import('@/Pages/MasterData/DietPasien'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterKelompokMakanan',
        path: '/master/kelompok/makanan',
        component: () => import('@/Pages/MasterData/KelompokMakanan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterMenu',
        path: '/master/menu',
        component: () => import('@/Pages/MasterData/Menu'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterMenuMakanan',
        path: '/master/mealsmenu',
        component: () => import('@/Pages/MasterData/MenuMakanan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterKelompokMenu',
        path: '/master/kelompok/menu',
        component: () => import('@/Pages/MasterData/KelompokMenu'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterTemplateGizi',
        path: '/master/template/gizi',
        component: () => import('@/Pages/MasterData/TemplateGizi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'DistribusiGizi',
        path: '/master/distribusi/gizi',
        component: () => import('@/Pages/MasterData/DistribusiGizi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterDetailDistribusiGizi',
        path: '/master/distribusi/detail/gizi',
        component: () => import('@/Pages/MasterData/DistribusiGiziDetail'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterInformedConsent',
        path: '/master/informed',
        component: () => import('@/Pages/MasterData/InformedConsent/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listTemplateInformed' },
        children:[
            {
                name: 'listTemplateInformed',
                path: 'list',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/InformedConsent/main'),
                redirect: { name: 'listTemplateInformedIndex' },
                children : [
                    {
                        name: 'listTemplateInformedIndex',
                        path: '',
                        meta: { requiresAuth: true, layout:'AppLayout' },
                        component: () => import('@/Pages/MasterData/InformedConsent/main/InformConsentList.vue'),
                    },
                    {
                        name: 'listTemplateInformedItem',
                        path: 'item',
                        meta: { requiresAuth: true, layout:'AppLayout' },
                        component: () => import('@/Pages/MasterData/InformedConsentDetail/main'),
                    },           
                    {
                        name: 'listTemplateInformedUnit',
                        path: 'map/unit',
                        meta: { requiresAuth: true, layout:'AppLayout' },
                        component: () => import('@/Pages/MasterData/InformedConsentUnitMapping/index.vue'),
                    },       
                ]
            },            

            {
                name: 'dataInformed',
                path: ':templateId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/InformedConsent/form'),
            },


        ]
    },
    {
        name: 'masterInformedConsentDetail',
        path: '/master/informedDetail',
        component: () => import('@/Pages/MasterData/InformedConsentDetail'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listTemplateInformedDetail' },
        children:[
            {
                name: 'listTemplateInformedDetail',
                path: '',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/InformedConsentDetail/main'),
            },
            {
                name: 'dataInformedDetail',
                path: ':detailId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/InformedConsentDetail/form'),
            },
        ]
    },
    {
        name: 'masterPasien',
        path: '/master/pasien',
        component: () => import('@/Pages/MasterData/Pasien'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listPasien' },
        children:[            
            {
                name: 'formPasien',
                path: 'form/:pasienId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Pasien/form'),
            },
            {
                name: 'listPasien',
                path: 'list',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/MasterData/Pasien/main'),
            },
        ]
    },
    
]

export default masterRouter