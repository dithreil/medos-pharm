import {getField} from 'vuex-map-fields';

export default {
    getField,
    ordersData(state) {
        return state.orders;
    },
    notPaidOrdersData(state) {
        return state.notPaidOrders;
    },
    forthcomingOrdersData(state) {
        return state.forthcomingOrders;
    },
    orderRequestParams(state) {
        return state.orderRequestParams;
    },
};
