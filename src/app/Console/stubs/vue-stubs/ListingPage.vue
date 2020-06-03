<template>
    <q-page>
        <q-table
          binary-state-sort
          bordered
          class="kps-table q-ma-sm"
          :columns="table.columns"
          :data="items"
          :loading="loading"
          :pagination="pagination"
          @request="onLoad"
          row-key="id"
          :rows-per-page-options="[10,20,50,100]"
          separator="cell"
          :visible-columns="visibleColumns"
        >
          <template v-slot:top="props">
            <div class="col-xs-12 col-sm">
              <h4 class="q-mb-none">Dummy</h4>
            </div>

            <q-space />

            <div class="col-xs-12 col-sm-auto text-center q-pt-xs-md">

              <q-btn
                color="accent"
                flat
                icon="fal fa-plus-circle"
                round
                :to="{ name: `${module}.create`, query }"
              >
                <q-tooltip
                  anchor="bottom middle"
                  self="center middle">New Record</q-tooltip>
              </q-btn>

              <q-btn
                class="on-right"
                @click="table.filtering = !table.filtering"
                :color="table.filtering ? 'accent' : 'primary'"
                flat
                icon="fal fa-filter"
                round
              >
                <q-tooltip
                  anchor="bottom middle"
                  self="center middle">Filters</q-tooltip>
              </q-btn>

              <q-btn
                class="on-right"
                @click="onReload"
                color="primary"
                flat
                icon="fal fa-sync"
                round
              >
                <q-tooltip
                  anchor="bottom middle"
                  self="center middle">Reload</q-tooltip>
              </q-btn>

              <div class="inline-block">
                <q-btn-dropdown
                  class="on-right"
                  color="primary"
                  flat
                  icon="fal fa-line-columns"
                  rounded
                >
                  <q-list dense>
                    <q-item-label header>Visible Columns</q-item-label>
                    <q-item
                      @click="item.visible = !item.visible; persistLayout()"
                      clickable
                      :key="item.name"
                      v-for="item in nonRequiredColumns"
                    >
                      <q-item-section side top>
                        <q-checkbox
                          @input="persistLayout"
                          v-model="item.visible" />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>{{ item.label }}</q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </q-btn-dropdown>

                <q-tooltip
                  anchor="bottom middle"
                  self="center middle">Visible Columns</q-tooltip>
              </div>
            </div>
            <q-slide-transition>
              <div
                class="col-xs-12"
                v-show="table.filtering">
                <div class="q-mt-sm q-pt-sm kps-top-border">
                  <div class="row q-col-gutter-sm">
                    <div class="col-xs-12 font-size-lg text-weight-medium text-faded">Filter Records</div>

                    <div class="col-xs-6 col-sm-auto">
                      <q-select
                        emit-value
                        label="Status"
                        map-options
                        multiple
                        :options="$store.getters.lookupStatuses"
                        outlined
                        @remove="restrictLastOptionRemoval('filters.status', $event)"
                        style="min-width: 8rem;"
                        v-model="filters.status"
                      ></q-select>
                    </div>

                    <div class="col-xs-6 col-sm-auto">
                      <q-input
                        label="Name"
                        outlined
                        v-model="filters.name"
                        clearable
                      ></q-input>
                    </div>

                    <div class="col-xs-12">
                      <q-btn
                        class="q-mt-xs"
                        @click="onLoad"
                        color="accent"
                        icon="fal fa-filter"
                        label="Apply"
                      ></q-btn>
                    </div>
                  </div>
                </div>
              </div>
            </q-slide-transition>
          </template>

          <template v-slot:header="props">
            <q-tr class="bg-blue-grey-2">
              <q-th
                :key="col.name"
                :props="props"
                v-for="col in props.cols"
              >{{ col.label }}</q-th>
            </q-tr>
          </template>

          <template v-slot:body-cell-name="props">
            <q-td>
              {{ props.row.name }}
            </q-td>
          </template>

          <template v-slot:body-cell-options="props">
            <q-td :props="props">
              <q-btn
                class="on-right"
                color="primary"
                flat
                icon="fal fa-edit"
                round
                :to="{ name: `${module}.edit`, params: { id: props.row.id }, query }"
              >
                <q-tooltip
                  anchor="top middle"
                  self="center middle"
                >Edit</q-tooltip>
              </q-btn>
              <q-btn
                class="on-right"
                @click="confirmRemove(props.row.id)"
                color="accent"
                flat
                icon="fal fa-trash-alt"
                round
              >
                <q-tooltip
                  anchor="top middle"
                  self="center middle">Delete</q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-page>
</template>

<script>
import ListsItems from '../../mixins/ListsItems'

export default {
  name: 'DummyListingPage',

  mixins: [ListsItems],

  props: {
    module: {
      type: String,
      default: 'dummy'
    }
  },

  data() {
    return {
      table: {
        columns: [{
          align: 'right',
          field: 'id',
          headerClasses: 'q-table--kps-auto-width',
          label: 'ID',
          name: 'id',
          sortable: true,
          visible: false
        }, {
          align: 'left',
          label: 'Name',
          name: 'name',
          sortable: true,
          required: true
        }, {
          headerClasses: 'q-table--kps-auto-width',
          label: 'Options',
          name: 'options',
          required: true
        }]
      }
    }
  },

  methods: {
    getFilters() {
      return {
        status: ['A'],
        name: ''
      }
    }
  }
}
</script>

<style scoped>

</style>
