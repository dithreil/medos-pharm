import {IListResponseData} from './request-params';
import {IProducer} from './producer';

export interface INomenclature {
    id: string,
    name: string,
    producer: IProducer,
    medicalForm: string,
    isVat: boolean,
}

export interface INomenclatureCreateEditData {
    name: string,
    producer: string,
    medicalForm: string,
    isVat: boolean,
}

export interface ICharacteristic {
    expire: string,
    expireOriginal: string,
    id: string | null,
    serial: string,
}

export interface INomenclatureDetails extends INomenclature {
    createdAt: string,
    updatedAt: string
}

export interface INomenclatureData extends IListResponseData {
    items: Array<INomenclature> | null
}
