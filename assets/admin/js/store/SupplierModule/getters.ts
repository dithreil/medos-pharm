import {GetterTree} from 'vuex';
import {ISupplierInterface} from './state';
import {StateInterface} from '../index';
import {ISupplierData} from '../../interfaces/supplier';
import {IRequestParams} from '../../interfaces/request-params';

const getters: GetterTree<ISupplierInterface, StateInterface> = {
    suppliersData(state) : ISupplierData {
        return state.suppliers;
    },
    supplierRequestParams(state) : IRequestParams {
        return state.supplierRequestParams;
    },
};
export default getters;
