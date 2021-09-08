import {updateField} from 'vuex-map-fields';

export default {
    updateField,
    updateProducers(state, payload) {
        state.producers = payload;
    },
    updateProducerRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.producerRequestParams[key] = payload[key];
        });
    },
};
