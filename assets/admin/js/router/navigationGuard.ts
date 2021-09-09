import {Route, NavigationGuardNext} from 'vue-router';
import {Store} from 'vuex';
import {StateInterface} from '../store';
export default (to: Route, next: NavigationGuardNext, store: Store<StateInterface>) => {
    const isUserAuthenticated = !!store.getters['user/userData'];

    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (isUserAuthenticated) {
            return next();
        }

        return next({name: 'Authorization', query: {redirect: to.fullPath}});
    }

    return next();
};
