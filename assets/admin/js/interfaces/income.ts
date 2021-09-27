import {IListResponseData} from './request-params';
import {ICharacteristic, INomenclature} from './nomenclature';
import {IStore} from './store';
import {ISupplier} from './supplier';

export interface ITableRowIncome {
    nomenclature: INomenclature | null,
    characteristic: ICharacteristic,
    value: number | null,
    purchasePrice: number | null,
    retailPrice: number | null,
}

export interface IDocumentIncome {
    id: string,
    date: string,
    supplier: ISupplier,
    store: IStore,
    rows: Array<ITableRowIncome>
}


export interface IDocumentIncomeDetails extends IDocumentIncome {
    createdAt: string,
    updatedAt: string
}

export interface IDocumentIncomeData extends IListResponseData {
    items: Array<IDocumentIncome> | null
}

export interface ITableRowIncomeCreateEditData {
    nomenclature: string | null,
    serial: string,
    expire: string,
    value: number | null,
    purchasePrice: number | null,
    retailPrice: number | null
}

export interface IDocumentIncomeCreateEditData {
    date: string,
    supplierId: string,
    storeId: string,
    comment: string,
    rows: Array<ITableRowIncomeCreateEditData>
}
