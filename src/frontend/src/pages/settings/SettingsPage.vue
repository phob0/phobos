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
            <h4 class="q-mb-none">{{ loading ? 'Please Wait' : (form.item.id ? 'Edit Setting' : 'Create Setting') }}</h4>
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
            v-go-back.single
          >
            <q-tooltip
              anchor="bottom middle"
              self="center middle">Go Back</q-tooltip>
          </q-btn>
        </q-card-section>

        <q-card-section id="section-general" class="relative-position">
          <div class="row q-col-gutter-sm">

            <q-input
              class="col-xs-12"
              :disable="editing"
              outlined
              label="Setting"
              :rules="form.rules.setting"
              v-model="form.item.setting"
            />

            <q-select
              class="col-xs-6"
              :disable="editing"
              emit-value
              label="Control Type"
              map-options
              outlined
              :options="settingTypeOptions"
              :rules="form.rules.setting_type"
              v-model="form.item.setting_type"
            />

            <q-field
              borderless
              class="col-xs-6"
              :disable="editing && initialSecured"
              :rules="form.rules.secured"
              v-model="form.item.secured"
            >
              <q-checkbox
                label="Protected"
                right-label
                v-model="form.item.secured"
              />
            </q-field>

            <q-input
              class="col-xs-12"
              :disable="editing && initialSecured"
              label="Value"
              outlined
              rows="4"
              :rules="form.rules.value"
              :type="controlType"
              v-model="form.item.value"
            />

          </div>

          <q-inner-loading :showing="loading">
            <i class="fal fa-fan fa-spin fa-3x text-accent"></i>
          </q-inner-loading>
        </q-card-section>

        <q-card-actions class="justify-end">
          <q-btn
            @click="onPreviousHistory"
            color="secondary"
            :disabled="!hasPreviousValue || loading || initialSecured"
            icon="fal fa-undo"
            label="Undo"
            outline
          />

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
  name: 'SettingsPage',

  mixins: [EditsItem],

  props: {
    module: {
      type: String,
      default: 'setting'
    }
  },

  data() {
    return {
      form: {
        item: this.getNewItem(),
        rules: this.getValidationRules()
      },
      previousHistory: null,
      nextHistory: null,

      settingTypeOptions: [
        { label: 'Boolean', value: 'bool' },
        { label: 'Float', value: 'float' },
        { label: 'Integer', value: 'int' },
        { label: 'JSON', value: 'json' },
        { label: 'String', value: 'string' }
      ],

      initialSecured: false
    }
  },

  computed: {
    controlType() {
      switch (this.form.item.setting_type) {
        case 'json':
          return 'textarea'
        default:
          return 'input'
      }
    },

    hasPreviousValue() {
      return this.form.item.history.length
    }
  },

  methods: {
    getNewItem() {
      return {
        id: 0,
        setting: '',
        value: '',
        setting_type: 'string',
        secured: false,
        history: []
      }
    },

    getValidationRules() {
      return {
        setting: [
          val => !!val || this.errorMessages.required,
          () => !this.remoteErrors['setting'] || this.remoteErrors['setting'][0]
        ],
        value: [
          () => !this.remoteErrors['value'] || this.remoteErrors['value'][0]
        ],
        setting_type: [
          val => !!val || this.errorMessages.required,
          () => !this.remoteErrors['setting_type'] || this.remoteErrors['setting_type'][0]
        ],
        secured: [
          () => !this.remoteErrors['setting_type'] || this.remoteErrors['setting_type'][0]
        ]
      }
    },

    onPreviousHistory() {
      let item = this.form.item.history.pop()
      this.form.item.value = item.value
    },

    beforeEditCommit(data) {
      this.initialSecured = data.secured
      return data
    }
  }
}
</script>
