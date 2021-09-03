import moment from 'moment';

export const dateValidate = (inputDate, isBirthdayDate = false, dateFormat = 'DD-MM-YYYY') => {
    const date = moment(inputDate, dateFormat).format('DD-MM-YYYY');
    const [day, month, year] = date.split('-').map((item) => +item);
    const currentYear = new Date().getFullYear();
    const isDateCorrect = moment(date, 'DD-MM-YYYY', true).isValid();

    if (!isDateCorrect) return false;
    if (isBirthdayDate) return year < currentYear;

    const currentDay = new Date().getDate();
    const currentMonth = new Date().getMonth() + 1;

    if (day < currentDay && month === currentMonth) return false;
    if (month < currentMonth && year === currentYear) return false;

    return true;
};
