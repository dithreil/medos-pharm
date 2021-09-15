import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';
import {ActionTree} from 'vuex';
import {IStoreInterface} from './state';
import {StateInterface} from '../index';


const actions: ActionTree<IStoreInterface, StateInterface> = {

    getStoreList({commit, getters}) {
        return requests.get(apiConstants.STORE.DEFAULT, getters.storeRequestParams)
            .then((response) => {
                commit('updateStores', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    getStoreDetails(ctx, id) {
        return requests.get(apiConstants.STORE.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    editStoreData({dispatch}, {id, payload}) {
        return requests.put(apiConstants.STORE.DETAILS(id), payload)
            .then((response) => {
                dispatch('getStoreList');
                notifizer.success('Данные торговой точки изменены');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    createStore({dispatch}, {payload}) {
        return requests.post(apiConstants.STORE.DEFAULT, payload)
            .then((response) => {
                dispatch('getStoreList');
                notifizer.success('Торговая точка создана');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    updateStoreRequestParams({commit, dispatch}, payload) {
        commit('updateStoreRequestParams', payload);

        return dispatch('getStoreList');
    },
};
export default actions;
