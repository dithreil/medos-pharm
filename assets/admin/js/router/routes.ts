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
        path: '/suppliers',
        name: 'SupplierList',
        component: () => import('../pages/Supplier/SupplierList.vue'),
    },
    {
        path: '/stores',
        name: 'StoreList',
        component: () => import('../pages/Store/StoreList.vue'),
    },
    {
        path: '/nomenclatures',
        name: 'NomenclatureList',
        component: () => import('../pages/Nomenclature/NomenclatureList.vue'),
    },
    {
        path: '/nomenclatures/create',
        name: 'NomenclatureCreate',
        component: () => import('../pages/Nomenclature/NomenclatureCreate.vue'),
    },
    {
        path: '/nomenclatures/:nomenclatureId/edit',
        name: 'NomenclatureEdit',
        component: () => import('../pages/Nomenclature/NomenclatureEdit.vue'),
        props: true,
    },
    {
        path: '/nomenclatures/:nomenclatureId',
        name: 'NomenclatureDetails',
        component: () => import('../pages/Nomenclature/NomenclatureDetails.vue'),
        props: true,
    },
    {
        path: '/incomes',
        name: 'IncomeList',
        component: () => import('../pages/Income/IncomeList.vue'),
        props: true,
    },
];

export default routes;
