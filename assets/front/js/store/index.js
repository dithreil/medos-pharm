import Vue from 'vue';
import Vuex from 'vuex';
import authentication from './AuthenticationModule';
import client from './ClientModule';
import employee from './EmployeeModule';
import user from './UserModule';
import order from './OrderModule';
import area from './AreaModule';
import speciality from './SpecialityModule';
import category from './CategoryModule';
import payment from './PaymentModule';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        authentication,
        client,
        employee,
        user,
        order,
        speciality,
        area,
        category,
        payment,
    },
});
