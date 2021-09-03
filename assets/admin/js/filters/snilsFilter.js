export default (snils) => {
    if (!snils) return snils;

    return snils.replace(/[^0-9]/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1-$2-$3 $4');
};
