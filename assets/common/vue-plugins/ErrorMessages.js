export default {
    install(Vue) {
        Vue.prototype.$errorMessages = {
            REQUIRED: 'Обязательное поле',
            INVALID_EMAIL: 'Неправильный email',
            INVALID_DATE: 'Неверный формат даты',
            INVALID_PHONE: 'Неправильный телефон',
            INVALID_PASSWOR_LENGTH: (count) => `Минимальная длина пароля ${count}`,
            INVALID_CONFIRM_PASSWORDS: 'Пароли не совпадают',
            ONLY_RU_LETTERS: 'Только русские буквы',
            ONLY_NUMBERS: 'Только русские цифры',
            CONTAIN_CYRILLIC: (field) => `Поле ${field} не должно содержать русские буквы`,
            INCORECT_PHONE: 'Неправильный формат телефона',
        };
    },
};
