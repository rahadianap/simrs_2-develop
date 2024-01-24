const farmasiRouter = [
    {
        name: 'resep',
        path: '/prescriptions',
        component: () => import('@/Pages/Farmasi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'resepIndex' },
        children : [
            {
                name : 'resepIndex',
                path : 'index',
                component: () => import('@/Pages/Farmasi/FarmasiHistory/index.vue'),
            },
            {
                name : 'dataResep',
                path : ':trxType/:trxId',
                component : () => import('@/Pages/Farmasi/FarmasiForm/index.vue'),
                props: true,
            },
            {
                name : 'viewResep',
                path : 'view/:trxId',
                component : () => import('@/Pages/Farmasi/FarmasiView/index.vue'),
                props: true,
            },
        ]
    },
    {
        name: 'listApotek',
        path: '/pharmacy',
        component: () => import('@/Pages/Apotek'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'apotekIndex' },
        children : [
            {
                name : 'apotekIndex',
                path : 'index',
                component: () => import('@/Pages/Apotek/ApotekHistory/index.vue'),
            },
            {
                name : 'formApotek',
                path : 'form/:trxId',
                component : () => import('@/Pages/Apotek/FormApotek/index.vue'),
                props: true,
            },
            
            {
                name : 'viewApotek',
                path : 'view/:trxId',
                component : () => import('@/Pages/Apotek/ApotekView/index.vue'),
                props: true,
            },
        ]
    },
]

export default farmasiRouter