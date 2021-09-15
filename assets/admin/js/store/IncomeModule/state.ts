import {IDocumentIncomeData} from '../../interfaces/income';
import {IRequestParams} from '../../interfaces/request-params';

export interface IDocumentIncomeInterface {
    incomes: IDocumentIncomeData;
    incomeRequestParams: IRequestParams;
}

function state(): IDocumentIncomeInterface {
    return {
        incomes: {
            total: null,
            pages: null,
            limit: null,
            page: null,
            prev: null,
            next: null,
            items: null,
        },
        incomeRequestParams: {
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
