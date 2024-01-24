const labRouter = [

    {
        name: 'labReferensi',
        path: '/lab/referensi',
        component: () => import('@/Pages/Penunjang/Laboratorium/Referensi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'masterItemHasil',
        path: '/lab/master/item',
        component: () => import('@/Pages/Penunjang/Laboratorium/Referensi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'labMasterPemeriksaan',
        path: '/lab/master/pemeriksaan',
        component: () => import('@/Pages/Penunjang/Laboratorium/MasterPemeriksaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'labTemplate',
        path: '/lab/template',
        component: () => import('@/Pages/MasterData/TemplateLab'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'labPemeriksaan',
        path: '/lab/pemeriksaan',
        component: () => import('@/Pages/Penunjang/Laboratorium/Pemeriksaan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },

    {
        name: 'labRegistrasi',
        path: '/lab/registrasi',
        component: () => import('@/Pages/Penunjang/Laboratorium/Pendaftaran'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    
]

export default labRouter