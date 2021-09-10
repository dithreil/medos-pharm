import {Module} from 'vuex';
import {StateInterface} from '../index';
import state, {IStoreInterface} from './state';
import actions from './actions';
import getters from './getters';
import mutations from './mutations';

const supplierModule: Module<IStoreInterface, StateInterface> = {
    namespaced: true,
    actions,
    getters,
    mutations,
    state,
};

export default supplierModule;
