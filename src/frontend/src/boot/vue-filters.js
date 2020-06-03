export default ({ Vue }) => {
  Vue.filter('capitalize', v => {
    if (!v) {
      return ''
    }
    v = v.toString()
    return v.charAt(0).toUpperCase() + v.slice(1)
  })
}
