import {getField} from 'vuex-map-fields';

export default {
    getField,
    usersData(state) {
        return state.users;
    },
    userRequestParams(state) {
        return state.userRequestParams;
    },
    weekSchedule(state) {
        return state.weekSchedule;
    },
    weekScheduleRequestParams(state) {
        return state.weekScheduleRequestParams;
    },
    daySchedule(state) {
        return state.daySchedule;
    },
    dayScheduleRequestParams(state) {
        return state.dayScheduleRequestParams;
    },
};
