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
};
