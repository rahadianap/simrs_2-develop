const radiologiRouter = [

    {
        name: 'radReferensi',
        path: '/rad/referensi',
        component: () => import('@/Pages/Penunjang/Radiologi/Referensi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'radMasterPemeriksaan',
        path: '/rad/master/pemeriksaan',
        component: () => import('@/Pages/Penunjang/Radiologi/MasterPemeriksaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'radTemplate',
        path: '/rad/template',
        component: () => import('@/Pages/MasterData/TemplateRadiologi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'radPemeriksaan',
        path: '/rad/pemeriksaan',
        component: () => import('@/Pages/Penunjang/Radiologi/Pemeriksaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },    
    {
        name: 'radRegistrasi',
        path: '/rad/registrasi',
        component: () => import('@/Pages/Penunjang/Radiologi/Pendaftaran'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
]

export default radiologiRouter