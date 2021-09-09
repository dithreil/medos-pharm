const ruLettersRule = (value: string) => {
    if (!value || '' === value) {
        return true;
    }

    return /^[\u0401\u0451\u0410-\u044f]+$/i.test(String(value));
};

const noCyrillicRule = (value: string) => {
    if (!value || '' === value) {
        return true;
    }

    return !/^[а-яё]+$/i.test(value);
};

// eslint-disable-next-line max-len
const emailRule = (value: string) => /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);

const numberOnlyRule = (value: string) => /^\d+$/.test(value);

const phoneRule = (value: string) => /^(\+?7?\s?\(?\d{3}\)?\s?\d{3}-?\s?\d{2}-?\s?\d{2})$/gi.test(value);

const countDigitsRule = (num: number) => {
    let n = num;
    let count = 0;

    for (let i = 0; 1 < n; i++) {
        n /= 10;
        count++;
    }

    return count;
};

const numberLengthRule = (param: number) => {
    return (value: number) => {
        return countDigitsRule(value) === param;
    };
};

const dateFormat = (value: string) => /^([0-3][0-9].[0-1][0-9].(19|20)[0-9]{2})$/gi.test(value);

export {
    ruLettersRule,
    emailRule,
    numberLengthRule,
    numberOnlyRule,
    phoneRule,
    countDigitsRule,
    noCyrillicRule,
    dateFormat,
};
