import {IStoreData} from '../../interfaces/store';
import {IRequestParams} from '../../interfaces/request-params';

export interface IStoreInterface {
    stores: IStoreData;
    storeRequestParams: IRequestParams;
}

function state(): IStoreInterface {
    return {
        stores: {
            total: null,
            pages: null,
            limit: null,
            page: null,
            prev: null,
            next: null,
            items: null,
        },
        storeRequestParams: {
            active: false,
            filter: null,
            descending: null,
            limit: null,
            page: null,
            rowsNumber: null,
            rowsPerPage: null,
            sortBy: null,
        },
    };
}

export default state;
