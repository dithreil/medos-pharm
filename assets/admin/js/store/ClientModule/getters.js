import {getField} from 'vuex-map-fields';

export default {
    getField,
    usersData(state) {
        return state.users;
    },
    userRequestParams(state) {
        return state.userRequestParams;
    },
};
