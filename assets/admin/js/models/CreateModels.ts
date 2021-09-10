import {IProducer} from '../interfaces/producer';
import {ISupplier} from '../interfaces/supplier';

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
export {producerCreate, supplierCreate};
