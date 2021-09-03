export default (to, next, store) => {
    const isUserAuthenticated = !!store.getters['user/userData'];

    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (isUserAuthenticated) {
            return next();
        }

        return next({name: 'Authorization', query: {redirect: to.fullPath}});
    } else {
        if (isUserAuthenticated) {
            const isUserEmployee = !!store.getters['user/userData'].speciality;

            if (isUserEmployee) {
                return next({name: 'EmployeeInfo'});
            } else {
                return next({name: 'ClientInfo'});
            }
        }
    }

    return next();
};
