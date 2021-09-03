const ruLettersRule = (value) => {
    if (!value || value === '') {
        return true;
    }

    return /^[а-яё-\s-]+$/i.test(String(value));
};

const noCyrillicRule = (value) => {
    if (!value || value === '') {
        return true;
    }

    return !/^[а-яё]+$/i.test(String(value));
};

// eslint-disable-next-line max-len
const emailRule = (value) => !!/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);

const numberOnlyRule = (value) => !!/^\d+$/.test(String(value));

const phoneRule = (value) => !!/^(\+?7?\s?\(?\d{3}\)?\s?\d{3}-?\s?\d{2}-?\s?\d{2})$/gi.test(String(value));

const countDigitsRule = (num) => {
    let n = num;
    let count = 0;

    for (let i = 0; n > 1; i++) {
        n /= 10;
        count++;
    }

    return count;
};

const numberLengthRule = (param) => {
    return (value) => {
        return countDigitsRule(value) === param;
    };
};

export {
    ruLettersRule,
    emailRule,
    numberLengthRule,
    numberOnlyRule,
    phoneRule,
    countDigitsRule,
    noCyrillicRule,
};
