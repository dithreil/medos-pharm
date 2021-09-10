import {IListResponseData} from './request-params';

export interface ISupplier {
    id: string,
    name: string,
    address: string,
    email: string,
    phoneNumber: string,
    information: string,
}

export interface ISupplierDetails extends ISupplier{
    createdAt: string,
    updatedAt: string
}

export interface ISupplierData extends IListResponseData{
    items: Array<ISupplier> | null
}
