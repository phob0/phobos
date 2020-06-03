import PersistsLayout from './PersistsLayout'
import HandlesDates from './HandlesDates'

import get from 'lodash/get'
import set from 'lodash/set'

export default {
  mixins: [PersistsLayout, HandlesDates],

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
      // list filters
      filters: this.getFilters(),

      // additional query params
      query: {},

      // list items
      items: [],

      // list pagination
      pagination: {
        descending: false,
        page: 1,
        rowsPerPage: 10,
        rowsNumber: 0,
        sortBy: ''
      },

      // table definition
      table: {
        columns: [],
        filtering: false
      },

      loading: false
    }
  },

  computed: {
    /**
     * The listing columns which may be hidden
     */
    nonRequiredColumns() {
      return this.table.columns.filter(item => !item.required)
    },

    /**
     * The initially visible listing columns. Required columns are visible by default
     */
    visibleColumns() {
      return this.table.columns.filter(item => item.visible).map(item => item.name)
    },

    columns() {
      return Object.fromEntries(this.table.columns.map(item => [item.name, item]))
    }
  },

  methods: {
    async initList(route) {
      if (this.beforeInitList) {
        await this.beforeInitList(route)
      }

      this.restoreLayout()
      this.resetFilters()

      let params = this.parseURLParams(route.query)
      this.query = params.query

      await this.list({
        pagination: params.pagination,
        filters: Object.assign({}, this.filters, params.filters)
      })

      this.table.filtering = this.hasActiveFilters(this.filters)

      // persists the layout in a cookie
      this.persistLayout()
    },

    /**
     * Returns the stored layout settings
     */
    restoreLayout() {
      let cookieVal = this._restoreLayout()
      if (cookieVal === false) {
        return
      }

      if (cookieVal.listing) {
        this.table.columns.forEach(column => {
          if (cookieVal.listing.columnVisibility[column.name] !== undefined) {
            column.visible = cookieVal.listing.columnVisibility[column.name]
          }
        })
      }
    },

    resetFilters() {
      this.filters = this.getFilters()
    },

    getFilters() {
      return {}
    },

    /**
     * Parses the given object, usually from a route's query object and returns pagination and filters objects
     * @param params
     */
    parseURLParams(params) {
      let filters = {}
      let pagination = {}
      let query = {}

      const paginationMap = {
        page: 'page',
        rowsPerPage: 'rowsPerPage',
        rowsNumber: 'rowsNumber',
        sortBy: 'sortBy',
        descending: 'descending'
      }

      Object.entries(params).forEach(pair => {
        if (pair[0].indexOf('filters_') === 0) {
          const filterName = pair[0].match(/^filters_(.+)$/)[1]

          if (this.filters[filterName] !== undefined &&
            this.filters[filterName] !== null &&
            typeof this.filters[filterName] === 'object' &&
            this.filters[filterName].length !== undefined) {
            // array filter
            if (typeof pair[1] === 'object') {
              // already array
              filters[filterName] = pair[1]
            } else {
              // push scalar into array
              if (filters[filterName] === undefined) {
                filters[filterName] = []
              }
              filters[filterName].push(pair[1])
            }
          } else {
            // scalar filter
            filters[filterName] = pair[1]
          }
        } else if (Object.keys(paginationMap).indexOf(pair[0]) >= 0) {
          pagination[paginationMap[pair[0]]] = pair[1]
        } else {
          query[pair[0]] = pair[1]
        }
      })

      return { pagination, filters, query }
    },

    /**
     * Requests a page of items
     * @param pagination
     * @param filters
     * @param params
     */
    async list({ pagination = this.pagination, filters = this.filters, query = this.query } = {}) {
      this.loading = true

      try {
        let data = await this.$store.dispatch(`${this.module}/list`, {
          pagination,
          filters: this.buildFilters(filters),
          query
        })

        this.items = data.data
        this.setPagination(data.meta)
        this.setFilters(data.meta.filters)
      } finally {
        this.loading = false
      }
    },

    /**
     * Prepare filters for being sent back to the server
     *
     * @param filters
     * @param forQuery
     */
    buildFilters(filters, forQuery = false) {
      let ret = {}

      Object.entries(filters).forEach(pair => {
        if (pair[1] === undefined || pair[1] === null) {
          return
        }

        ret[forQuery ? `filters_${pair[0]}` : pair[0]] = pair[1]
      })

      return ret
    },

    setPagination(meta) {
      if (meta.current_page) {
        // comes from the API
        this.pagination.page = meta.current_page
        this.pagination.rowsPerPage = meta.per_page
        this.pagination.rowsNumber = meta.total
        this.pagination.sortBy = meta.sort_by
        this.pagination.descending = meta.sort_order === 'desc'

        return
      }

      // comes from a component
      this.pagination.page = meta.page
      this.pagination.rowsPerPage = meta.rowsPerPage
      this.pagination.rowsNumber = meta.rowsNumber
      this.pagination.sortBy = meta.sortBy
      this.pagination.descending = meta.descending
    },

    setFilters(filters) {
      for (let i in this.filters) {
        const isFilterArray = this.filters[i] !== null &&
          typeof this.filters[i] === 'object' &&
          this.filters[i].length !== undefined

        const isValueArray = filters[i] !== undefined &&
          filters[i] !== null &&
          typeof filters[i] === 'object' &&
          filters[i].length !== undefined

        this.filters[i] = filters[i] === undefined
          ? (isFilterArray ? [] : null)
          : (isFilterArray
            ? (isValueArray
              ? filters[i]
              : (filters[i] !== null ? [filters[i]] : null))
            : filters[i])
      }
    },

    /**
     * Navigates to a new listing route, using the given pagination and filters and defaulting to the current ones
     * @param pagination
     * @param filters
     */
    async onLoad({ pagination = this.pagination, filters = this.filters } = {}) {
      let query = this.buildURLParams({ pagination, filters })
      try {
        await this.$router.push({ name: `${this.module}.listing`, query })
      } catch (ex) {
        // tried to load same URL. Just reload
        this.onReload()
      }
    },

    /**
     * Reloads the items with the current pagination and filters
     */
    onReload() {
      this.list().then(() => {
        this.table.filtering = this.hasActiveFilters(this.filters)
      })
    },

    /**
     * Decides if the filters drawer will open. Active status filter doesn't
     * count
     * @param obj
     * @returns {boolean}
     * @private
     */
    hasActiveFilters(obj) {
      return Object.entries(obj).some(pair => (pair[1] !== null && // has value
        (typeof pair[1] !== 'object' || pair[1].length > 0) && // but not empty array
        (pair[0] !== 'status' || pair[1].length > 1 || pair[1][0] !== 'A')))
    },

    /**
     * Builds a parameters object, ready to be fed to a route, starting from the given pagination and filters objects
     * @param pagination
     * @param filters
     * @param extra
     */
    buildURLParams({ pagination = {}, filters = {}, query = this.query }) {
      let params = {}

      Object.entries(Object.assign({}, pagination, query)).forEach(pair => {
        if (pair[1] === undefined || pair[1] === null) {
          return
        }

        params[pair[0]] = pair[1]
      })

      Object.assign(params, this.buildFilters(filters, true))

      return params
    },

    getLayoutKey() {
      return this.module
    },

    /**
     * Persists listing columns in a cookie
     */
    persistLayout() {
      let columnVisibility = {}
      this.table.columns.filter(item => item.visible !== undefined).forEach(item => {
        columnVisibility[item.name] = item.visible
      })

      let val = {
        listing: {
          columnVisibility
        }
      }

      this._persistLayout(val)
    },

    /**
     * Doesn't let the user remove the last option of a multiple selection
     * field. May be used as the @remove event handler on a q-select
     *
     * @param selectField
     * @param details
     */
    restrictLastOptionRemoval(selectField, details) {
      if (get(this, selectField).length > 1) {
        return
      }

      this.$nextTick(() => {
        set(this, selectField, details.value)
      })
    },

    /**
     * Confirm record removal
     * @param {number} id
     */
    confirmRemove(id) {
      this.$q.dialog({
        title: 'Please Confirm',
        message: 'This action may be undoable. Are you sure you want to remove this record?',
        cancel: 'No',
        ok: 'Yes, remove it'
      }).onOk(async v => {
        this.loading = true

        try {
          let data = await this.$store.dispatch(`${this.module}/destroy`, { id, query: this.query })

          if (data.errors) {
            return
          }

          this.$q.notify({
            closeBtn: 'Dismiss',
            color: 'positive',
            icon: 'fal fa-thumbs-up',
            message: `The record with the ID ${id} has been removed`,
            position: 'top'
          })

          this.onReload()
        } finally {
          this.loading = false
        }
      })
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
        vm.initList(to)
      }
    })
  },

  /**
   * Called when changing sorting or filtering and upon each move back or forth in the history
   * @param to
   * @param from
   * @param next
   */
  async beforeRouteUpdate(to, from, next) {
    this.initList(to)
    next()
  },

  /**
   * Called at the same time as beforeRouteEnter and after hot reloading
   */
  mounted() {
    if (this.module) {
      this.initList(this.$route)
    }
  }
}
