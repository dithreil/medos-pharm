import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import Quasar from 'quasar';
import iconSet from 'quasar/icon-set/material-icons-round';
import ErrorMessages from '../../common/vue-plugins/ErrorMessages';
import 'quasar/dist/quasar.sass';
import '../styles/app.sass';
import moment from 'moment';

Vue.use(Quasar, {
    iconSet,
});
Vue.use(ErrorMessages);
Vue.prototype.$moment = moment;

const initApp = () => {
    return new Vue({
        router,
        store,
        render: (h) => h(App),
    }).$mount('#app-front');
};

store.dispatch('user/getUserData').finally(() => initApp());
