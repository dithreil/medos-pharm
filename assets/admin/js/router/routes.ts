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
    {
        path: '/nomenclature',
        name: 'NomenclatureList',
        component: () => import('../pages/Nomenclature/NomenclatureList.vue'),
    },
    {
        path: '/nomenclature/create',
        name: 'NomenclatureCreate',
        component: () => import('../pages/Nomenclature/NomenclatureCreate.vue'),
    },
    {
        path: '/nomenclature/:nomenclatureId/edit',
        name: 'NomenclatureEdit',
        component: () => import('../pages/Nomenclature/NomenclatureEdit.vue'),
        props: true,
    },
    {
        path: '/nomenclature/:nomenclatureId',
        name: 'NomenclatureDetails',
        component: () => import('../pages/Nomenclature/NomenclatureDetails.vue'),
        props: true,
    },

];

export default routes;
