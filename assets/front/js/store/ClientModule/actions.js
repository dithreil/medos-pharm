import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';
import router from '../../router';

export default {
    registration({dispatch}, payload) {
        return requests.post(apiConstants.CLIENT.DEFAULT, payload)
            .then((response) => {
                if (response.status === 201) {
                    notifizer.success(`На почту ${payload.email} отправлено письмо для подтверждения email`);
                    router.replace({name: 'Authorization'});
                } else {
                    notifizer.error();
                }

                dispatch('user/getUserData', null, {root: true});

                return response;
            })
            .catch((error) => {
                console.log(error);
                notifizer.error(error.response.data.error);

                return error;
            });
    },
    saveClientData({dispatch}, payload) {
        return requests.put(apiConstants.CLIENT.DEFAULT, payload)
            .then((response) => {
                if (response.status === 200) {
                    notifizer.success('Ваши данные обновлены успешно');
                    router.push({name: 'ClientInfo'});
                } else {
                    notifizer.error();
                }

                return response;
            })
            .catch((error) => {
                console.log(error);
                notifizer.error(error.response.data.error);

                return error;
            });
    },
};
