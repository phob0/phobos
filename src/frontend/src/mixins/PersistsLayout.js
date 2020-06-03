export default {
  methods: {
    getLayoutKey() {
      return 'general'
    },

    /**
     * Persists listing columns in a cookie
     */
    _persistLayout(val) {
      let cookieVal = this.$q.cookies.get('kps_layout') || {}
      cookieVal[this.getLayoutKey()] = val

      this.$q.cookies.set('kps_layout', cookieVal, {
        expires: 3650
      })
    },

    /**
     * Returns the stored layout settings
     */
    _restoreLayout() {
      let cookieVal = this.$q.cookies.get('kps_layout')
      if (!cookieVal || cookieVal[this.getLayoutKey()] === undefined) {
        return false
      }
      return cookieVal[this.getLayoutKey()]
    }
  }
}
