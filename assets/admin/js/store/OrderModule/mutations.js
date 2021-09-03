import {updateField} from 'vuex-map-fields';

export default {
    updateField,
    updateOrders(state, payload) {
        state.orders = payload;
    },
    updateOrderRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.orderRequestParams[key] = payload[key];
        });
    },
};
