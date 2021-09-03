import Vue from 'vue';
import App from './App.vue';
import Quasar from 'quasar';
import iconSet from 'quasar/icon-set/material-icons-round';
import '../../styles/app.sass';


Vue.use(Quasar, {
    iconSet,
});

new Vue({
    render: (h) => h(App),
}).$mount('#app-admin-authorization');
