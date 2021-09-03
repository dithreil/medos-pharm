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

                throw error;
            });
    },
    getNotPaidOrderList({commit, getters}) {
        return requests.get(apiConstants.ORDER.DEFAULT, {...getters.orderRequestParams, notPaid: true})
            .then((response) => {
                commit('updateNotPaidOrders', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    getForthcomingOrderList({commit, getters}) {
        return requests.get(apiConstants.ORDER.DEFAULT, {...getters.orderRequestParams, forthcoming: true})
            .then((response) => {
                commit('updateForthcomingOrders', response.data);

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

                return error;
            });
    },
    createOrder({dispatch}, payload) {
        return requests.post(apiConstants.ORDER.DEFAULT, payload)
            .then((response) => {
                dispatch('getOrderList');
                notifizer.success('Запись на консультацию создана!');

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
    getOrderDocument(ctx, {id}) {
        return requests.get(apiConstants.ORDER.GET_DOC(id))
            .then((response) => {
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
    updateNotPaidOrderRequestParams({commit, dispatch}, payload) {
        commit('updateNotPaidOrderRequestParams', payload);

        return dispatch('getNotPaidOrderList');
    },
    updateForthcomingOrderRequestParams({commit, dispatch}, payload) {
        commit('updateForthcomingOrderRequestParams', payload);

        return dispatch('getForthcomingOrderList');
    },
    orderSpecialActions({dispatch}, {id, payload}) {
        return requests.patch(apiConstants.ORDER.DETAILS(id), payload)
            .then((response) => {
                dispatch('getOrderDetails', id);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
};

