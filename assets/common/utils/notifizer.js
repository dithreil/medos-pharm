import {Notify} from 'quasar';

const success = (message = null) => {
    Notify.create({
        message: message || 'Успешно',
        type: 'positive',
        position: 'top',
    });
};

const warning = (message = null) => {
    Notify.create({
        message: message || 'Предупреждение',
        type: 'warning',
        position: 'top',
    });
};

const error = (message = null) => {
    Notify.create({
        message: message || 'Ошибка',
        type: 'negative',
        position: 'top',
    });
};

export {success, warning, error};
