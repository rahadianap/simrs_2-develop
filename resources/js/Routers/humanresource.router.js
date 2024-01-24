const humanResourceRouter = [
    {
        name: 'masterkaryawan',
        path: '/karyawan',
        component: () => import('@/Pages/HumanResource/Karyawan/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listKaryawan' },
        children:[
            {
                name: 'listKaryawan',
                path: 'list',
                component: () => import('@/Pages/HumanResource/Karyawan/ListKaryawan/index.vue'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'detailKaryawan',
                path: 'detail/:karyawanId',
                props: true,
                component: () => import('@/Pages/HumanResource/Karyawan/DetailKaryawan/index.vue'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'formKaryawan',
                path: ':karyawanId',
                props: true,
                component: () => import('@/Pages/HumanResource/Karyawan/CreateKaryawan/index.vue'),
                meta: { requiresAuth: true, layout:'AppLayout' },
            },
            // {
            //     name: 'tambahKaryawan',
            //     path: 'baru',
            //     component: () => import('@/Pages/HumanResource/Karyawan/CreateKaryawan/index.vue'),
            //     meta: { requiresAuth: true, layout:'AppLayout' },
            // },
        ]
    },
    {
        name: 'masterjabatan',
        path: '/jabatan',
        component: () => import('@/Pages/HumanResource/JobRole/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
]

export default humanResourceRouter