const keuanganRouter = [

    {
        name: 'referensiKeuangan',
        path: '/referensi/keuangan',
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
        name: 'masterKelompokTagihan',
        path: '/master/kelompok/tagihan',
        component: () => import('@/Pages/MasterData/KelompokTagihan'),
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
        name: 'masterTindakan',
        path: '/master/tindakan',
        component: () => import('@/Pages/MasterData/Tindakan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterPaketTindakan',
        path: '/master/paket/tindakan',
        component: () => import('@/Pages/MasterData/PaketTindakan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterAkunProduk',
        path: '/master/akun/produk',
        component: () => import('@/Pages/MasterData/AkunProduk'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },    
    {
        name: 'masterCaraBayar',
        path: '/master/cara/bayar',
        component: () => import('@/Pages/MasterData/CaraBayar'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterHarga',
        path: '/master/harga',
        component: () => import('@/Pages/MasterData/Harga'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'kelompokHarga',
        path: '/kelompok/harga',
        component: () => import('@/Pages/MasterData/KelompokHarga'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
]

export default keuanganRouter