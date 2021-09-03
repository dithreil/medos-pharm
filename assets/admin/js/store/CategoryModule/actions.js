import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';

export default {
    getCategoryList({commit, getters}) {
        return requests.get(apiConstants.CATEGORIES.DEFAULT, getters.categoryRequestParams)
            .then((response) => {
                commit('updateCategories', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    getCategoryDetails(ctx, id) {
        return requests.get(apiConstants.CATEGORIES.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    updateCategoryRequestParams({commit, dispatch}, payload) {
        commit('updateCategoryRequestParams', payload);

        return dispatch('getCategoryList');
    },
};
