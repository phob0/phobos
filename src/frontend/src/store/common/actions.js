import Vue from 'vue'

export function getDownloadFilename(response, defaultFilename) {
  if (response.headers['content-disposition']) {
    let filename = response.headers['content-disposition'].match(/filename=([^;]+)/)
    if (filename && filename.length > 1) {
      return filename[1]
    }
  }
  return defaultFilename
}

export async function _list(module, { dispatch, rootState }, payload) {
  try {
    let params = Object.assign({}, payload.query, {
      page: payload.pagination.page,
      per_page: payload.pagination.rowsPerPage,
      sort_by: payload.pagination.sortBy,
      sort_order: (payload.pagination.descending === true || payload.pagination.descending === 'true') ? 'desc' : 'asc'
    })

    Object.entries(payload.filters).filter(pair => pair[1] !== null).forEach(pair => {
      params[`filters[${pair[0]}]`] = pair[1]
    })

    const response = await Vue.prototype.$axios.get(`${rootState.API_BASE_URL}/${module}`, { params })
    return response.data
  } catch (ex) {
    dispatch('setError', { ex }, { root: true })
  }
}

export async function _store(module, { dispatch, rootState }, payload) {
  try {
    const response =
      await Vue.prototype.$axios.post(`${rootState.API_BASE_URL}/${module}`, Object.assign({}, payload.query, payload.params))
    return response.data
  } catch (ex) {
    if (ex.response && ex.response.status === 422) {
      return ex.response.data
    }
    dispatch('setError', { ex }, { root: true })
    return { errors: [] }
  }
}

export async function _edit(module, { dispatch, rootState }, payload) {
  try {
    const response = await Vue.prototype.$axios.get(`${rootState.API_BASE_URL}/${module}/${payload.id}`, {
      params: payload.query
    })
    return response.data.data
  } catch (ex) {
    dispatch('setError', { ex }, { root: true })
    return { errors: [] }
  }
}

export async function _update(module, { dispatch, rootState }, payload) {
  try {
    const response =
      await Vue.prototype.$axios.put(`${rootState.API_BASE_URL}/${module}/${payload.id}`, Object.assign({}, payload.query, payload.params))
    return response.data
  } catch (ex) {
    if (ex.response && ex.response.status === 422) {
      return ex.response.data
    }
    dispatch('setError', { ex }, { root: true })
    return { errors: [] }
  }
}

export async function _destroy(module, { dispatch, rootState }, payload) {
  try {
    const response =
      await Vue.prototype.$axios.delete(`${rootState.API_BASE_URL}/${module}/${payload.id}`, { params: payload.query })
    return response.data
  } catch (ex) {
    dispatch('setError', { ex }, { root: true })
    return { errors: [] }
  }
}
