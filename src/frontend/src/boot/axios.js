import axios from 'axios'

export default ({ Vue, store }) => {
  const token = Vue.prototype.$q.localStorage.getItem('token')

  let params = {
    validateStatus: status => {
      if (status === 401) {
        store.dispatch('logout', false)
      } else if (status === 403) {
        // need elevated privileges
        store.dispatch('elevate')
      }

      return status >= 200 && status < 300
    }
  }

  if (token) {
    Object.assign(params, {
      headers: {
        Authorization: 'Bearer ' + token
      }
    })
  }

  Vue.prototype.$axios = axios.create(params)
}
