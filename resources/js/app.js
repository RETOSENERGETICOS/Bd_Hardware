require('./bootstrap');

import Vue from 'vue'
import VueRouter from "vue-router";
import Vuex from "vuex";

import vuetify from './plugins/vuetify'
import router from './plugins/router'
import store from './plugins/store'

Vue.component('login-page', require('./components/login/index').default)
Vue.use(VueRouter)
Vue.use(Vuex)

new Vue({
    vuetify,
    store,
    router
}).$mount('#app')
