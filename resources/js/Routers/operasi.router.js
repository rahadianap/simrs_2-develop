const operasiRouter = [
    {
        name: 'pendaftaranOperasi',
        path: '/pendaftaran/operasi',
        component: () => import('@/Pages/Penunjang/Operasi/Pendaftaran'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listPendaftaranOperasi' },
        children:[
            {
                name: 'listPendaftaranOperasi',
                path: 'list',
                component: () => import('@/Pages/Penunjang/Operasi/Pendaftaran/ListPasienOperasi'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'formPendaftaranOperasi',
                path: ':tipe/:dataId',
                component: () => import('@/Pages/Penunjang/Operasi/Pendaftaran/FormOperasi'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
        ]
    },
    {
        name: 'tindakanOperasi',
        path: '/pelayanan/bedah',
        component: () => import('@/Pages/Penunjang/Operasi/Tindakan'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listPasienOperasi' },
        children:[
            {
                name: 'listPasienOperasi',
                path: 'list',
                component: () => import('@/Pages/Penunjang/Operasi/Tindakan/ListPasienOperasi.vue'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'realisasiOperasi',
                path: 'realisasi/:trxId',
                component: () => import('@/Pages/Penunjang/Operasi/Tindakan/FormRealisasiOps.vue'),
                meta: { requiresAuth: true, layout:'AppLayout' },
                props : true,
            },
        ]
    },
]

export default operasiRouter