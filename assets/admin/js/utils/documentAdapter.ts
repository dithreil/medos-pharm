import {ITableRowIncome, ITableRowIncomeCreateEditData} from '../interfaces/income';

const prepareDocumentRows = (rows: Array<ITableRowIncome>): Array<ITableRowIncomeCreateEditData> => {
    const rowCreateEdit : Array<ITableRowIncomeCreateEditData> = [];
    rows.forEach((p) => {
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

export {prepareDocumentRows};
