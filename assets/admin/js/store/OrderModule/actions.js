import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';

export default {
    getOrderList({commit, getters}) {
        return requests.get(apiConstants.ORDER.DEFAULT, getters.orderRequestParams)
            .then((response) => {
                commit('updateOrders', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    getOrderDetails(ctx, id) {
        return requests.get(apiConstants.ORDER.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    editOrderData({dispatch}, {id, payload}) {
        return requests.put(apiConstants.ORDER.EDIT(id), payload)
            .then((response) => {
                dispatch('getOrderList');
                notifizer.success('Консультация изменена!');

                return response;
            })
            .catch((error) => {
                throw error;
            });
    },
    createOrder({dispatch}, payload) {
        return requests.post(apiConstants.ORDER.DEFAULT, payload)
            .then((response) => {
                dispatch('getOrderList');
                notifizer.success('Консультация создана!');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    uploadOrderDocuments({dispatch}, {id, payload}) {
        return requests.post(apiConstants.ORDER.ADD_DOC(id), payload)
            .then((response) => {
                dispatch('getOrderList');
                notifizer.success('Документы загружены');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    deleteOrderDocument({dispatch}, {id}) {
        return requests.delete(apiConstants.ORDER.DELETE_DOC(id))
            .then((response) => {
                dispatch('getOrderList');
                notifizer.success('Документ удалён');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    updateOrderRequestParams({commit, dispatch}, payload) {
        commit('updateOrderRequestParams', payload);

        return dispatch('getOrderList');
    },
};
