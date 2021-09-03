import {highlightRequiredFields} from '../utils/highlightRequiredFields';

export const requiredFieldsMixin = {
    mounted() {
        setTimeout(() => highlightRequiredFields(), 0);
    },
};
