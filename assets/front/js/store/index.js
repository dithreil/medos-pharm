import Vue from 'vue';
import Vuex from 'vuex';
import user from './UserModule';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        user,

    },
});
