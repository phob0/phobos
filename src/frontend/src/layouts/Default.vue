<template>
  <q-layout view="hHh lpR fFf">
    <q-header reveal elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          :disable="!this.user"
          round
          @click="onSideMenuMove"
          aria-label="Menu"
        >
          <q-icon name="fal fa-bars" />
        </q-btn>

        <q-toolbar-title>
          <router-link
            class="kps-dashboard-link"
            :to="{ name: 'dashboard' }">Phobo <span class="text-weight-bold">Broth</span></router-link>
        </q-toolbar-title>

        <div v-if="user">
          <q-btn-dropdown
            auto-close
            no-caps
            no-wrap
            unelevated
          >
            <span slot="label">
              <q-avatar
                class="on-left"
                v-if="userAvatar"
              ><img :src="userAvatar"></q-avatar>
              <span v-if="$q.screen.gt.xs">{{ user.name }}</span>
            </span>

            <q-list>
              <q-item
                class="text-center q-pt-md bg-quaternary"
                v-if="$q.screen.xs"
              >
                {{ user.name }}
              </q-item>
              <q-item
                @click="onLogout"
                clickable
                v-ripple
              >
                <q-item-section avatar>
                  <q-avatar icon="fal fa-door-open"></q-avatar>
                </q-item-section>
                <q-item-section>
                  Logout
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </div>
        <div v-else>{{ version }}</div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      :mini="leftDrawerMini"
      no-swipe-open
      no-swipe-close
      content-class="bg-secondary"
    >
      <q-list separator>
        <template v-for="(menuItem, ix) in menu">
          <side-menu-item
            :key="ix"
            :data="menuItem"
            :mini="leftDrawerMini"
            :depth="1"
            :active="active"
            @click="active = $event.code || active"></side-menu-item>
        </template>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>

    <login
      :elevate="$store.state.elevate"
      v-model="displayLogin"
    ></login>
  </q-layout>
</template>

<style scoped lang="stylus">
  >>> .q-drawer--mini
    .q-expansion-item__content
      display inherit

  .kps-dashboard-link
    color white
    text-decoration none
    &:hover
      color $blue-grey-2
</style>

<script>
import { openURL } from 'quasar'
import PersistsLayout from '../mixins/PersistsLayout'

export default {
  name: 'Default',

  mixins: [PersistsLayout],

  components: {
    SideMenuItem: () => import(/* webpackChunkName: 'app_base' */ '../components/layout/SideMenuItem'),
    Login: () => import(/* webpackChunkName: 'app_base' */ '../components/authentication/Login')
  },

  data() {
    return {
      leftDrawerMini: false,
      leftDrawerOpen: true,
      active: ''
    }
  },

  computed: {
    user() {
      return this.$store.state.config.user
    },

    menu() {
      return this.$store.getters.sideMenu
    },

    version() {
      return this.$store.getters.version
    },

    error() {
      return this.$store.getters.error
    },

    displayLogin() {
      return !this.$store.getters.loggedIn || this.$store.state.elevate
    },

    userAvatar() {
      return `https://www.gravatar.com/avatar/${this.user.email_hash || ''}?d=identicon`
    }
  },

  watch: {
    error(n) {
      if (!n.message) {
        return
      }

      this.$q.dialog({
        noBackdropDismiss: true,
        ok: {
          label: n.fatal ? 'Reload App' : 'Dismiss'
        },
        persistent: n.fatal,
        message: n.message,
        title: n.fatal ? 'Fatal Error' : 'Error'
      }).onOk(() => {
        if (n.fatal) {
          document.location.reload()
        }
      })
    },

    user() {
      this.leftDrawerOpen = !!this.user && this.$q.screen.gt.sm
    }
  },

  methods: {
    openURL,

    onSideMenuMove() {
      if (this.$q.screen.gt.sm) {
        this.leftDrawerMini = !this.leftDrawerMini
        this.leftDrawerOpen = true
      } else {
        this.leftDrawerMini = false
        this.leftDrawerOpen = !this.leftDrawerOpen
      }
      this._persistLayout({
        leftDrawerMini: this.leftDrawerMini
      })
    },

    onLogout() {
      this.$store.dispatch('logout')
    }
  },

  async created() {
    let cookieVal = this._restoreLayout()
    if (cookieVal.leftDrawerMini !== undefined) {
      this.leftDrawerMini = cookieVal.leftDrawerMini
    }

    // this.leftDrawerOpen = !!this.user && this.$q.screen.gt.sm
  }
}
</script>

<style>
</style>
