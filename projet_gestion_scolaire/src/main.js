import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

// Importer les services API
import * as ApiServices from './services/api'

// Rendre les services API disponibles globalement
Vue.prototype.$api = ApiServices

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
