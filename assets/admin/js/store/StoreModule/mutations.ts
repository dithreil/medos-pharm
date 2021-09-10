import {MutationTree} from 'vuex';

import {IStoreInterface} from './state';
import {IRequestParams} from '../../interfaces/request-params';

const mutation: MutationTree<IStoreInterface> = {
    updateStores(state, payload) {
        state.stores = payload;
    },
    updateStoreRequestParams(state, payload: IRequestParams) {
        Object.keys(state.storeRequestParams).forEach((key) => {
            state.storeRequestParams[key] = payload[key];
        });
    },
};

export default mutation;
