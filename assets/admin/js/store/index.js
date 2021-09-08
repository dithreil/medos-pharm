import Vue from 'vue';
import Vuex from 'vuex';
import producer from './ProducerModule';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        producer,
    },
});
