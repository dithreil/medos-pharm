import Vue from 'vue';
import Vuex from 'vuex';
import client from './ClientModule';
import employee from './EmployeeModule';
import order from './OrderModule';
import payment from './PaymentModule';
import area from './AreaModule';
import speciality from './SpecialityModule';
import category from './CategoryModule';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        client,
        employee,
        order,
        payment,
        area,
        speciality,
        category,
    },
});
