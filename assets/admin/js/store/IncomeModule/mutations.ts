import {MutationTree} from 'vuex';

import {IDocumentIncomeInterface} from './state';
import {IRequestParams} from '../../interfaces/request-params';

const mutation: MutationTree<IDocumentIncomeInterface> = {
    updateIncomes(state, payload) {
        state.incomes = payload;
    },
    updateIncomeRequestParams(state, payload: IRequestParams) {
        Object.keys(state.incomeRequestParams).forEach((key) => {
            state.incomeRequestParams[key] = payload[key];
        });
    },
};

export default mutation;
