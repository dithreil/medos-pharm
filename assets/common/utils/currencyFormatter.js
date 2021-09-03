const formatter = new Intl.NumberFormat('ru-RU', {
    currency: 'RUB',
    style: 'currency',
});

export function currencyFormatter(value) {
    return typeof value === 'number'
        ? formatter.format(value)
        : formatter.format(parseFloat(value));
}
