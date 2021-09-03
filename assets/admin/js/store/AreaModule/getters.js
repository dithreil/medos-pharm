import {getField} from 'vuex-map-fields';

export default {
    getField,
    areasData(state) {
        return state.areas;
    },
    areaRequestParams(state) {
        return state.areaRequestParams;
    },
};
