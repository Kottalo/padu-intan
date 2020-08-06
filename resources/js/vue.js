import Vue from 'vue'
import vuetify from './plugins/vuetify'

/* Views */
Vue.component('project-material',require('./views/ProjectMaterial.vue').default);
Vue.component('supplier-record',require('./views/SupplierRecord.vue').default);

/* Components */
Vue.component('Datepicker',require('./components/Datepicker.vue').default);
Vue.component('OrderItemDataRow',require('./components/OrderItemDataRow.vue').default);
Vue.component('ConfirmDialog',require('./components/ConfirmDialog.vue').default);

const app = new Vue({
  el: '#app',
  vuetify,
});
