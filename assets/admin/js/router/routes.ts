import {RouteConfig} from 'vue-router';

const routes: RouteConfig[] = [
    {
        path: '/',
        name: 'Main',
        component: () => import('../pages/Main.vue'),
    },
    {
        path: '/producers',
        name: 'ProducerList',
        component: () => import('../pages/Producer/ProducerList.vue'),
    },
    {
        path: '/supplier',
        name: 'SupplierList',
        component: () => import('../pages/Supplier/SupplierList.vue'),
    },
    {
        path: '/store',
        name: 'StoreList',
        component: () => import('../pages/Store/StoreList.vue'),
    },
];

export default routes;
