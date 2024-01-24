
const guestRouter = [
    {
        name: 'index',
        path: '/',
        component: () => import('@/Pages/WelcomePage'),
        meta: { requiresAuth: false, layout:'GuestLayout' },
        redirect: { name: 'login' },
        children:[
            {
                name: 'signup',
                path: '/signup',
                component: () => import('@/Pages/Auth/Signup'),
            },
            {
                name: 'signupverification',
                path: '/verification',
                component: () => import('@/Pages/Auth/SignupVerification'),
            },
            {
                name: 'login',
                path: '/login',
                component: () => import('@/Pages/Auth/Login'),
            },
            {
                name: 'forgetpassword',
                path: '/forget/password',
                component: () => import('@/Pages/Auth/ResetPassword'),
            },
            {
                name: 'resetverification',
                path: '/reset/verification',
                component: () => import('@/Pages/Auth/ResetVerification'),
            }           
        ]
    },
]

export default guestRouter
