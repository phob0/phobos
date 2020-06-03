import * as menuSets from './sideMenu'

export function config(state) { return state.config }
export function version(state) { return state.config ? state.config.version : '' }
export function error(state) { return state.error.message ? state.error : null }
export function loggedIn(state) { return state.loggedIn }

export function baseUrl(state) {
  return state.API_BASE_URL
}

export function user(state) {
  return state.config.user ? state.config.user : null
}

export function isAdmin(state) {
  return state.config.user ? ['admin', 'superadmin'].indexOf(state.config.user.role.name) >= 0 : false
}

export function isUser(state) {
  return state.config.user ? ['user'].indexOf(state.config.user.role.name) >= 0 : false
}

export function sideMenu(state) {
  if (!state.config.user) {
    return []
  }
  switch (state.config.user.role.name) {
    case 'superadmin':
    case 'admin':
      return menuSets.superadmin(state.config.user.id)
    case 'user':
      return menuSets.user(state.config.user.id, state.config.user.role.company_id, state.config.user.role.agency_id)
    case 'responsible':
      return menuSets.responsible(state.config.user.id)
    default:
      return []
  }
}

export function lookupAllRoles() {
  return [
    { label: 'Superadmin', value: 'superadmin' },
    { label: 'Admin', value: 'admin' },
    { label: 'User', value: 'user' }
  ]
}

export function lookupRoles(state) {
  if (!state.config.user) {
    return []
  }
  switch (state.config.user.role.name) {
    case 'superadmin':
      return lookupAllRoles()
    case 'admin':
      return lookupAllRoles().filter(role => ['superadmin'].indexOf(role.value) < 0)
    default:
      return []
  }
}

export function lookupStatuses() {
  return [
    { label: 'Active', value: 'A' },
    { label: 'Inactive', value: 'I' }
  ]
}
