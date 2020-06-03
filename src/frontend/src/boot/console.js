export default ({ Vue }) => {
  Vue.prototype.$console = console

  Vue.prototype.$console.slog = function(...args) {
    for (let i of args) {
      console.log(JSON.stringify(i))
    }
  }
}
