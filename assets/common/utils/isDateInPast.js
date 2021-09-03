import {dateValidate} from './dateValidate';
import {error} from './notifizer';

export const isDateInPast = (date) => {
    if (!date) return false;

    if (!dateValidate(date, false, 'YYYY-MM-DD')) {
        error('Невозможно посмотреть расписание за прошедшие дни!');

        return false;
    };

    return true;
};
