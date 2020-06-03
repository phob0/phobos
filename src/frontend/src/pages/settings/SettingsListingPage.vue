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
          <h4 class="q-mb-none">Settings</h4>
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

      <template v-slot:body-cell-status="props">
        <q-td :props="props">
          <i :class="`font-size-xl fal fa-${props.row.status === 'I' ? 'lightbulb-slash' : props.row.status === 'A' ? 'lightbulb-on' : 'lightbulb'} text-${props.row.status === 'I' ? 'negative' : props.row.status === 'A' ? 'positive' : 'warning'}`"></i>
          <q-tooltip
            anchor="top middle"
            self="top middle">{{ props.row.status }}</q-tooltip>
        </q-td>
      </template>

      <template v-slot:body-cell-updated_at="props">
        <q-td>
          {{ toLocalDowDate(props.row.updated_at) }}
          <div class="font-size-sm text-faded">{{ toLocalTime(props.row.updated_at) }}</div>
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
        </q-td>
      </template>

    </q-table>
  </q-page>
</template>

<script>
import ListsItems from '../../mixins/ListsItems'

export default {
  name: 'SettingsListingPage',

  mixins: [ListsItems],

  props: {
    module: {
      type: String,
      default: 'setting'
    }
  },

  data() {
    return {
      table: {
        columns: [{
          align: 'left',
          field: 'setting',
          label: 'Setting',
          name: 'setting',
          sortable: true,
          required: true
        }, {
          align: 'left',
          field: 'value',
          label: 'Value',
          name: 'value',
          required: true
        }, {
          align: 'right',
          headerClasses: 'q-table--kps-auto-width',
          label: 'Updated',
          name: 'updated_at',
          visible: false
        }, {
          headerClasses: 'q-table--kps-auto-width',
          label: 'Options',
          name: 'options',
          required: true
        }]
      }
    }
  }
}
</script>
