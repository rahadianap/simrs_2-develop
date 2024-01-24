const giziRouter = [
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
        name: 'penunjangGizi',
        path: '/penunjang/gizi',
        component: () => import('@/Pages/Penunjang/DietPasien'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'referensiGizi',
        path: '/gizi/referensi',
        component: () => import('@/Pages/Penunjang/DietPasien/Referensi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'monitoringGizi',
        path: '/gizi/monitoring',
        component: () => import('@/Pages/Penunjang/DietPasien/Monitoring'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
]

export default giziRouter