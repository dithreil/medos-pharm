import Inputmask from 'inputmask';

const options = {};

export default {
    bind(el, bind) {
        Inputmask(bind.value, options).mask(el);
    },
    inserted(el) {
        if (el.value) {
            el.dispatchEvent(new Event('input'));
        }
    },
};
