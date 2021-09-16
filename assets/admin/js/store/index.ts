import Vue from 'vue';

import Vuex from 'vuex';
import producer from './ProducerModule';
import supplier from './SupplierModule';
import store from './StoreModule';
import income from './IncomeModule';
import nomenclature from './NomenclatureModule';
import {IStoreInterface} from './StoreModule/state';
import {IProducerInterface} from './ProducerModule/state';
import {ISupplierInterface} from './SupplierModule/state';
import {INomenclatureInterface} from './NomenclatureModule/state';
import {IDocumentIncomeInterface} from "./IncomeModule/state";

Vue.use(Vuex);


export interface StateInterface {
    modules: {
        producer: IProducerInterface,
        supplier: ISupplierInterface,
        store: IStoreInterface,
        nomenclature: INomenclatureInterface,
        income: IDocumentIncomeInterface,
    }
}

export default new Vuex.Store<StateInterface>( {
    modules: {
        producer,
        supplier,
        store,
        nomenclature,
        income,
    },
});
