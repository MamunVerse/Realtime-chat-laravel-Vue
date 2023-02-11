
require('./bootstrap');

window.Vue = require('vue');

import Vuex from 'vuex';

Vue.use(Vuex);

import storeVuex from './store/index';

const store = new Vuex.Store(storeVuex);



Vue.component('main-app', require('./components/MainApp.vue').default);
const app = new Vue({
    el: '#app',
    store
});
