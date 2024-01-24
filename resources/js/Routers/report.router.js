const reportRouter = [
    {
        name: 'listReport',
        path: '/report',
        component: () => import('@/Pages/Report'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
]

export default reportRouter