import {updateField} from 'vuex-map-fields';

export default {
    updateField,
    updateCategories(state, payload) {
        state.categories = payload;
    },
    updateCategoryRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.categoryRequestParams[key] = payload[key];
        });
    },
    updateCurrentCategory(state, payload) {
        state.currentCategory = payload;
    },
};
