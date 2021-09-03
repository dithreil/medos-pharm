import {getField} from 'vuex-map-fields';

export default {
    getField,
    categoriesData(state) {
        return state.categories;
    },
    categoryRequestParams(state) {
        return state.categoryRequestParams;
    },
    currentCategory(state) {
        return state.currentCategory;
    },
};
