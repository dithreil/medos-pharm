import systemConstants from './systemConstants';
import {IConstants} from "../interfaces/request-params";


declare module 'vue/types/vue' {
    interface Vue {
        $constants: IConstants,
    }
}

export default {
    systemConstants,
};
