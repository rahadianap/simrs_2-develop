const praktekDokterRouter = [

    {
        name: 'PraktekDokter',
        path: '/praktek/dokter',
        component: () => import('@/Pages/PraktekDokter/Dashboard/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'layananPraktekDokter',
        path: '/praktek/dokter/layanan',
        component: () => import('@/Pages/PraktekDokter/LayananDokter/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },

    },
    {
        name: 'layananPraktekDokter',
        path: '/praktek/dokter/layanan',
        component: () => import('@/Pages/PraktekDokter/LayananDokter/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listPasienPraktekDokter' },
        children : [
            {
                name: 'listPasienPraktekDokter',
                path: 'pasien',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/PraktekDokter/LayananDokter/tabs/MasterPasien/index.vue'),
            },
            {
                name: 'listAntrianPraktekDokter',
                path: 'antrian',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/PraktekDokter/LayananDokter/tabs/Antrian/index.vue'),
            },
            {
                name: 'listPembayaranPraktekDokter',
                path: 'pembayaran',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/PraktekDokter/LayananDokter/tabs/RiwayatPembayaran/index.vue'),
            },
        ]
    },
    {
        name: 'kasPraktekDokter',
        path: '/praktek/dokter/kas',
        component: () => import('@/Pages/PraktekDokter/PencatatanKas/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },    
    // {
    //     name: 'laporanPraktekDokter',
    //     path: '/praktek/dokter/laporan',
    //     component: () => import('@/Pages/PraktekDokter/Laporan/index.vue'),
    //     meta: { requiresAuth: true, layout:'AppLayout' },
    // },

    {
        name: 'laporanPraktekDokter',
        path: '/praktek/dokter/laporan',
        component: () => import('@/Pages/PraktekDokter/Laporan/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'lapPendapatanPraktekDokter' },
        children : [
            {
                name: 'lapPendapatanPraktekDokter',
                path: 'pendapatan',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/PraktekDokter/Laporan/tabs/Pendapatan/index.vue'),
            },
            {
                name: 'lapKunjunganPraktekDokter',
                path: 'kunjungan',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/PraktekDokter/Laporan/tabs/Kunjungan/index.vue'),
            },
            {
                name: 'lapKasPraktekDokter',
                path: 'kas',
                meta: { requiresAuth: true, layout:'AppLayout' },
                component: () => import('@/Pages/PraktekDokter/Laporan/tabs/LaporanCatatanKas/index.vue'),
            },
        ]
    },
]

export default praktekDokterRouter