import {getField} from 'vuex-map-fields';

export default {
    getField,
    ordersData(state) {
        return state.orders;
    },
    orderRequestParams(state) {
        return state.orderRequestParams;
    },
};
