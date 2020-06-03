const API_BASE_URL = process.env.API_BASE_URL

export default {
  API_BASE_URL,
  config: {},
  error: {
    fatal: false,
    message: ''
  },
  loggedIn: true,
  // whether the users needs to authenticate with a higher role to access a
  // resource
  elevate: false,
  base: {}
}
