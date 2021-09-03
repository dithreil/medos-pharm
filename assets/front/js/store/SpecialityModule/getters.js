import {getField} from 'vuex-map-fields';

export default {
    getField,
    specialitiesData(state) {
        return state.specialities;
    },
    specialityRequestParams(state) {
        return state.specialityRequestParams;
    },
};
