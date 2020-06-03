export default {

  methods: {

    fuzzySearch(needle, haystack) {
      const nlen = needle.length
      const hlen = haystack.length
      if (nlen > hlen) {
        return false
      }

      needle = needle.toLowerCase()
      haystack = haystack.toLowerCase()
      if (String.prototype.normalize) {
        needle = needle.normalize('NFD')
        haystack = haystack.normalize('NFD')
      }
      needle = needle.replace(/[\u0300-\u036f]/g, '')
      haystack = haystack.replace(/[\u0300-\u036f]/g, '')

      if (nlen === hlen) {
        return needle === haystack
      }

      for (let i = 0, j = 0; i < nlen; i++) {
        const nch = needle[i]
        let foundChar = false
        while (j < hlen) {
          if (haystack[j++].localeCompare(nch) === 0) {
            foundChar = true
            break
          }
        }
        if (foundChar) {
          continue
        }
        return false
      }

      return true
    },

    numericSearch(needle, haystack) {
      return parseFloat(needle) === haystack
    },

    onSelectFilter(filteredOptionsVar, options, val, update) {
      let optionSet = []
      let type = 'string'

      if (options.length === undefined) {
        // options is an object
        optionSet = options.options || []
        type = options.type || 'string'
      } else {
        optionSet = options
      }

      if (val === '') {
        update(() => {
          this[filteredOptionsVar] = optionSet
        })
        return
      }

      switch (type) {
        case 'string':
          update(() => {
            this[filteredOptionsVar] = optionSet.filter(
              v => this.fuzzySearch(val, v.label))
          })
          break
        case 'numeric':
          update(() => {
            this[filteredOptionsVar] = optionSet.filter(
              v => this.numericSearch(val, v.value))
          })
          break
      }
    }

  }

}
