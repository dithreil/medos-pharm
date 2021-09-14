export default (phone: string) => {
    let result = '';

    if ('7' === phone.charAt(0)) {
        result = `${phone}`;
    } else {
        result = `7${phone}`;
    }

    return result.replace(/[^0-9]/g, '').replace(/(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/, '+$1 ($2) $3-$4-$5');
};
