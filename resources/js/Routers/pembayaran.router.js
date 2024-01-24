const pembayaranRouter = [
    {
        name: 'pelayananBilling',
        path: '/pelayanan/billing',
        component: () => import('@/Pages/Pelayanan/Billing'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect : { name: 'listBilling' },
        children : [
            {
                name: 'listBilling',
                path: 'list',
                component: () => import('@/Pages/Pelayanan/Billing/List'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'dataBilling',
                path: ':regId',
                component: () => import('@/Pages/Pelayanan/Billing/Data'),
                meta: { requiresAuth: true, layout:'AppLayout' },
                props: true,
            },
        ]
    },
    // {
    //     name: 'pendaftaranPoli',
    //     path: '/pendaftaran/poli',
    //     component: () => import('@/Pages/RawatJalan/Pendaftaran'),
    //     meta: { requiresAuth: true, layout:'AppLayout' },
    //     redirect: { name: 'listPendaftaran' },
    //     children:[
    //         {
    //             name: 'listPendaftaran',
    //             path: 'antrian/list',
    //             component: () => import('@/Pages/RawatJalan/Pendaftaran/AntrianPoli'),
    //             meta: { requiresAuth: true, layout:'AppLayout' },
    //         },
    //         {
    //             name: 'listBookingPoli',
    //             path: 'booking/list',
    //             component: () => import('@/Pages/RawatJalan/Pendaftaran/BookingPoli'),
    //             meta: { requiresAuth: true, layout:'AppLayout' },
    //         },
    //         {
    //             name: 'listMasterPasien',
    //             path: 'pasien/list',
    //             component: () => import('@/Pages/RawatJalan/Pendaftaran/ListPasien'),
    //             meta: { requiresAuth: true, layout:'AppLayout' },
    //         },
    //     ]
    // },
]
export default pembayaranRouter;