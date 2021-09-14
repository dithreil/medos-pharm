import {IProducer, IProducerData} from '../../interfaces/producer';
import {IRequestParams} from '../../interfaces/request-params';

export interface IProducerInterface {
    producers: IProducerData;
    producerRequestParams: IRequestParams;
}

function state(): IProducerInterface {
    return {
        producers: {
            total: null,
            pages: null,
            limit: null,
            page: null,
            prev: null,
            next: null,
            items: null,
        },
        producerRequestParams: {
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
