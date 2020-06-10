<template>
    <q-page padding="">
        <q-form
          autofocus
          ref="form"
          @submit="onSubmit"
        >
          <q-card class="kps-form">
            <q-card-section class="row items-center">
              <div>
                <h4 class="q-mb-none">{{ loading ? 'Please Wait' : (form.item.id ? 'Edit Dummy' : 'Create Dummy') }}</h4>
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
                  autocomplete="off"
                  class="col-xs-12"
                  label="Name"
                  outlined
                  :rules="form.rules.name"
                  v-model="form.item.name"
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
  name: 'DummyPage',

  mixins: [EditsItem],

  props: {
    module: {
      type: String,
      default: 'dummy'
    }
  },

  data() {
    return {
      form: {
        item: this.getNewItem(),
        rules: this.getValidationRules()
      },

      name_translating: false
    }
  },

  methods: {
    getValidationRules() {
      return this.initRules(true)
    },
    setNewItemFields() {
      return {
        translatables: [],
        string: [
          'name'
        ],
        number: [
          'id'
        ],
        array: [],
        object: [],
        boolean: []
      }
    }
  }
}
</script>

<style scoped>

</style>
