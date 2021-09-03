import ClientAppointment from '../components/Cabinet/Client/ClientAppointment';

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
        path: '/employee/cabinet',
        component: () => import('../components/Layouts/Cabinet'),
        children: [
            {
                path: '',
                name: 'EmployeeInfo',
                component: () => import('../components/Cabinet/Common/UserInfo'),
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'edit',
                name: 'EmployeeEdit',
                component: () => import('../components/Cabinet/Employee/EmployeeEdit'),
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'orders',
                name: 'EmployeeOrdersList',
                component: () => import('../components/Cabinet/Common/Order/OrdersList'),
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'orders/:id',
                name: 'EmployeeOrderDetails',
                component: () => import('../components/Cabinet/Common/Order/OrderDetails.vue'),
                props: true,
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'edit-password',
                name: 'EmployeePasswordEdit',
                component: () => import('../components/Cabinet/Common/PasswordEdit'),
                meta: {
                    requiresAuth: true,
                },
            },
        ],
    },
    {
        path: '/client/cabinet',
        component: () => import('../components/Layouts/Cabinet'),
        children: [
            {
                path: '',
                name: 'ClientInfo',
                component: () => import('../components/Cabinet/Common/UserInfo'),
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'edit',
                name: 'ClientEdit',
                component: () => import('../components/Cabinet/Client/ClientEdit'),
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'orders',
                name: 'ClientOrdersList',
                component: () => import('../components/Cabinet/Common/Order/OrdersList'),
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'orders/:id',
                name: 'ClientOrderDetails',
                component: () => import('../components/Cabinet/Common/Order/OrderDetails.vue'),
                props: true,
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'appointment',
                name: 'Appointment',
                component: ClientAppointment,
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'edit-password',
                name: 'ClientPasswordEdit',
                component: () => import('../components/Cabinet/Common/PasswordEdit'),
                meta: {
                    requiresAuth: true,
                },
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
