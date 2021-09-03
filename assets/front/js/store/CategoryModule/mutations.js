export default {
    updateCategoriesData(state, payload) {
        state.categories = payload;
    },
    updateCurrentCategory(state, payload) {
        state.currentCategory = payload;
    },
};
