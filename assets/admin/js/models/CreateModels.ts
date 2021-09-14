import {IProducer} from '../interfaces/producer';
import {ISupplier} from '../interfaces/supplier';
import {IStore} from "../interfaces/store";

const producerCreate: IProducer = {
    id: '',
    shortName: '',
    fullName: '',
    country: '',
};

const supplierCreate: ISupplier = {
    id: '',
    name: '',
    address: '',
    email: '',
    phoneNumber: '',
    information: '',
};

const storeCreate: IStore = {
    id: '',
    name: '',
    address: '',
    description: '',
};
export {producerCreate, supplierCreate, storeCreate};
