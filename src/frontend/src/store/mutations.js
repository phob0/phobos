import Vue from 'vue'

export function setConfig(state, payload) {
  state.config = payload.config
  if (!payload.config.user) {
    state.loggedIn = false
  }
}

export function setError(state, payload) {
  const status = payload.exception.response ? payload.exception.response.status : 500
  if (status === 401 || status === 403) {
    // authentication errors will display the login modal
    return
  }

  state.error = {
    fatal: payload.fatal,
    message: payload.message
  }
}

export function loggedIn(state) {
  state.loggedIn = true
}

export function loggedOut(state) {
  state.config.user = null
  Vue.prototype.$q.localStorage.remove('token')
  state.loggedIn = false
  delete Vue.prototype.$axios.defaults.headers['Authorization']
}

export function elevate(state, payload = true) {
  state.elevate = payload
}
