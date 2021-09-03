import {Notify} from 'quasar';

const success = (message = null) => {
    showMessage(message || 'Успешно', 'positive');
};

const warning = (message = null) => {
    showMessage(message || 'Предупреждение', 'warning');
};

const error = (message = null) => {
    showMessage(message || 'Ошибка', 'negative');
};

const info = (message = null) => {
    showMessage(message || 'Информация', 'info');
};

const showMessage = (message, type) => {
    Notify.create({
        type,
        message: convertMessage(message),
        position: 'top',
        multiLine: true,
        html: true,
    });
};

const convertMessage = (message) => {
    if (message && typeof message === 'object') {
        if (message.hasOwnProperty('errors')) {
            const newMessage = [];

            Object.keys(message.errors).forEach((key) => {
                newMessage.push(`<b>${key}</b>: ${message.errors[key].error}`);
            });

            message = newMessage.join('<br>');
        } else {
            message = message.error;
        }
    }

    return message;
};

export {success, warning, error, info};
