import {IListResponseData} from "./request-params";

export interface IProducer {
    id: string,
    shortName: string,
    fullName: string,
    country: string,
}

export interface IProducerDetails extends IProducer{
    createdAt: string,
    updatedAt: string
}

export interface IProducerData extends IListResponseData{
    items: Array<IProducer> | null
}
