import {MutationTree} from 'vuex';

import {IProducerInterface} from './state';
import {IRequestParams} from '../../interfaces/request-params';

const mutation: MutationTree<IProducerInterface> = {
    updateProducers(state, payload) {
        state.producers = payload;
    },
    updateProducerRequestParams(state, payload: IRequestParams) {
        Object.keys(state.producerRequestParams).forEach((key) => {
            state.producerRequestParams[key] = payload[key];
        });
    },
};

export default mutation;
