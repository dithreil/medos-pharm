import Vue from 'vue';

import Vuex, {Store, StoreOptions} from 'vuex';
import producer from './ProducerModule';
import {IProducerInterface} from './ProducerModule/state';

Vue.use(Vuex);


export interface StateInterface {
    modules: {
        producer: IProducerInterface
    }
}

export default new Vuex.Store<StateInterface>( {
    modules: {
        producer,
    },
});
