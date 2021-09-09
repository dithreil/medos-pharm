import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';
import router from '../../router';

export default {
    getUserData({commit}) {
        return requests.get(apiConstants.USER.DEFAULT)
            .then((response) => {
                const userData = {...response.data};

                if (!!userData && 0 < Object.keys(userData).length) {
                    if (userData.phoneNumber && '7' === userData.phoneNumber.charAt(0)) {
                        userData.phoneNumber = userData.phoneNumber.substring(1);
                    }

                    commit('updateUserData', userData);
                }

                return response;
            })
            .catch((error) => {
                console.log(error);
                notifizer.error(error.response.data.error);

                return error;
            });
    },
    saveUserData({dispatch}, payload) {
        return requests.put(apiConstants.USER.DEFAULT, payload)
            .then((response) => {
                if (204 === response.status) {
                    notifizer.success('Ваши данные обновлены успешно');
                    dispatch('getUserData')
                        .then((res) => {
                            if (200 === res.status) {
                                router.replace({name: 'ClientInfo'});
                            }
                        });
                } else {
                    notifizer.error();
                }

                dispatch('getUserData');

                return response;
            })
            .catch((error) => {
                console.log(error);
                notifizer.error(error.response.data.error);

                return error;
            });
    },
    changePassword({dispatch}, payload) {
        return requests.put(apiConstants.AUTH.CHANGE_PASSWORD, payload)
            .then((response) => {
                notifizer.success('Ваш пароль успешно изменен');
                dispatch('getUserData');

                return response;
            })
            .catch((error) => {
                console.log(error);
                notifizer.error(error.response.data.error);
            });
    },
};
