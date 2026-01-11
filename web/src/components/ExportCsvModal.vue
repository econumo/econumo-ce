<template>
  <q-dialog class="responsive-modal" :model-value="true" @hide="cancel" no-backdrop-dismiss>
    <q-card class="responsive-modal-card">
      <q-form
        ref="exportForm"
        @submit.prevent="handleSubmit"
        @reset="cancel"
      >
        <q-card-section>
          <div class="settings-toolbar-mobile">
            <div>
              <h4 class="settings-toolbar-mobile-title">{{ $t('modules.export_csv.modal.export_csv_form.header') }}</h4>
            </div>
          </div>

          <div class="responsive-modal-header">{{ $t('modules.export_csv.modal.export_csv_form.header') }}</div>
          <export-csv-form
            :selected-ids="selectedAccountIds"
            v-on:update-selected="value => selectedAccountIds = value"
          />
        </q-card-section>

        <q-card-actions class="responsive-modal-actions">
          <q-btn class="econumo-btn -medium -grey responsive-modal-actions-button" flat :label="$t('elements.button.cancel.label')" v-close-popup @click="cancel"/>
          <q-btn class="econumo-btn -medium -magenta responsive-modal-actions-button" flat :label="$t('elements.button.export.label')" type="submit" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Id } from '../modules/types';
import ExportCsvForm from './ExportCsvForm.vue';

const props = defineProps<{
  selectedIds: Id[]
}>();

const emit = defineEmits([
  'update-selected',
  'submit',
  'close'
]);

defineOptions({
  name: 'ExportCsvModal'
});

const selectedAccountIds = computed({
  get() {
    return props.selectedIds
  },
  set(value) {
    emit('update-selected', value);
  }
});

function handleSubmit() {
  emit('submit', {
    accountIds: selectedAccountIds.value
  });
}

function cancel() {
  emit('close');
}
</script>

<style scoped lang="scss">
.responsive-modal-header {
  margin-bottom: 8px !important;
}

:deep(.budget-form-wrapper) {
  margin-top: 0 !important;
}

:deep(.budget-form-wrapper-accounts-header-container) {
  margin-top: 0 !important;
}

:deep(.budget-form-wrapper-accounts-header) {
  margin-top: 0 !important;
}
</style>
