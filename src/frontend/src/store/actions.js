import Vue from 'vue'

export async function loadConfig({ commit, state }) {
  try {
    const config = (await Vue.prototype.$axios.get(`${state.API_BASE_URL}/config`)).data
    commit({ type: 'setConfig', config })
  } catch (ex) {
    console.debug(ex)
    commit({
      type: 'setError',
      fatal: true,
      message: 'Could not load app config from server',
      exception: ex
    })
  }
}

export function setError({ commit }, payload) {
  if (payload.ex) {
    // non authentication errors get caught by axios
    if (payload.ex.response && payload.ex.response.status === 401) {
      return
    }

    console.error(payload.ex)
  }

  const fatal = (payload.ex && payload.ex.response && payload.ex.response.data)
    ? payload.ex.response.data.fatal || false : false
  const message = (payload.ex && payload.ex.response && payload.ex.response.data)
    ? payload.ex.response.data.message || 'An error has occurred' : 'An error has occurred'

  commit({
    type: 'setError',
    fatal,
    message,
    exception: payload.ex || null
  })
}

export async function login({ commit, state, dispatch }, payload) {
  try {
    const response = await Vue.prototype.$axios.post(`${state.API_BASE_URL}/login`, payload.data)
    if (!response.data.errors) {
      if (response.headers['access-control-allow-origin']) {
        // persist token, as we're not a 1st hand client (different URL, no cookie)
        Vue.prototype.$q.localStorage.set('token', response.data.extra.access_token)
      }

      // in both cases, this takes care of the token until a page refresh
      // (cookies will not be live until then)
      Vue.prototype.$axios.defaults.headers['Authorization'] = 'Bearer ' + response.data.extra.access_token

      const me = await Vue.prototype.$axios.get(`${state.API_BASE_URL}/me`)
      commit({ type: 'setConfig', config: Object.assign({}, state.config, { user: me.data.extra.user }) })

      commit('loggedIn', { token: response.data.extra.access_token })
      return response.data
    }
  } catch (ex) {
    if (ex.response && ex.response.status === 422) {
      return ex.response.data
    }
    dispatch('setError', { ex })
    return { errors: [] }
  }
}

export async function logout({ commit, state }, payload = true) {
  if (payload) {
    await Vue.prototype.$axios.post(`${state.API_BASE_URL}/logout`)
  }
  commit('loggedOut')
}

export async function elevate({ commit, state }, payload = true) {
  commit('elevate')
}
