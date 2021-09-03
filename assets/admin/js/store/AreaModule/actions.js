import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';

export default {
    getAreaList({commit, getters}) {
        return requests.get(apiConstants.AREA.DEFAULT, getters.areaRequestParams)
            .then((response) => {
                commit('updateAreas', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    getAreaDetails(ctx, id) {
        return requests.get(apiConstants.AREA.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    updateAreaRequestParams({commit, dispatch}, payload) {
        commit('updateAreaRequestParams', payload);

        return dispatch('getAreaList');
    },
};
