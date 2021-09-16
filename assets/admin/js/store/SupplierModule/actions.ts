import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';
import {ActionTree} from 'vuex';
import {ISupplierInterface} from './state';
import {StateInterface} from '../index';


const actions: ActionTree<ISupplierInterface, StateInterface> = {

    getSupplierList({commit, getters}) {
        return requests.get(apiConstants.SUPPLIER.DEFAULT, getters.supplierRequestParams)
            .then((response) => {
                commit('updateSuppliers', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    getSupplierDetails(ctx, id) {
        return requests.get(apiConstants.SUPPLIER.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    editSupplierData({dispatch}, {id, payload}) {
        return requests.put(apiConstants.SUPPLIER.DETAILS(id), payload)
            .then((response) => {
                dispatch('getSupplierList');
                notifizer.success('Данные поставщика изменены');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    createSupplier({dispatch}, {payload}) {
        return requests.post(apiConstants.SUPPLIER.DEFAULT, payload)
            .then((response) => {
                dispatch('getSupplierList');
                notifizer.success('Поставщик создан');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    updateSupplierRequestParams({commit, dispatch}, payload) {
        commit('updateSupplierRequestParams', payload);

        return dispatch('getSupplierList');
    },
};
export default actions;
