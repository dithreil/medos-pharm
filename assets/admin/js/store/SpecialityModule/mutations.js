import {updateField} from 'vuex-map-fields';

export default {
    updateField,
    updateSpecialities(state, payload) {
        state.specialities = payload;
    },
    updateSpecialityRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.specialityRequestParams[key] = payload[key];
        });
    },
};
