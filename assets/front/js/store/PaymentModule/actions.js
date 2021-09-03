import {requests, apiConstants} from '../../api';

export default {
    getPaymentsList({commit, getters}) {
        return requests.get(apiConstants.PAYMENT.DEFAULT, getters.paymentRequestParams)
            .then((response) => {
                commit('updatePayments', response.data);

                return response;
            })
            .catch((error) => {
                throw error;
            });
    },
    createPayment({dispatch}, payload) {
        return requests.put(apiConstants.PAYMENT.DEFAULT, payload)
            .then((response) => {
                dispatch('getPaymentsList', response.data);

                return response;
            })
            .catch((error) => {
                throw error;
            });
    },
};
