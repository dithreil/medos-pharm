import Vue from 'vue';

import Vuex from 'vuex';
import producer from './ProducerModule';
import supplier from './SupplierModule';
import store from './StoreModule';
import {IStoreInterface} from './StoreModule/state';
import {IProducerInterface} from './ProducerModule/state';
import {ISupplierInterface} from './SupplierModule/state';

Vue.use(Vuex);


export interface StateInterface {
    modules: {
        producer: IProducerInterface,
        supplier: ISupplierInterface,
        store: IStoreInterface
    }
}

export default new Vuex.Store<StateInterface>( {
    modules: {
        producer,
        supplier,
        store,
    },
});
