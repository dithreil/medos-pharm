export default [
    {
        path: '/',
        name: 'Main',
        component: () => import('../pages/Main.vue'),
    },
    {
        path: '/clients/',
        name: 'ClientList',
        component: () => import('../pages/Client/ClientList.vue'),
    },
    {
        path: '/clients/create',
        name: 'ClientCreate',
        component: () => import('../pages/Client/ClientCreate.vue'),
    },
    {
        path: '/clients/:id',
        name: 'ClientDetails',
        component: () => import('../pages/Client/ClientDetails.vue'),
        props: true,
    },
    {
        path: '/clients/:id/edit',
        name: 'ClientEdit',
        component: () => import('../pages/Client/ClientEdit.vue'),
        props: true,
    },

    {
        path: '/employees/',
        name: 'EmployeeList',
        component: () => import('../pages/Employee/EmployeeList.vue'),
    },
    {
        path: '/employees/:id',
        name: 'EmployeeDetails',
        component: () => import('../pages/Employee/EmployeeDetails.vue'),
        props: true,
    },
    {
        path: '/employees/:id/edit',
        name: 'EmployeeEdit',
        component: () => import('../pages/Employee/EmployeeEdit.vue'),
        props: true,
    },
    {
        path: '/employees/create',
        name: 'EmployeeCreate',
        component: () => import('../pages/Employee/EmployeeCreate.vue'),
    },

    {
        path: '/orders/',
        name: 'OrderList',
        component: () => import('../pages/Order/OrderList.vue'),
    },
    {
        path: '/orders/create',
        name: 'OrderCreate',
        component: () => import('../pages/Order/OrderCreate.vue'),
    },
    {
        path: '/orders/:id',
        name: 'OrderDetails',
        component: () => import('../pages/Order/OrderDetails.vue'),
        props: true,
    },
    {
        path: '/orders/:id/edit',
        name: 'OrderEdit',
        component: () => import('../pages/Order/OrderEdit.vue'),
        props: true,
    },
    {
        path: '/payments/',
        name: 'PaymentList',
        component: () => import('../pages/Payment/PaymentList.vue'),
    },
    {
        path: '/payments/:id',
        name: 'PaymentDetails',
        component: () => import('../pages/Payment/PaymentDetails.vue'),
        props: true,
    },

    {
        path: '/areas/',
        name: 'AreaList',
        component: () => import('../pages/Area/AreaList.vue'),
    },
    {
        path: '/categories/',
        name: 'CategoryList',
        component: () => import('../pages/Category/CategoryList.vue'),
    },
    {
        path: '/specialities/',
        name: 'SpecialityList',
        component: () => import('../pages/Speciality/SpecialityList.vue'),
    },

];
