import {getField} from 'vuex-map-fields';

export default {
    getField,
    paymentsData(state) {
        return state.payments;
    },
    paymentsRequestParams(state) {
        return state.paymentsRequestParams;
    },
};
