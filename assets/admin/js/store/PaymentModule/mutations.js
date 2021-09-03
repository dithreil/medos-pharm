import {updateField} from 'vuex-map-fields';

export default {
    updateField,
    updatePayments(state, payload) {
        state.payments = payload;
    },
    updatePaymentRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.paymentRequestParams[key] = payload[key];
        });
    },
};
