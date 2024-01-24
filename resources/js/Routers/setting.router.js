const settingRouter = [
    {
        name: 'role',
        path: '/pengaturan/role',
        component: () => import('@/Pages/Pengaturan/Role'),
        meta: { requiresAuth: true, layout:'AppLayout' }
    },
    {
        name: 'pengguna',
        path: '/pengaturan/pengguna',
        component: () => import('@/Pages/Pengaturan/Pengguna'),
        meta: { requiresAuth: true, layout:'AppLayout' }
    },
    {
        name: 'parameter',
        path: '/pengaturan/parameter',
        component: () => import('@/Pages/Pengaturan/Parameter'),
        meta: { requiresAuth: true, layout:'AppLayout' }
    },
    {
        name: 'tempatkerja',
        path: '/pengaturan/tempatkerja',
        component: () => import('@/Pages/Pengaturan/TempatKerja'),
        meta: { requiresAuth: true, layout:'AppLayout' }
    },
    {
        name: 'templatemaster',
        path: '/pengaturan/templatemaster',
        component: () => import('@/Pages/Pengaturan/TemplateMaster'),
        meta: { requiresAuth: true, layout:'AppLayout' }
    },
]

export default settingRouter