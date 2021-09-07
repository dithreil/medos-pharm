export default [
    {
        path: '/',
        component: () => import('../components/Layouts/Authentication'),
        children: [
            {
                path: '',
                name: 'NotAuthorized',
                component: () => import('../components/Authentication/NotAuthorized'),
                props: true,
            },
            {
                path: 'login/:userType',
                name: 'Authorization',
                component: () => import('../components/Authentication/Authorization'),
                props: true,
            },
            {
                path: 'registration',
                name: 'Registration',
                component: () => import('../components/Authentication/Registration'),
            },
            {
                path: 'password-restore/:userType',
                name: 'PasswordRestore',
                component: () => import('../components/Authentication/PasswordRestore'),
                props: true,
            },
        ],
    },
    {
        path: '/404',
        name: '404',
        component: () => import('../components/PageNotFound'),
    },
    {
        path: '*',
        redirect: '/404',
    },

];
