import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';
import store from '../store';
import navigationGuard from './navigationGuard';

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes,
});

router.beforeEach((to, from, next) => {
    navigationGuard(to, next, store);
});

export default router;
