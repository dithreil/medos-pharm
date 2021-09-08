export default [
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
];
