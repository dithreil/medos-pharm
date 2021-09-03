import {getField} from 'vuex-map-fields';

export default {
    getField,
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
