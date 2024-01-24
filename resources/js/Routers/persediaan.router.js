const persediaanRouter = [
    {
        name: 'referensiPersediaan',
        path: '/persediaan/referensi',
        component: () => import('@/Pages/Persediaan/Referensi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterPemasok',
        path: '/master/pemasok',
        component: () => import('@/Pages/MasterData/Pemasok'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'produkPersediaan',
        path: '/persediaan/produk',
        component: () => import('@/Pages/Persediaan/Produk'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'depoPersediaan',
        path: '/persediaan/depo',
        component: () => import('@/Pages/MasterData/Depo'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    
    {
        name: 'stockOpname',
        path: '/persediaan/opname',
        component: () => import('@/Pages/Persediaan/Opname'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listOpname' },
        children:[
            {
                name: 'listOpname',
                path: 'list',
                component: () => import('@/Pages/Persediaan/Opname/ListOpname/index.vue'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'formOpname',
                path: ':opnameId',
                component: () => import('@/Pages/Persediaan/Opname/FormOpname/index.vue'),
                meta: { requiresAuth: true, layout:'AppLayout' },
                props :true
            },
            {
                name: 'viewOpname',
                path: 'view/:opnameId',
                component: () => import('@/Pages/Persediaan/Opname/PreviewOpname/index.vue'),
                meta: { requiresAuth: true, layout:'AppLayout' },
                props :true
            },
        ]
    },


    {
        name: 'stockAdjustment',
        path: '/persediaan/penyesuaian',
        component: () => import('@/Pages/Persediaan/Adjustment'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'pengeluaranPersediaan',
        path: '/persediaan/pengeluaran',
        component: () => import('@/Pages/Persediaan/Pengeluaran'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'produksiPersediaan',
        path: '/persediaan/produksi',
        component: () => import('@/Pages/Persediaan/Produksi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'distribusiPersediaan',
        path: '/persediaan/distribusi',
        component: () => import('@/Pages/Persediaan/Distribusi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'permintaanPembelian',
        path: '/persediaan/permintaan',
        component: () => import('@/Pages/Persediaan/PurchaseRequest'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },    
    {
        name: 'pembelianPersediaan',
        path: '/persediaan/pembelian',
        component: () => import('@/Pages/Persediaan/PurchaseOrder'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'penerimaanPersediaan',
        path: '/persediaan/penerimaan',
        component: () => import('@/Pages/Persediaan/PurchaseReceive'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'pengembalianPembelian',
        path: '/persediaan/pengembalian',
        component: () => import('@/Pages/Persediaan/PurchaseReturn'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'hibahPersediaan',
        path: '/persediaan/hibah',
        component: () => import('@/Pages/Persediaan/Hibah'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'beliLangsungPersediaan',
        path: '/persediaan/belilangsung',
        component: () => import('@/Pages/Persediaan/PembelianLangsung'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'pengelolaanPengadaan',
        path: '/persediaan/maintenance',
        component: () => import('@/Pages/Persediaan/PengelolaanPengadaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'pemusnahanPersediaan',
        path: '/persediaan/pemusnahan',
        component: () => import('@/Pages/Persediaan/Pemusnahan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    
]
export default persediaanRouter