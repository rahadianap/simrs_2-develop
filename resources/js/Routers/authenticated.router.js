const authenticatedRouter = [    
    {
        name: 'home',
        path: '/home',
        component: () => import('@/Pages/Home/index'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'profile',
        path: '/profile',
        component: () => import('@/Pages/Profile/index'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'dashboard',
        path: '/dashboard',
        component: () => import('@/Pages/Dashboard/index'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
]

export default authenticatedRouter