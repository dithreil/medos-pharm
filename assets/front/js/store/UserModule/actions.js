import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';
import router from '../../router';

export default {
    getUserData({commit}) {
        return requests.get(apiConstants.USER.DEFAULT)
            .then((response) => {
                const userData = {...response.data};

                if (!!userData && Object.keys(userData).length > 0) {
                    if (userData.phoneNumber && userData.phoneNumber.charAt(0) === '7') {
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
                if (response.status === 204) {
                    notifizer.success('Ваши данные обновлены успешно');
                    dispatch('getUserData')
                        .then((res) => {
                            if (res.status === 200) {
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
