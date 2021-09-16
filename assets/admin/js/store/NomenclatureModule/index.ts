import {Module} from 'vuex';
import {StateInterface} from '../index';
import state, {INomenclatureInterface} from './state';
import actions from './actions';
import getters from './getters';
import mutations from './mutations';

const producerModule: Module<INomenclatureInterface, StateInterface> = {
    namespaced: true,
    actions,
    getters,
    mutations,
    state,
};

export default producerModule;
