import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';
import router from '../../router';

export default {
    login({dispatch}, payload) {
        return requests.post(apiConstants.AUTH.LOGIN, payload)
            .then((response) => {
                if (response.status === 200) {
                    dispatch('user/getUserData', null, {root: true})
                        .then((response) => {
                            if (response.status !== 200) {
                                notifizer.error();
                            }
                        });
                } else {
                    notifizer.error();
                }

                return response;
            })
            .catch((error) => {
                notifizer.error(error.response.data.error);

                throw error;
            });
    },
    logout({commit}) {
        return requests.get(apiConstants.AUTH.LOGOUT)
            .then((response) => {
                commit('user/updateUserData', null, {root: true});
                router.replace({name: 'Authorization'});

                return response;
            })
            .catch((error) => {
                console.log(error);

                return error;
            });
    },
    restorePasswordClient(ctx, payload) {
        return requests.post(apiConstants.AUTH.PASSWORD_RESTORE.CLIENT, payload)
            .then((response) => {
                notifizer.success('Если такой пользователь существует, то вам было отправлено письмо с инструкцией');

                return response;
            })
            .catch((error) => {
                notifizer.success('Если такой пользователь существует, то вам было отправлено письмо с инструкцией');
            });
    },
    restorePasswordEmployee(ctx, payload) {
        return requests.post(apiConstants.AUTH.PASSWORD_RESTORE.EMPLOYEE, payload)
            .then((response) => {
                notifizer.success('Если такой пользователь существует, то вам было отправлено письмо с инструкцией');

                return response;
            })
            .catch((error) => {
                notifizer.success('Если такой пользователь существует, то вам было отправлено письмо с инструкцией');
            });
    },
};
