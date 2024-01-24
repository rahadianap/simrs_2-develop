const patologiRouter = [

    {
        name: 'paReferensi',
        path: '/pa/referensi',
        component: () => import('@/Pages/Penunjang/Patologi/Referensi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'paMasterPemeriksaan',
        path: '/pa/master/pemeriksaan',
        component: () => import('@/Pages/Penunjang/Patologi/MasterPemeriksaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'paTemplate',
        path: '/pa/template',
        component: () => import('@/Pages/MasterData/TemplatePatologi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'paPemeriksaan',
        path: '/pa/pemeriksaan',
        component: () => import('@/Pages/Penunjang/Patologi/Pemeriksaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },    
    {
        name: 'paRegistrasi',
        path: '/pa/registrasi',
        component: () => import('@/Pages/Penunjang/Patologi/Pendaftaran'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
]

export default patologiRouter