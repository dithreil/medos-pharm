import {ISupplier} from '../interfaces/supplier';
import {IStore} from '../interfaces/store';
import {
    IDocumentIncome,
    IDocumentIncomeCreateEditData,
    ITableRowIncome,
    ITableRowIncomeCreateEditData,
} from '../interfaces/income';
import {storeCreate, supplierCreate} from './CreateModels';

export default class IncomeDocument {
    public id: string;
    public date: string;
    public supplier: ISupplier;
    public store: IStore;
    public rows: Array<ITableRowIncome>;

    constructor(obj?: IDocumentIncome) {
        if (obj) {
            this.id = obj.id;
            this.date = obj.date;
            this.supplier = obj.supplier;
            this.store = obj.store;
            this.rows = obj.rows;
        } else {
            this.rows = [];
            this.supplier = supplierCreate;
            this.store = storeCreate;
            this.date = '';
            this.id = '';
        }
    }

    private prepareDocumentRows(): Array<ITableRowIncomeCreateEditData> {
        const rowCreateEdit : Array<ITableRowIncomeCreateEditData> = [];

        this.rows.forEach((p) => {
            rowCreateEdit.push({
                expire: p.characteristic.expireOriginal + ' 00:00:00',
                serial: p.characteristic.serial,
                nomenclature: p.nomenclature?.id || null,
                purchasePrice: p.purchasePrice,
                retailPrice: p.retailPrice,
                value: p.value,
            });
        });

        return rowCreateEdit;
    };

    public getDataForServer(): IDocumentIncomeCreateEditData {
        return {
            comment: '',
            date: this.date,
            storeId: this.store.id,
            supplierId: this.supplier.id,
            rows: this.prepareDocumentRows(),
        };
    }
    public addNewRow() {
        this.rows.push({
            nomenclature: null,
            retailPrice: null,
            purchasePrice: null,
            value: null,
            characteristic: {
                id: null,
                expire: '',
                serial: '',
                expireOriginal: '',
            },
        });
    }
    public isValid() {
        return !(!this.store || !this.supplier || !this.date || !this.isRowsValid());
    }

    private isRowsValid() {
        let flag = true;
        this.rows.forEach((p) => {
            if (!p.nomenclature
                || !p.characteristic.serial
                || !p.characteristic.expire
                || !p.purchasePrice || 0 > p.purchasePrice
                || !p.retailPrice || 0 > p.retailPrice
                || !p.value || 0 > p.value
            ) {
                flag = false;
            }
        });

        return flag;
    }
}
