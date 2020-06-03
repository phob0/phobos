<template>
  <q-dialog
    v-model="$attrs.value"
    :persistent="!elevate"
  >
    <q-card class="kps-submodal">
      <q-form
        ref="form"
        @submit="onLogin"
      >
        <q-card-section>
          <h4>{{ elevate ? 'Forbidden' : 'My Account' }}</h4>
          <div
            class="font-size-sm text-faded"
            v-if="elevate"
          >Your current role does not allow you to perform this action. Please log into an account with more privileges
            and then retry your action</div>
        </q-card-section>

        <q-card-section>

          <q-input
            autocomplete="email"
            class="col-xs-12"
            label="Email Address"
            name="email"
            :rules="rules.email"
            v-model="form.email"
          ></q-input>

          <q-input
            autocomplete="current-password"
            class="col-xs-12"
            label="Password"
            name="password"
            :rules="rules.password"
            type="password"
            v-model="form.password"
          ></q-input>

          <q-field
            class="col-xs-12"
            hint="Keep me logged in if I close the browser"
          >
            <q-checkbox
              label="Remember me"
              name="remember"
              v-model="form.remember"
            ></q-checkbox>
          </q-field>

        </q-card-section>

        <q-card-actions>
          <q-space></q-space>
          <q-btn
            color="secondary"
            :disable="loading"
            label="Cancel"
            outline
            type="button"
            v-close-popup
            v-if="elevate"
          ></q-btn>
          <q-btn
            color="accent"
            label="Login"
            :loading="loading"
            type="submit"
          ></q-btn>
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script>
import EditsItem from '../../mixins/EditsItem'

export default {
  name: 'Login',

  mixins: [EditsItem],

  props: {
    elevate: {
      type: Boolean,
      default: false
    }
  },

  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: true
      },

      rules: {
        email: [
          v => !!v || this.errorMessages.required,
          () => !this.remoteErrors['email'] || this.remoteErrors['email'][0]
        ],
        password: [
          v => !!v || this.errorMessages.required,
          () => !this.remoteErrors['password'] || this.remoteErrors['password'][0]
        ]
      }
    }
  },

  methods: {
    onLogin() {
      this.loading = true

      let data = Object.assign({}, this.form)
      delete data.rules // inherited from mixin

      this.$store
        .dispatch('login', { data })
        .then(data => {
          if (data.errors) {
            this.remoteErrors = data.errors
            this.$refs.form.validate()
            this.remoteErrors = {} // by doing this, remote errors get cleared right away and do not block resubmit
          } else {
            this.$store.commit('elevate', false)
          }
        })
        .finally(() => { this.loading = false })
    }
  }
}
</script>
