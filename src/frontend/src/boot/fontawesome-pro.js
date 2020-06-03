import '@fortawesome/fontawesome-pro/css/fontawesome.min.css'
import '@fortawesome/fontawesome-pro/css/light.min.css'
import '@fortawesome/fontawesome-pro/css/regular.min.css'
import '@fortawesome/fontawesome-pro/css/solid.min.css'
import '@fortawesome/fontawesome-pro/css/brands.min.css'

export default ({ Vue }) => {
  Vue.prototype.$q.iconSet.expansionItem.icon = 'fal fa-angle-down'
  Vue.prototype.$q.iconSet.chip.remove = 'fas fa-times-circle q-ml-sm'
}
