const bpjsRouter = [
    {
        name: 'bpjsCredential',
        path: '/bridging/credential',
        component: () => import('@/Pages/BridgingBpjs/Credential'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'bpjsRefWilayah',
        path: '/bridging/wilayah',
        component: () => import('@/Pages/BridgingBpjs/Wilayah'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'refpropinsi' },
        children:[
            {
                name: 'refpropinsi',
                path: 'propinsi',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/BridgingBpjs/Wilayah/Propinsi/index.vue'),
            },
            {
                name: 'refkabupaten',
                path: 'kabupaten/:propinsiId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/BridgingBpjs/Wilayah/Kabupaten/index.vue'),
            },
            {
                name: 'refkecamatan',
                path: 'kecamatan/:kabupatenId',
                props: true,
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/BridgingBpjs/Wilayah/Kecamatan/index.vue'),
            }
        ]
    },
    {
        name: 'bpjsRefSpesialistik',
        path: '/bridging/spesialistik',
        component: () => import('@/Pages/BridgingBpjs/Spesialistik'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'bpjsRefRuangRawat',
        path: '/bridging/ruang',
        component: () => import('@/Pages/BridgingBpjs/RuangRawat'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'bpjsRefCaraKeluar',
        path: '/bridging/cara/keluar',
        component: () => import('@/Pages/BridgingBpjs/CaraPulang'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'bpjsRefPascaPulang',
        path: '/bridging/pasca/pulang',
        component: () => import('@/Pages/BridgingBpjs/PascaPulang'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'bpjsRefPoli',
        path: '/bridging/poli',
        component: () => import('@/Pages/BridgingBpjs/UnitPelayanan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'bpjsRefDokter',
        path: '/bridging/dokter',
        component: () => import('@/Pages/BridgingBpjs/Dokter'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'bpjsRefDpjp',
        path: '/bridging/dpjp',
        component: () => import('@/Pages/BridgingBpjs/Dpjp'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'bpjsRefPrb',
        path: '/bridging/prb',
        component: () => import('@/Pages/BridgingBpjs/Prb'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'bpjsRujukan',
        path: '/bridging/rujukan',
        component: () => import('@/Pages/BridgingBpjs/Rujukan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'bpjsSep',
        path: '/bridging/sep',
        component: () => import('@/Pages/BridgingBpjs/Sep'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'bpjsMonitoring',
        path: '/bridging/monitoring',
        component: () => import('@/Pages/BridgingBpjs/Monitoring'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'bpjsPeserta',
        path: '/bridging/peserta',
        component: () => import('@/Pages/BridgingBpjs/Peserta'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'bpjsSuratKontrol',
        path: '/bridging/suratkontrol',
        component: () => import('@/Pages/BridgingBpjs/SuratKontrol'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

]

export default bpjsRouter