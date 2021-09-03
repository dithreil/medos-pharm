const statusMap = {
    new: 'Новый заказ',
    cancelled: 'Отменён',
    paid: 'Оплачен',
    not_paid: 'Не оплачен',
    done: 'Завершен',
    change_status: 'Изменение статуса',
};

export const getStatusFromStatusMap = (status) => {
    return statusMap[status];
};
