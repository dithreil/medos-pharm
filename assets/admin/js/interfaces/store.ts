import {IListResponseData} from './request-params';

export interface IStore {
    id: string,
    name: string,
    address: string,
    description: string,
}

export interface IStoreDetails extends IStore{
    createdAt: string,
    updatedAt: string
}

export interface IStoreData extends IListResponseData{
    items: Array<IStore> | null
}
