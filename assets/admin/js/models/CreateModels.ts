import {IProducer} from '../interfaces/producer';
import {ISupplier} from '../interfaces/supplier';
import {IStore} from '../interfaces/store';
import {INomenclature} from '../interfaces/nomenclature';

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

const nomenclatureCreate: INomenclature = {
    id: '',
    name: '',
    isVat: false,
    medicalForm: '',
    producer: producerCreate,
};

const storeCreate: IStore = {
    id: '',
    name: '',
    address: '',
    description: '',
};
export {producerCreate, nomenclatureCreate, supplierCreate, storeCreate};
