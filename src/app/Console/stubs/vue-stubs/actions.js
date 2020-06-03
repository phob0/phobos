import * as common from '../common/actions'

const moduleName = 'dummys'

export async function list({ dispatch, commit, state, rootState }, payload) {
  return common._list(moduleName, ...arguments)
}

export async function store({ dispatch, rootState, getters }) {
  return common._store(moduleName, ...arguments)
}

export async function edit({ dispatch, commit, rootState }, payload) {
  return common._edit(moduleName, ...arguments)
}

export async function update({ dispatch, rootState, getters }) {
  return common._update(moduleName, ...arguments)
}

export async function destroy({ dispatch, commit, state, rootState }, payload) {
  return common._destroy(moduleName, ...arguments)
}
