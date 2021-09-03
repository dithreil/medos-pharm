import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';

export default {
    getUsersList({commit, getters}) {
        return requests.get(apiConstants.EMPLOYEE.DEFAULT, getters.userRequestParams)
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
        return requests.get(apiConstants.EMPLOYEE.DETAILS(id))
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

        return requests.patch(apiConstants.EMPLOYEE.EDIT(id, action))
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
        return requests.patch(apiConstants.EMPLOYEE.EDIT(id, 'password.change'), {password})
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
        return requests.put(apiConstants.EMPLOYEE.EDIT(id), payload)
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
        return requests.post(apiConstants.EMPLOYEE.DEFAULT, payload)
            .then((response) => {
                dispatch('getUsersList');

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    getWeekSchedule({commit, getters}) {
        return requests.get(apiConstants.EMPLOYEE.WEEKLY_SCHEDULES, getters.weekScheduleRequestParams)
            .then((response) => {
                commit('updateWeekSchedule', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    getDaySchedule({commit, getters}) {
        return requests.get(apiConstants.EMPLOYEE.DAY, getters.dayScheduleRequestParams)
            .then((response) => {
                commit('updateDaySchedule', response.data);

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data);

                return error;
            });
    },
    updateWeekScheduleRequestParams({commit, dispatch}, payload) {
        commit('updateWeekScheduleRequestParams', payload);

        return dispatch('getWeekSchedule');
    },
    updateDayScheduleRequestParams({commit, dispatch}, payload) {
        commit('updateDayScheduleRequestParams', payload);

        return dispatch('getDaySchedule');
    },
    updateUserRequestParams({commit, dispatch}, payload) {
        commit('updateUserRequestParams', payload);

        return dispatch('getUsersList');
    },
};
