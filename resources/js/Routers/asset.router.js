const assetRouter = [
    {
        name: 'asset',
        path: '/asset',
        component: () => import('@/Pages/Asset'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'assetoverview',
        path: '/asset/overview',
        component: () => import('@/Pages/AssetOverview'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'assetmaintenance',
        path: '/asset/maintenance',
        component: () => import('@/Pages/AssetMaintenance'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'assetmaintenancetechnician',
        path: '/asset/maintenance/technician',
        component: () => import('@/Pages/AssetMaintenanceTechnician'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
//     {
//         name: 'asset',
//         path: '/asset',
//         component: () => import('@/Pages/Asset'),
//         meta: { requiresAuth: true, layout:'AppLayout' },
//     },
]

export default assetRouter