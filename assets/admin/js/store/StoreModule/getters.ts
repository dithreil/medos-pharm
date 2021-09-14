import {GetterTree} from 'vuex';
import {IStoreInterface} from './state';
import {StateInterface} from '../index';
import {IStoreData} from '../../interfaces/store';
import {IRequestParams} from '../../interfaces/request-params';

const getters: GetterTree<IStoreInterface, StateInterface> = {
    storesData(state) : IStoreData {
        return state.stores;
    },
    storeRequestParams(state) : IRequestParams {
        return state.storeRequestParams;
    },
};
export default getters;
