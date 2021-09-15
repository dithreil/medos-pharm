import {GetterTree} from 'vuex';
import {IDocumentIncomeInterface} from './state';
import {StateInterface} from '../index';
import {IDocumentIncomeData} from '../../interfaces/income';
import {IRequestParams} from '../../interfaces/request-params';

const getters: GetterTree<IDocumentIncomeInterface, StateInterface> = {
    incomesData(state) : IDocumentIncomeData {
        return state.incomes;
    },
    incomeRequestParams(state) : IRequestParams {
        return state.incomeRequestParams;
    },
};
export default getters;
