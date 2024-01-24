const satusehatRouter = [
    {
        name: 'satusehatPractitioner',
        path: '/satusehat/practitioner',
        component: () => import('@/Pages/SatuSehat/Practitioner'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'satusehatPatient',
        path: '/satusehat/patient',
        component: () => import('@/Pages/SatuSehat/Patient'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'satusehatOrganization',
        path: '/satusehat/organization',
        component: () => import('@/Pages/SatuSehat/Organization'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
    {
        name: 'satusehatLocation',
        path: '/satusehat/location',
        component: () => import('@/Pages/SatuSehat/Location'),
        meta: { requiresAuth: true, layout:'AppLayout' },
    },
]

export default satusehatRouter