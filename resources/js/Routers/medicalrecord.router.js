const medrecRouter = [

    {
        name: 'medrec',
        path: '/medrec',
        component: () => import('@/Pages/MedicalRecord/index.vue'),
        meta: { requiresAuth: true, layout:'AppLayout' },
        redirect: { name: 'listPasienMedrec' },
        children : [
            {
                name : 'listPasienMedrec',
                path : 'list',
                component : () => import('@/Pages/MedicalRecord/MedicalRecordPasien/index.vue'),
                //meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name : 'listMedrec',
                path : ':pasienId',
                component: () => import('@/Pages/MedicalRecord/MedicalRecordList/index.vue'),
                props : true,
                //meta: { requiresAuth: true, layout:'AppLayout' },
            },
            {
                name: 'dataMedrec',
                path: 'data/:regId',
                component: () => import('@/Pages/MedicalRecord/MedicalRecordData/index.vue'),
                props : true,
                //meta: { requiresAuth: true, layout:'AppLayout' },
            },
        ]
    },
]

export default medrecRouter