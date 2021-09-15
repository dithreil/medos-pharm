import {GetterTree} from 'vuex';
import {INomenclatureInterface} from './state';
import {StateInterface} from '../index';
import {INomenclatureData} from '../../interfaces/nomenclature';
import {IRequestParams} from '../../interfaces/request-params';

const getters: GetterTree<INomenclatureInterface, StateInterface> = {
    nomenclaturesData(state) : INomenclatureData {
        return state.nomenclatures;
    },
    medFormsData(state) : Array<string> {
        return state.medForms;
    },
    nomenclatureRequestParams(state) : IRequestParams {
        return state.nomenclatureRequestParams;
    },
};
export default getters;
