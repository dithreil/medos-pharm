import {ISupplierData} from '../../interfaces/supplier';
import {IRequestParams} from '../../interfaces/request-params';

export interface ISupplierInterface {
    suppliers: ISupplierData;
    supplierRequestParams: IRequestParams;
}

function state(): ISupplierInterface {
    return {
        suppliers: {
            total: null,
            pages: null,
            limit: null,
            page: null,
            prev: null,
            next: null,
            items: null,
        },
        supplierRequestParams: {
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
