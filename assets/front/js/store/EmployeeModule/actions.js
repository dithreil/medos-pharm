import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';

export default {
    getWeekSchedule({commit, getters}) {
        return requests.get(apiConstants.EMPLOYEE.DEFAULT, getters.weekScheduleRequestParams)
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
    saveEmployeeData(ctx, payload) {
        return requests.put(apiConstants.EMPLOYEE.DEFAULT, payload)
            .then((response) => {
                notifizer.success('Ваши данные обновлены успешно');

                return response;
            })
            .catch((error) => {
                console.log(error);
                notifizer.error(error.response.data.error);
            });
    },
};
