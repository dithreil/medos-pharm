import {getField} from 'vuex-map-fields';

export default {
    getField,
    producersData(state) {
        return state.producers;
    },
    producerRequestParams(state) {
        return state.producerRequestParams;
    },
};
