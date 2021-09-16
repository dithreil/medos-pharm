import Vue from 'vue';
import App from './App.vue';
import Quasar from 'quasar';
import store from './store';
import router from './router';
import iconSet from 'quasar/icon-set/material-icons-round';
import ErrorMessages from '../../common/vue-plugins/ErrorMessages';
import langRu from 'quasar/lang/ru';
import '../styles/app.sass';
import moment from 'moment';
import constants from '../js/constants';

Vue.use(Quasar, {
    iconSet,
    lang: langRu,
});
Vue.use(ErrorMessages);

Vue.prototype.$moment = moment;
Vue.prototype.$constants = constants;

new Vue({
    store,
    router,
    render: (h) => h(App),
}).$mount('#app-admin-main');


