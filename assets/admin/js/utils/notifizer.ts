import {Notify} from 'quasar';

interface IError {
    error: string
}
interface IErrorMessage {
    error: string | null
    errors: any
}

const success = (message: string | null) => {
    showMessage(message || 'Успешно', 'positive');
};

const warning = (message: string | null) => {
    showMessage(message || 'Предупреждение', 'warning');
};

const error = (message: string | null) => {
    showMessage(message || 'Ошибка', 'negative');
};

const info = (message: string | null) => {
    showMessage(message || 'Информация', 'info');
};

const showMessage = (message: IErrorMessage | string, type: string) => {
    Notify.create({
        type,
        message: 'string' === typeof message ? message : convertMessage(message),
        position: 'top',
        multiLine: true,
        html: true,
    });
};

const convertMessage = (message: any) => {
    if (message.hasOwnProperty('errors')) {
        const newMessage : Array<string> = [];
        let errorName : string;

        Object.keys(message.errors).forEach((key) => {
            errorName = message.errors[key].error;
            newMessage.push(`<b>${key}</b>: ${errorName}`);
        });

        return newMessage.join('<br>');
    } else {
        return message.error || 'Unknown error';
    }
};

export {success, warning, error, info};
