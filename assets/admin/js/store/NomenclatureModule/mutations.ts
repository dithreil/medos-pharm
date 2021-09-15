import {MutationTree} from 'vuex';

import {INomenclatureInterface} from './state';
import {IRequestParams} from '../../interfaces/request-params';

const mutation: MutationTree<INomenclatureInterface> = {
    updateNomenclatures(state, payload) {
        state.nomenclatures = payload;
    },
    updateMedForms(state, payload) {
        state.medForms = payload;
    },
    updateNomenclatureRequestParams(state, payload: IRequestParams) {
        Object.keys(state.nomenclatureRequestParams).forEach((key) => {
            state.nomenclatureRequestParams[key] = payload[key];
        });
    },
};

export default mutation;
