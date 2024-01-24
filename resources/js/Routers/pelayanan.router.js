const pelayananRouter = [
    {
        name: 'pelayananPoli',
        path: '/pelayanan/poli',
        component: () => import('@/Pages/RawatJalan/Pemeriksaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listPasienPoli' },
        children : [
            {
                name: 'listPasienPoli',
                path: 'list',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/RawatJalan/Pemeriksaan/ListPasienPoli/index.vue'),
            },
            {
                name: 'dataPasienPoli',
                path: ':trxId',
                component: () => import('@/Pages/RawatJalan/Pemeriksaan/ListPemeriksaan/index.vue'),
                props : true,
            },
            {
                name: 'dataLabPoli',
                path: ':trxId/lab/:labId',
                component: () => import('@/Pages/RawatJalan/Pemeriksaan/SubListLaboratorium/index.vue'),
                props : true,
            },
            {
                name: 'dataRadPoli',
                path: ':trxId/rad/:radId',
                component: () => import('@/Pages/RawatJalan/Pemeriksaan/SubListRadiologi/index.vue'),
                props : true,
            },

            {
                name: 'dataTindakanPoli',
                path: ':trxId/tindakan/:actId',
                component: () => import('@/Pages/RawatJalan/Pemeriksaan/SubFormTindakan/index.vue'),
                props : true,
            },

            {
                name: 'dataResepPoli',
                path: ':trxId/resep/:resepId',
                component: () => import('@/Pages/RawatJalan/Pemeriksaan/SubFormResep/index.vue'),
                props : true,
            },
        ]
    },

    {
        name: 'pelayananIgd',
        path: '/pelayanan/igd',
        component: () => import('@/Pages/Pelayanan/IGD'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'pelayananInap',
        path: '/pelayanan/inap',
        component: () => import('@/Pages/RawatInap/Pemeriksaan/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listPasienInap' },
        children : [
            {
                name: 'listPasienInap',
                path: 'list',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/RawatInap/Pemeriksaan/ListPasienInap/index.vue'),
            },
            {
                name: 'dataPasienRawatInap',
                path: ':trxId',
                component: () => import('@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/index.vue'),
                props : true,
            },
            {
                name: 'dataTindakanInap',
                path: ':trxId/tindakan/:actId',
                component: () => import('@/Pages/RawatInap/Pemeriksaan/SubFormTindakanInap/index.vue'),
                props : true,
            },

            {
                name: 'dataLabInap',
                path: ':trxId/lab/:labId',
                component: () => import('@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListLabInap/FormSubListLabInap.vue'),
                props : true,
            },
            {
                name: 'dataRadInap',
                path: ':trxId/rad/:radId',
                component: () => import('@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListRadInap/FormSubListRadInap.vue'),
                props : true,
            },            
        ]

    },
    // {
    //     name: 'pelayananBedah',
    //     path: '/pelayanan/bedah',
    //     component: () => import('@/Pages/Pelayanan/BedahSentral'),
    //     meta: { requiresAuth: true, layout:'AppLayout' },
    // },
    {
        name: 'pelayananMcu',
        path: '/pelayanan/mcu',
        component: () => import('@/Pages/Pelayanan/MCU'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'pelayananKeperawatan',
        path: '/pelayanan/keperawatan',
        component: () => import('@/Pages/Pelayanan/Keperawatan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    // {
    //     name: 'pelayananBilling',
    //     path: '/pelayanan/billing',
    //     component: () => import('@/Pages/Pelayanan/Billing'),
    //     meta: { requiresAuth: true, layout:'AppLayout' },
    // },

]
export default pelayananRouter