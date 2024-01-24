const pendaftaranRouter = [
    {
        name: 'pendaftaranPoli',
        path: '/pendaftaran/poli',
        component: () => import('@/Pages/RawatJalan/Pendaftaran'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listPendaftaran' },
        children:[
            {
                name: 'listPendaftaran',
                path: 'antrian/list',
                component: () => import('@/Pages/RawatJalan/Pendaftaran/AntrianPoli'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'listBookingPoli',
                path: 'booking/list',
                component: () => import('@/Pages/RawatJalan/Pendaftaran/BookingPoli'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'listMasterPasien',
                path: 'pasien/list',
                component: () => import('@/Pages/RawatJalan/Pendaftaran/ListPasien'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
        ]
    },

    {
        name: 'formBookingPoli',
        path: '/booking/poli/:tipe/:dataId',
        component: () => import('@/Pages/RawatJalan/Pendaftaran/FormBooking'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'formAntrianPoli',
        path: '/antrian/poli/:tipe/:dataId',
        component: () => import('@/Pages/RawatJalan/Pendaftaran/FormAntrian'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    
    {
        name: 'pendaftaranInap',
        path: '/pendaftaran/inap',
        component: () => import('@/Pages/RawatInap/Admisi'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listAdmisi' },
        children:[ 
            {
                name: 'listAdmisi',
                path: 'list',
                component: () => import('@/Pages/RawatInap/Admisi/ListAdmisi'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'formAdmisi',
                path: 'form/:trxId',
                component: () => import('@/Pages/RawatInap/Admisi/FormAdmisi'),
                meta: { requiresAuth: true, layout:'AppLayout' },
                props : true,
            },
            {
                name: 'viewAdmisi',
                path: 'view/:trxId',
                component: () => import('@/Pages/RawatInap/Admisi/ViewAdmisi'),
                meta: { requiresAuth: true, layout:'AppLayout' },
                props : true,
            },
        ]
    },

    {
        name: 'pendaftaranMcu',
        path: '/pendaftaran/mcu',
        component: () => import('@/Pages/Pendaftaran/MCU'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    // {
    //     name: 'pendaftaranOperasi',
    //     path: '/pendaftaran/operasi',
    //     component: () => import('@/Pages/Penunjang/Operasi/Pendaftaran'),
    //     meta: { requiresAuth: true, layout:'AppLayout' },
    // },
    {
        name: 'pendaftaranPenunjang',
        path: '/pendaftaran/penunjang',
        component: () => import('@/Pages/Pendaftaran/Penunjang'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
]

export default pendaftaranRouter