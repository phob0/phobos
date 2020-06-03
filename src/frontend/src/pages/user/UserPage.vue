<template>
  <q-page padding>
    <q-form
      autofocus
      ref="form"
      @submit="onSubmit"
    >
      <q-card class="kps-form">
        <q-card-section class="row items-center">
          <div>
            <h4 class="q-mb-none">{{ loading ? 'Please Wait' : formHeaderText }}</h4>
            <div
              v-if="editing"
              class="text-faded"
              v-text="formTitle"
            ></div>
          </div>
          <q-space></q-space>
          <q-btn
            color="tertiary"
            flat
            icon="fal fa-arrow-alt-left"
            round
            v-if="!ownAccount"
            v-go-back.single
          >
            <q-tooltip
              anchor="bottom middle"
              self="center middle">Go Back</q-tooltip>
          </q-btn>
        </q-card-section>

        <q-card-section id="section-general" class="relative-position">
          <div class="row q-col-gutter-sm">

            <q-select
              class="col-xs-6"
              emit-value
              label="Role"
              map-options
              :options="rolesOptions"
              outlined
              :readonly="editing && !$store.getters.isAdmin"
              :rules="form.rules.role"
              v-model="form.item.role"
              v-show="!hasRoleQuery(query)"
            />

            <q-input
              class="col-xs-6"
              label="Name"
              outlined
              :rules="form.rules.name"
              v-model="form.item.name"
            />

            <q-input
              autocomplete="off"
              class="col-xs-6"
              label="Email"
              outlined
              :rules="form.rules.email"
              v-model="form.item.email"
            />

            <q-input
              autocomplete="off"
              class="col-xs-6"
              label="Phone number"
              outlined
              :rules="form.rules.phone"
              v-model="form.item.phone"
            />

            <q-input
              autocomplete="off"
              class="col-xs-6"
              label="Password"
              outlined
              :rules="form.rules.password"
              type="password"
              v-model="form.item.password"
            />

            <q-select
              class="col-xs-6"
              emit-value
              label="Location"
              map-options
              :options="locationOptions"
              outlined
              :readonly="editing && !$store.getters.isAdmin"
              :rules="form.rules.location"
              v-model="form.item.location"
              v-show="hasRoleQuery(query) && query.role === 'responsible'"
            />

          </div>

          <q-inner-loading :showing="loading">
            <i class="fal fa-fan fa-spin fa-3x text-accent"></i>
          </q-inner-loading>
        </q-card-section>

        <q-card-actions class="justify-end">
          <q-btn
            color="accent"
            :loading="loading"
            label="Submit"
            type="submit"
          />
        </q-card-actions>
      </q-card>
    </q-form>
  </q-page>
</template>

<script>
import EditsItem from '../../mixins/EditsItem'

export default {
  name: 'UserPage',

  mixins: [EditsItem],

  props: {
    module: {
      type: String,
      default: 'user'
    }
  },

  data() {
    return {
      form: {
        item: this.getNewItem(),
        rules: this.getValidationRules()
      },

      ownAccount: false,
      locationOptions: []
    }
  },

  computed: {
    formHeaderText() {
      switch (this.query.role) {
        case 'responsible':
          return this.form.item.id ? 'Edit regional responsible' : 'Create regional responsible'
        case 'nurse':
          return this.form.item.id ? 'Edit intervention nurse' : 'Create intervention nurse'
        case 'collector':
          return this.form.item.id ? 'Edit data collector' : 'Create data collector'
        default:
          return this.form.item.id ? 'Edit user' : 'Create user'
      }
    },
    rolesOptions() {
      return this.$store.getters.lookupRoles
    }
  },

  methods: {
    getNewItem() {
      return {
        id: 0,
        email: '',
        name: '',
        password: '',
        phone: '',
        role: null,
        location: null
      }
    },

    hasRoleQuery(query) {
      return Object.prototype.hasOwnProperty.call(query, 'role')
    },

    async initLocations() {
      this.loading = true
      try {
        let data = await this.$store.dispatch('location/list', {
          pagination: 1,
          filters: [],
          query: { perPage: 1000 }
        })
        this.locationOptions = data.data.map(location => ({ label: location.city_name, value: location.id }))
      } finally {
        this.loading = false
      }
    },

    getValidationRules() {
      return {
        email: [
          val => !!val || this.errorMessages.required,
          () => !this.remoteErrors['email'] || this.remoteErrors['email'][0]
        ],
        name: [
          val => !!val || this.errorMessages.required,
          () => !this.remoteErrors['name'] || this.remoteErrors['name'][0]
        ],
        password: [
          val => this.editing || !!val || this.errorMessages.required,
          () => !this.remoteErrors['password'] || this.remoteErrors['password'][0]
        ],
        phone: [
          () => !this.remoteErrors['phone'] || this.remoteErrors['phone'][0]
        ],
        role: [
          val => !!val || this.errorMessages.required,
          () => !this.remoteErrors['role'] || this.remoteErrors['role'][0]
        ],
        location: [
          val => !!val || this.errorMessages.required,
          () => !this.remoteErrors['location'] || this.remoteErrors['location'][0]
        ]
      }
    },

    beforeInitForm(route) {
      if (this.hasRoleQuery(route.query) && route.query.role === 'responsible') {
        this.initLocations()
      }
    },

    afterCreate() {
      if (this.hasRoleQuery(this.query)) {
        this.form.item.role = this.query.role
        if (this.query.role === 'responsible') {
          this.form.item.location = null
        } else {
          this.form.item.location = 'none'
        }
      } else {
        this.form.item.location = 'none'
      }
    },

    beforeEdit() {
      // save my account flag and prevent propagation to other requests
      this.ownAccount = this.query.my_account
      delete this.query.my_account
    },

    beforeEditCommit(data) {
      data.role = data.role.name
      if (this.hasRoleQuery(this.query)) {
        data.location = data.location.id
      } else {
        data.location = 'none'
      }

      return data
    },

    getFormItem() {
      let data = Object.assign({}, this.form.item)
      delete data.email_hash

      if (!this.hasRoleQuery(this.query)) {
        delete data.location
      }

      return data
    }
  }
}
</script>
