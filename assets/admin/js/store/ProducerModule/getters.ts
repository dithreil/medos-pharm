import {GetterTree} from 'vuex';
import {IProducerInterface} from './state';
import {StateInterface} from '../index';
import {IProducerData} from '../../interfaces/producer';
import {IRequestParams} from '../../interfaces/request-params';

const getters: GetterTree<IProducerInterface, StateInterface> = {
    producersData(state) : IProducerData {
        return state.producers;
    },
    producerRequestParams(state) : IRequestParams {
        return state.producerRequestParams;
    },
};
export default getters;
