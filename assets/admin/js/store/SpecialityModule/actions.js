import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';

export default {
    getSpecialityList({commit, getters}) {
        return requests.get(apiConstants.SPECIALITIES.DEFAULT, getters.specialityRequestParams)
            .then((response) => {
                commit('updateSpecialities', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    getSpecialityDetails(ctx, id) {
        return requests.get(apiConstants.SPECIALITIES.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    updateSpecialityRequestParams({commit, dispatch}, payload) {
        commit('updateSpecialityRequestParams', payload);

        return dispatch('getSpecialityList');
    },
};
