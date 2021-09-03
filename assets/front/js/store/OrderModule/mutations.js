import {updateField} from 'vuex-map-fields';

export default {
    updateField,
    updateOrders(state, payload) {
        state.orders = payload;
    },
    updateNotPaidOrders(state, payload) {
        state.notPaidOrders = payload;
    },
    updateForthcomingOrders(state, payload) {
        state.forthcomingOrders = payload;
    },
    updateOrderRequestParams(state, payload) {
        state.orderRequestParams = {};
        Object.keys(payload).forEach((key) => {
            state.orderRequestParams[key] = payload[key];
        });
    },
    updateNotPaidOrderRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.notPaidOrderRequestParams[key] = payload[key];
        });
    },
    updateForthcomingOrderRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.forthcomingOrderRequestParams[key] = payload[key];
        });
    },
};
