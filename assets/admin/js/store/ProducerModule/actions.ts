import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';
import {ActionTree} from 'vuex';
import {IProducerInterface} from './state';
import {StateInterface} from '../index';


const actions: ActionTree<IProducerInterface, StateInterface> = {

    getProducerList({commit, getters}) {
        return requests.get(apiConstants.PRODUCER.DEFAULT, getters.producerRequestParams)
            .then((response) => {
                commit('updateProducers', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    getProducerDetails(ctx, id) {
        return requests.get(apiConstants.PRODUCER.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    editProducerData({dispatch}, {id, payload}) {
        return requests.put(apiConstants.PRODUCER.DETAILS(id), payload)
            .then((response) => {
                dispatch('getProducerList');
                notifizer.success('Данные производителя изменены');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    createProducer({dispatch}, {payload}) {
        return requests.post(apiConstants.PRODUCER.DEFAULT, payload)
            .then((response) => {
                dispatch('getProducerList');
                notifizer.success('Производитель создан');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                throw error;
            });
    },
    updateProducerRequestParams({commit, dispatch}, payload) {
        commit('updateProducerRequestParams', payload);

        return dispatch('getProducerList');
    },
};
export default actions;
