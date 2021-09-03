import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';

export default {
    getPaymentList({commit, getters}) {
        return requests.get(apiConstants.PAYMENT.DEFAULT, getters.paymentRequestParams)
            .then((response) => {
                commit('updatePayments', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    getPaymentDetails(ctx, id) {
        return requests.get(apiConstants.PAYMENT.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    updatePaymentRequestParams({commit, dispatch}, payload) {
        commit('updatePaymentRequestParams', payload);

        return dispatch('getPaymentList');
    },
};
