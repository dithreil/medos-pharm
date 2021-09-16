import {IListResponseData} from "./request-params";
import {INomenclature} from "./nomenclature";
import {IStore} from "./store";
import {ISupplier} from "./supplier";

export interface ITableRowIncome {
    nomenclature: INomenclature,
    serial: string,
    expire: string,
    value: number,
    purchasePrice: number,
    retailPrice: number
}

export interface IDocumentIncome {
    date: string,
    supplier: ISupplier,
    store: IStore,
    rows: Array<ITableRowIncome>
}

export interface IDocumentIncomeData extends IListResponseData {
    items: Array<IDocumentIncome> | null
}
