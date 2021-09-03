import {requests, apiConstants} from '../../api';
import * as notifizer from '../../utils/notifizer';

export default {
    getCategoriesList({commit}) {
        return requests.get(apiConstants.CATEGORY.DEFAULT)
            .then((response) => {
                commit('updateCategoriesData', response);

                return response;
            })
            .catch((error) => {
                console.log(error);
                notifizer.error(error.response.data.error);

                return error;
            });
    },
};
