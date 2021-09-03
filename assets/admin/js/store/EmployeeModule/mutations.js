import {updateField} from 'vuex-map-fields';

export default {
    updateField,
    updateUsers(state, payload) {
        state.users = payload;
    },
    updateUserRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.userRequestParams[key] = payload[key];
        });
    },
    updateWeekSchedule(state, payload) {
        state.weekSchedule = payload;
    },
    updateWeekScheduleRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.weekScheduleRequestParams[key] = payload[key];
        });
    },
    updateDaySchedule(state, payload) {
        state.daySchedule = payload;
    },
    updateDayScheduleRequestParams(state, payload) {
        Object.keys(payload).forEach((key) => {
            state.dayScheduleRequestParams[key] = payload[key];
        });
    },
};
