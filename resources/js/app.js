require('./bootstrap');

import Vue from "vue";
import Moment from "moment";
import Swiper, {Navigation, Pagination, Autoplay, Thumbs, EffectFade, Scrollbar} from 'swiper';
import Slideout from "slideout";
import KProgress from 'k-progress';
import CoolLightBox from 'vue-cool-lightbox';
import VueTheMask from 'vue-the-mask';
import Notifications from 'vue-notification';

Swiper.use([Navigation, Pagination, Autoplay, Thumbs, EffectFade, Scrollbar]);

Vue.component('k-progress', KProgress);
Vue.use(CoolLightBox);
Vue.use(VueTheMask);
Vue.use(Notifications);

window.Swiper = Swiper;
window.Moment = Moment;
window.Slideout = Slideout;
window.Vue = Vue;

require("./helpers");
require("./main");
require("./admin/menu");
require("suggestions-jquery/dist/js/jquery.suggestions");
const _ = require('lodash');
// Инициализация tosts
let toastElList = [].slice.call(document.querySelectorAll('.toast'))
let toastList = toastElList.map(function (toastEl) {
  return new bs.Toast(toastEl, {
    delay: 5000
  })
})

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Автозагрузка компонентов
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
const VueUploadComponent = require('vue-upload-component')
Vue.component('file-upload', VueUploadComponent)

const app = new Vue({
  el: '#app',
});
