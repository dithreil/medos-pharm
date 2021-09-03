import {getField} from 'vuex-map-fields';

export default {
    getField,
    paymentData(state) {
        return state.payments;
    },
    paymentRequestParams(state) {
        return state.paymentRequestParams;
    },
};
