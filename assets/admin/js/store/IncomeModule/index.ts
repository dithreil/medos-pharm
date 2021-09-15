import {Module} from 'vuex';
import {StateInterface} from '../index';
import state, {IDocumentIncomeInterface} from './state';
import actions from './actions';
import getters from './getters';
import mutations from './mutations';

const producerModule: Module<IDocumentIncomeInterface, StateInterface> = {
    namespaced: true,
    actions,
    getters,
    mutations,
    state,
};

export default producerModule;
