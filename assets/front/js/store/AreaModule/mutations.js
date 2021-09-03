import {updateField} from 'vuex-map-fields';

export default {
    updateField,
    updateAreas(state, payload) {
        state.areas = payload;
    },
    updateAreaRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.areaRequestParams[key] = payload[key];
        });
    },
};
