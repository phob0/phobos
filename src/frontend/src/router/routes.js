
const routes = [
  {
    path: '/',
    component: () => import(/* webpackChunkName: 'app_base' */ 'layouts/Default.vue'),
    children: [
      { path: '', name: 'dashboard', component: () => import(/* webpackChunkName: 'app_base' */ 'pages/Index.vue') },
      { path: 'home', component: () => import(/* webpackChunkName: 'app_base' */ 'pages/Index.vue') },
      /* { routesDummy } */
      { path: 'settings', name: 'setting.listing', component: () => import(/* webpackChunkName: 'app_settings' */ 'pages/settings/SettingsListingPage.vue') },
      { path: 'settings/create', name: 'setting.create', component: () => import(/* webpackChunkName: 'app_settings' */ 'pages/settings/SettingsPage.vue') },
      { path: 'settings/:id', name: 'setting.edit', component: () => import(/* webpackChunkName: 'app_settings' */ 'pages/settings/SettingsPage.vue') },

      { path: 'users', name: 'user.listing', component: () => import(/* webpackChunkName: 'app_users' */ 'pages/user/UserListingPage.vue') },
      { path: 'users/create', name: 'user.create', component: () => import(/* webpackChunkName: 'app_users' */ 'pages/user/UserPage.vue') },
      { path: 'users/:id', name: 'user.edit', component: () => import(/* webpackChunkName: 'app_users' */ 'pages/user/UserPage.vue') }
    ]
  }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    name: 'error404',
    component: () => import(/* webpackChunkName: 'app_base' */ 'pages/Error404.vue')
  })
}

export default routes
