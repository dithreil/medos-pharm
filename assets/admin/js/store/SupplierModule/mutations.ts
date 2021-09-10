import {MutationTree} from 'vuex';

import {ISupplierInterface} from './state';
import {IRequestParams} from '../../interfaces/request-params';

const mutation: MutationTree<ISupplierInterface> = {
    updateSuppliers(state, payload) {
        state.suppliers = payload;
    },
    updateSupplierRequestParams(state, payload: IRequestParams) {
        Object.keys(state.supplierRequestParams).forEach((key) => {
            state.supplierRequestParams[key] = payload[key];
        });
    },
};

export default mutation;
