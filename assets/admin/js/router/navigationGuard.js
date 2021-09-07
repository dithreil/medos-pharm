export default (to, next, store) => {
    const isUserAuthenticated = !!store.getters['user/userData'];

    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (isUserAuthenticated) {
            return next();
        }

        return next({name: 'Authorization', query: {redirect: to.fullPath}});
    }

    return next();
};
