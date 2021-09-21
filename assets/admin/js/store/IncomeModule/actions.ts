import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';
import {ActionTree} from 'vuex';
import {IDocumentIncomeInterface} from './state';
import {StateInterface} from '../index';


const actions: ActionTree<IDocumentIncomeInterface, StateInterface> = {

    getIncomeList({commit, getters}) {
        return requests.get(apiConstants.INCOME.DEFAULT, getters.incomeRequestParams)
            .then((response) => {
                commit('updateIncomes', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    getIncomeDetails(ctx, id) {
        return requests.get(apiConstants.INCOME.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    editIncomeData({dispatch}, {id, payload}) {
        return requests.patch(apiConstants.INCOME.DETAILS(id), payload)
            .then((response) => {
                dispatch('getIncomeList');
                notifizer.success('Данные поступления изменены');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    createIncome({dispatch}, {payload}) {
        return requests.post(apiConstants.INCOME.DEFAULT, payload)
            .then((response) => {
                dispatch('getIncomeList');
                notifizer.success('Поступление создано');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    updateIncomeRequestParams({commit, dispatch}, payload) {
        commit('updateIncomeRequestParams', payload);

        return dispatch('getIncomeList');
    },
};
export default actions;
