import ValidatesForm from './ValidatesForm'
import HasForm from './HasForm'

export default {
  mixins: [ValidatesForm, HasForm],

  props: {
    /**
     * The Vuex module name belonging to this component
     */
    module: {
      type: String,
      default: ''
    }
  },

  data() {
    return {
      // additional query params
      query: {},

      form: {
        item: {},
        rules: {}
      },
      loading: false,
      remoteErrors: {},
      listingAfterStore: true,
      listingAfterUpdate: true
    }
  },

  computed: {
    /**
     * Returns the string to display as a form subtitle
     *
     * @returns {string}
     */
    formTitle() {
      return this.form.item.name[this.defaultLocale]
    },

    editing() {
      return !!this.form.item.id
    },

    defaultLocale() {
      return this.$store.getters.config.defaultLocale
    },

    localesList() {
      return Object.keys(this.$store.getters.config.locales)
    },

    locales() {
      return this.$store.getters.config.locales
    }
  },

  methods: {

    getNewItem() {
      return this.initItem()
    },

    setNewItemFields() {
      return {
        translatables: [],
        string: [],
        number: [
          'id'
        ],
        array: [],
        object: [],
        boolean: []
      }
    },

    getValidationRules() {
      return this.initRules()
    },

    /* Generates item based on it`s type */
    initItem() {
      let item = {}

      Object.keys(this.setNewItemFields()).map((Key) => {
        if (Key === 'translatables') {
          this.setNewItemFields()[Key].map((key) => {
            // generate translatable items
            item[key] = Object.fromEntries(this.getLocalesList().map(locale => [locale, '']))
          }, this)
        } else {
          this.setNewItemFields()[Key].map((key) => {
            // generate items
            // if array means you don`t want default value from type
            if (Array.isArray(key)) {
              item[key[0]] = key[1]
            } else {
              item[key] = window[`${Key.charAt(0).toUpperCase() + Key.slice(1)}`]()
            }
          }, Key)
        }
      }, this)

      return item
    },

    /* Generates rule based on item type */
    initRules(setRequiredRules = false) {
      let rules = {}

      Object.keys(this.setNewItemFields()).map((Key) => {
        if (Key === 'translatables') {
          this.setNewItemFields()[Key].map((key) => {
            // generate translatable rules
            rules[key] = Object.fromEntries(this.getLocalesList().map(locale => {
              let rules = [
                () => !this.remoteErrors[key] || !this.remoteErrors[key][locale] || this.remoteErrors[key][locale][0]
              ]

              if (locale === this.getDefaultLocale() && setRequiredRules) {
                rules.unshift(val => !!val || this.errorMessages.required)
              }

              return [locale, rules]
            }, [this, key]))
          }, this)
        } else {
          this.setNewItemFields()[Key].map((key) => {
            // generate rules
            if (key !== 'id') {
              rules[key] = []
              if (setRequiredRules) {
                rules[key].push(val => !!val || this.errorMessages.required)
              }
              rules[key].push(() => !this.remoteErrors[key] || this.remoteErrors[key][0])
            }
          }, Key)
        }
      }, this)

      return rules
    },

    resetForm(overwrite = {}) {
      // ommit undefined items from the overwrite object

      this.form.item = Object.assign({},
        Object.keys(this.getNewItem()).length > 1 ? this.getNewItem() : this.initItem(),
        Object.fromEntries(Object.entries(overwrite).filter(pair => pair[1] !== undefined))
      )
    },

    async initForm(route) {
      if (this.beforeInitForm) {
        await this.beforeInitForm(route)
      }

      let params = this.parseURLParams(route.query)
      this.query = params.query

      const id = route.params.id

      if (id) {
        this.edit(id)
        return
      }

      this.create()
    },

    /**
     * Parses the given object, usually from a route's query object
     * @param params
     */
    parseURLParams(params) {
      let query = {}

      Object.entries(params).forEach(pair => {
        query[pair[0]] = pair[1]
      })

      return { query }
    },

    async create() {
      this.remoteErrors = {}
      this.resetForm(this.$route.params)
      await this.$nextTick()
      this.$refs.form.resetValidation()

      // this hook can perform actions after initial data has been loaded
      if (this.afterCreate) {
        await this.afterCreate()
      }
    },

    async edit(id) {
      this.remoteErrors = {}
      this.resetForm(this.$route.params)
      await this.$nextTick()
      this.$refs.form.resetValidation()

      if (this.beforeEdit) {
        await this.beforeEdit()
      }

      this.loading = true
      try {
        let data = await this.$store.dispatch(
          `${this.module}/edit`,
          { id, params: this.$route.params, query: this.query }
        )

        if (this.beforeEditCommit) {
          data = await this.beforeEditCommit(data)
        }

        this.form.item = data

        await this.$nextTick()

        // this hook can perform actions after data has been loaded from the
        // server
        if (this.afterEdit) {
          await this.afterEdit()
        }
      } finally {
        this.loading = false
      }
    },

    getFormItem() {
      return Object.assign({}, this.form.item)
    },

    onSubmit() {
      if (this.form.item.id) {
        return this.update()
      }
      return this.store()
    },

    async store() {
      if (this.beforeStore) {
        await this.beforeStore()
      }

      let params = this.getFormItem()
      delete params.id

      this.loading = true

      try {
        let data = await this.$store.dispatch(`${this.module}/store`, {
          params,
          query: this.query
        })

        if (data.errors) {
          this.remoteErrors = data.errors
          this.$refs.form.validate()
          this.remoteErrors = {} // by doing this, remote errors get cleared right away and do not block resubmit
          return
        }

        if (this.afterStore) {
          await this.afterStore()
        }

        const newRecordId = data.newRecordId
        this.$q.notify({
          closeBtn: 'Dismiss',
          color: 'positive',
          icon: 'fal fa-thumbs-up',
          message: `A new record with the ID ${newRecordId} has been created`,
          position: 'top'
        })

        if (this.listingAfterStore) {
          this.$router.push({ name: `${this.module}.listing`, params: this.$route.params, query: this.query })
        } else {
          this.$router.push({ name: `${this.module}.edit`, params: Object.assign(this.$route.params, { id: newRecordId }), query: this.query })
        }
      } finally {
        this.loading = false
      }
    },

    async update() {
      if (this.beforeUpdate) {
        await this.beforeUpdate()
      }

      let params = this.getFormItem()
      delete params.id

      this.loading = true

      try {
        let data = await this.$store.dispatch(`${this.module}/update`, {
          id: this.form.item.id,
          params,
          query: this.query
        })

        if (data.errors) {
          this.remoteErrors = data.errors
          this.$refs.form.validate()
          this.remoteErrors = {} // by doing this, remote errors get cleared right away and do not block resubmit
          return
        }

        if (!data.success) {
          return
        }

        if (this.afterUpdate) {
          await this.afterUpdate()
        }

        this.$q.notify({
          closeBtn: 'Dismiss',
          color: 'positive',
          icon: 'fal fa-thumbs-up',
          message: `The record with the ID ${this.form.item.id} has been updated`,
          position: 'top'
        })

        if (this.listingAfterUpdate) {
          this.$router.push({ name: `${this.module}.listing`, params: this.$route.params, query: this.query })
        }
      } finally {
        this.loading = false
      }
    },

    getDefaultLocale() {
      return this.$store.getters.config.defaultLocale
    },

    getLocalesList() {
      return Object.keys(this.$store.getters.config.locales)
    }
  },

  /**
   * Called when first entering the module
   * @param to
   * @param from
   * @param next
   */
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (!vm.module) {
        vm.initForm(to)
      }
    })
  },

  /**
   * Called when changing sorting or filtering and upon each move back or forth in the history
   * @param to
   * @param from
   * @param next
   */
  beforeRouteUpdate(to, from, next) {
    this.initForm(to)
    next()
  },

  /**
   * Called at the same time as beforeRouteEnter and after hot reloading
   */
  mounted() {
    if (this.module) {
      this.initForm(this.$route)
    }
  }
}
