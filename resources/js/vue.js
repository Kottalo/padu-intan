import Vue from 'vue'
import vuetify from './plugins/vuetify'

/* Views */
Vue.component('project-material',require('./views/ProjectMaterial.vue').default);

/* Components */
Vue.component('Datepicker',require('./components/Datepicker.vue').default);
Vue.component('OrderItemDataRow',require('./components/OrderItemDataRow.vue').default);

const app = new Vue({
  el: '#app',
  vuetify,
});
