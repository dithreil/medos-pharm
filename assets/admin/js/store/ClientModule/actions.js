import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';

export default {
    getUsersList({commit, getters}) {
        return requests.get(apiConstants.CLIENT.DEFAULT, getters.userRequestParams)
            .then((response) => {
                commit('updateUsers', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    getUserDetails(ctx, id) {
        return requests.get(apiConstants.CLIENT.DETAILS(id))
            .then((response) => {
                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    toggleBlockUser({getters, dispatch}, id) {
        const user = getters.usersData.items.find((user) => user.id === id);
        const action = user.isActive ? 'disable' : 'enable';

        return requests.patch(apiConstants.CLIENT.EDIT(id, action))
            .then((response) => {
                dispatch('getUsersList');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    changeUserPassword({dispatch}, {id, password}) {
        return requests.patch(apiConstants.CLIENT.EDIT(id, 'password.change'), {password})
            .then((response) => {
                dispatch('getUsersList');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    editUserData({dispatch}, {id, payload}) {
        return requests.put(apiConstants.CLIENT.EDIT(id), payload)
            .then((response) => {
                dispatch('getUsersList');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    createUser({dispatch}, {payload}) {
        return requests.post(apiConstants.CLIENT.DEFAULT, payload)
            .then((response) => {
                dispatch('getUsersList');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    updateUserRequestParams({commit, dispatch}, payload) {
        commit('updateUserRequestParams', payload);

        return dispatch('getUsersList');
    },
};
