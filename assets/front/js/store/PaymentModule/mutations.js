import {updateField} from 'vuex-map-fields';

export default {
    updateField,
    updatePayments(state, payload) {
        state.payments = payload;
    },
    updatePaymentsRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.paymentsRequestParams[key] = payload[key];
        });
    },
};
