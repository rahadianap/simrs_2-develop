const kioskRouter = [
    {
        name: 'kioskAntrian',
        path: '/kiosk/antrian',
        component: () => import('@/Pages/Kiosk/Antrian'),
        meta: { requiresAuth: true, layout:'KioskLayout' },
    },
    
]

export default kioskRouter