export function superadmin(id) {
  return [{
    code: 'account',
    label: 'My Account',
    caption: 'Account details',
    icon: 'fal fa-user-edit',
    route: { name: 'user.edit', params: { id }, query: { my_account: 1 } },
    children: [],
    link: null,
    target: null
  }, {
    code: 'users',
    label: 'Users',
    caption: 'User accounts',
    icon: 'fal fa-users',
    route: { name: 'user.listing' },
    children: [],
    link: null,
    target: null
  }, {
    code: 'settings',
    label: 'Settings',
    caption: 'System settings',
    icon: 'fal fa-cog',
    route: '/settings',
    children: [],
    link: null,
    target: null
  }/* { sidemenuDummy } */]
}

export function user(id) {
  return [{
    code: 'account',
    label: 'My Account',
    caption: 'Account details',
    icon: 'fal fa-user-edit',
    route: { name: 'user.edit', params: { id }, query: { my_account: 1 } },
    children: [],
    link: null,
    target: null
  }, {
    code: 'settings',
    label: 'Settings',
    caption: 'System settings',
    icon: 'fal fa-cog',
    route: '/settings',
    routeParams: {},
    children: [],
    link: null,
    target: null
  }]
}

export function responsible(id) {
  return [{
    code: 'account',
    label: 'My Account',
    caption: 'Account details',
    icon: 'fal fa-user-edit',
    route: { name: 'user.edit', params: { id }, query: { my_account: 1 } },
    children: [],
    link: null,
    target: null
  }, {
    code: 'settings',
    label: 'Settings',
    caption: 'System settings',
    icon: 'fal fa-cog',
    route: '/settings',
    routeParams: {},
    children: [],
    link: null,
    target: null
  }]
}
