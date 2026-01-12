<template>
  <q-dialog :model-value="true" class="prompt-modal" no-backdrop-dismiss @hide="onHide">
    <q-card class="prompt-modal-card econumo-modal">
      <q-form
        ref="folderForm"
        @submit="submit"
        @reset="cancel"
        class="prompt-modal-card-form"
      >
        <q-card-section class="prompt-modal-card-section">
          <div class="prompt-modal-card-section-label">{{ headerLabel }}</div>
          <div class="prompt-modal-card-section-input">
            <q-input class="form-input -narrow full-width" autofocus outlined v-model="value" :label="inputLabel" :rules="validation ?? []"/>
          </div>
        </q-card-section>

        <q-card-actions class="prompt-modal-card-actions">
          <q-btn flat class="econumo-btn -medium -grey prompt-modal-card-actions-button" :label="cancelLabel" @click="cancel" v-close-popup />
          <q-btn flat class="econumo-btn -medium -magenta prompt-modal-card-actions-button" :label="submitLabel" type="submit"/>
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script>
import {defineComponent} from 'vue'

export default defineComponent({
  props: ['headerLabel', 'inputValue', 'inputLabel', 'cancelLabel', 'submitLabel', 'validation', 'id'],
  data() {
    return {
      inputValueCopy: null
    }
  },
  computed: {
    value: {
      get() {
        return this.inputValueCopy ?? this.inputValue
      },
      set(value) {
        this.inputValueCopy = value;
      }
    },
  },
  methods: {
    submit: function() {
      this.$emit('submit', this.value, this.inputValue, this.id);
    },
    cancel: function() {
      if (this.$refs.folderForm) {
        this.$refs.folderForm.resetValidation();
      }
      this.$emit('close')
    },
    onHide: function() {
      this.$emit('close')
    }
  }
})
</script>
