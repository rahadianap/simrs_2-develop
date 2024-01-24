const penunjangRouter = [
    {
        name: 'penunjangLab',
        path: '/penunjang/lab',
        component: () => import('@/Pages/Penunjang/Laboratorium'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'penunjangRadiologi',
        path: '/penunjang/rad',
        component: () => import('@/Pages/Penunjang/Radiologi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'penunjangHemo',
        path: '/penunjang/hemo',
        component: () => import('@/Pages/Penunjang/Hemodialisa'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'penunjangFisio',
        path: '/penunjang/fisio',
        component: () => import('@/Pages/Penunjang/Fisioterapi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'penunjangPatologi',
        path: '/penunjang/patologi',
        component: () => import('@/Pages/Penunjang/Patologi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },


    /**
     * BANK DARAH 
     * */
     {
        name: 'referensiBankDarah',
        path: '/bankdarah/referensi',
        component: () => import('@/Pages/Penunjang/BankDarah/Referensi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'penunjangBankDarah',
        path: '/penunjang/bankdarah',
        component: () => import('@/Pages/Penunjang/BankDarah'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'PersediaanBankDarah',
        path: '/bankdarah/info',
        component: () => import('@/Pages/Penunjang/BankDarah/Persediaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'PenerimaanBankDarah',
        path: '/bankdarah/penerimaan',
        component: () => import('@/Pages/Penunjang/BankDarah/Penerimaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'PermintaanBankDarah',
        path: '/bankdarah/permintaan',
        component: () => import('@/Pages/Penunjang/BankDarah/Pengiriman'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'PemusnahanBankDarah',
        path: '/bankdarah/pemusnahan',
        component: () => import('@/Pages/Penunjang/BankDarah/Pemusnahan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },    
    {
        name: 'MonitoringBankDarah',
        path: '/bankdarah/monitoring',
        component: () => import('@/Pages/Penunjang/BankDarah/Persediaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },    
   

    {
        name: 'penunjangFarmasi',
        path: '/penunjang/farmasi',
        component: () => import('@/Pages/Penunjang/Farmasi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'penunjangApotek',
        path: '/penunjang/apotek',
        component: () => import('@/Pages/Penunjang/Apotek'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'penunjangAmbulance',
        path: '/penunjang/ambulance',
        component: () => import('@/Pages/Penunjang/Ambulance'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'penunjangPemulasaraan',
        path: '/penunjang/pemulasaraan',
        component: () => import('@/Pages/Penunjang/Pemulasaraan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    /**
     * LAUNDRY (LINEN)
     */
    {
        name: 'penunjangLaundry',
        path: '/penunjang/laundry',
        component: () => import('@/Pages/Penunjang/Laundry'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'persediaanLaundry',
        path: '/laundry/info',
        component: () => import('@/Pages/Penunjang/Laundry/Persediaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'penerimaanLaundry',
        path: '/laundry/penerimaan',
        component: () => import('@/Pages/Penunjang/Laundry/Penerimaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'pengirimanLaundry',
        path: '/laundry/pengiriman',
        component: () => import('@/Pages/Penunjang/Laundry/Pengiriman'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'pemusnahanLinen',
        path: '/laundry/pemusnahan',
        component: () => import('@/Pages/Penunjang/Laundry/Pemusnahan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    
    /**
     * CSSD
     */
    {
        name: 'penunjangCssd',
        path: '/penunjang/cssd',
        component: () => import('@/Pages/Penunjang/CSSD'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'infoCssd',
        path: '/cssd/info',
        component: () => import('@/Pages/Penunjang/CSSD/Persediaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'penerimaanCssd',
        path: '/cssd/penerimaan',
        component: () => import('@/Pages/Penunjang/CSSD/Penerimaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'distribusiCssd',
        path: '/cssd/pengiriman',
        component: () => import('@/Pages/Penunjang/CSSD/Pengiriman'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'pemusnahanCssd',
        path: '/cssd/pemusnahan',
        component: () => import('@/Pages/Penunjang/CSSD/Pemusnahan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'penunjangDiet',
        path: '/pasien/diet',
        component: () => import('@/Pages/Penunjang/DietPasien'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'mealOrder',
        path: '/mealorder',
        component: () => import('@/Pages/Penunjang/DietPasien/MealOrder'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    
]
export default penunjangRouter