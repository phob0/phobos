/* eslint-disable space-before-function-paren */

export default async ({ Vue, store }) => {
  Vue.prototype.$app = {}

  Vue.prototype.$q.loading.show({
    delay: 100
  })

  await store.dispatch('loadConfig')

  Vue.prototype.$q.loading.hide()
}
