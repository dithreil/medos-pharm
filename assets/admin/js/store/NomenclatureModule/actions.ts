import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';
import {ActionTree} from 'vuex';
import {INomenclatureInterface} from './state';
import {StateInterface} from '../index';


const actions: ActionTree<INomenclatureInterface, StateInterface> = {

    getNomenclatureList({commit, getters}) {
        return requests.get(apiConstants.NOMENCLATURE.DEFAULT, getters.nomenclatureRequestParams)
            .then((response) => {
                commit('updateNomenclatures', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    getMedFormsList({commit}) {
        return requests.get(apiConstants.NOMENCLATURE.MED_FORMS)
            .then((response) => {
                commit('updateMedForms', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    getNomenclatureDetails(ctx, id) {
        return requests.get(apiConstants.NOMENCLATURE.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    editNomenclatureData({dispatch}, {id, payload}) {
        return requests.put(apiConstants.NOMENCLATURE.DETAILS(id), payload)
            .then((response) => {
                dispatch('getNomenclatureList');
                notifizer.success('Данные номенклатуры изменены');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    createNomenclature({dispatch}, {payload}) {
        return requests.post(apiConstants.NOMENCLATURE.DEFAULT, payload)
            .then((response) => {
                dispatch('getNomenclatureList');
                notifizer.success('Номенклатура создана');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    updateNomenclatureRequestParams({commit, dispatch}, payload) {
        commit('updateNomenclatureRequestParams', payload);

        return dispatch('getNomenclatureList');
    },
};
export default actions;
