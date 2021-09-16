import {INomenclature, INomenclatureData} from '../../interfaces/nomenclature';
import {IRequestParams} from '../../interfaces/request-params';

export interface INomenclatureInterface {
    nomenclatures: INomenclatureData;
    nomenclatureRequestParams: IRequestParams;
    medForms: Array<string>;
}

function state(): INomenclatureInterface {
    return {
        medForms: [],
        nomenclatures: {
            total: null,
            pages: null,
            limit: null,
            page: null,
            prev: null,
            next: null,
            items: null,
        },
        nomenclatureRequestParams: {
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
