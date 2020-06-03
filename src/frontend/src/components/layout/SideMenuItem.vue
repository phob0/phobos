<template>
  <q-item
    v-if="!data.children || !data.children.length"
    v-model="expanded"
    clickable
    tag="a"
    :target="data.target"
    :href="data.link"
    :to="data.route"
    class="kps-side-menu-item q-touch"
    :class="{ [`kps-depth-${depth}`]: !!depth, 'kps-active': active == data.code }"
    @click="$emit('click', Object.assign($event, { code: data.code }))">
    <q-item-section avatar>
      <q-icon :name="data.icon"/>
      <q-tooltip
        v-if="mini"
        anchor="center right"
        self="center left">{{ data.label }}</q-tooltip>
    </q-item-section>
    <q-item-section>
      <q-item-label
        class="text-weight-medium"
        v-text="data.label"></q-item-label>
      <q-item-label
        v-if="data.caption"
        caption
        v-text="data.caption"></q-item-label>
    </q-item-section>

    <q-item-section
      side
      v-if="data.side"
    >
      <q-btn
        :color="data.side.color"
        :href="data.side.link"
        :icon="data.side.icon"
        :label="data.side.label"
        round
        size="sm"
        tag="a"
        :to="data.side.route"
      >
      </q-btn>
    </q-item-section>
  </q-item>

  <q-expansion-item
    v-else
    v-model="expanded"
    :expand-icon-class="`text-light-green-1${mini ? ' hidden': ''}`"
    class="kps-side-menu-item q-touch"
    :class="{ [`kps-depth-${depth}`]: !!depth }"
    @click="$emit(expanded ? 'expand' : 'collapse', $event)"
    :group="`side-menu-collapsible-${depth}`">
    <q-item slot="header">
      <q-item-section avatar>
        <q-icon :name="data.icon"/>
        <q-tooltip
          v-if="mini"
          anchor="center right"
          self="center left">{{ data.label }}</q-tooltip>
      </q-item-section>
      <q-item-section>
        <q-item-label
          class="text-weight-medium"
          v-text="data.label"></q-item-label>
        <q-item-label
          v-if="data.caption"
          caption
          v-text="data.caption"/>
      </q-item-section>
    </q-item>

    <q-list
      separator
      class="bg-primary">
      <side-menu-item
        v-for="(submenuItem, subIx) in data.children"
        :key="subIx"
        :data="submenuItem"
        :mini="mini"
        :depth="depth + 1"
        :active="active"
        @click="$emit('click', $event)"/>
    </q-list>
  </q-expansion-item>
</template>

<style scoped lang="stylus">
  .kps-side-menu-item
    color $quaternary
    font-size 16px

    // menu item caption color
    &.kps-depth-1
      .text-caption
        color $tertiary
    // if using different 1st level caption color
    // .kps-side-menu-item
    //   .text-caption
    //     color $tertiary

    // get rid of expansion items padding and make inner header slot full width
    >>> .q-expansion-item__container
      > .q-item
        padding 0
        > .q-item
          width 100%

    // identify active menu item
    &.kps-active
      .q-item__section--avatar
        color $brand
</style>

<script>
export default {
  name: 'SideMenuItem',

  props: {
    data: {
      type: Object,
      default: () => ({})
    },

    mini: {
      type: Boolean,
      default: false
    },

    depth: {
      type: Number,
      default: 1
    },

    active: {
      type: String,
      default: ''
    }
  },

  data() {
    return {
      expanded: false
    }
  }
}
</script>
