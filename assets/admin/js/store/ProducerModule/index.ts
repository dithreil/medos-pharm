import {Module} from 'vuex';
import {StateInterface} from '../index';
import state, {IProducerInterface} from './state';
import actions from './actions';
import getters from './getters';
import mutations from './mutations';

const producerModule: Module<IProducerInterface, StateInterface> = {
    namespaced: true,
    actions,
    getters,
    mutations,
    state,
};

export default producerModule;
