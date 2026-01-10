<template>
  <q-dialog class="responsive-modal" :model-value="true" @hide="cancel">
    <q-card class="responsive-modal-card">
      <q-form
        ref="budgetForm"
        @submit.prevent="handleSubmit"
        @reset="cancel"
      >
        <q-card-section>
          <div class="settings-toolbar-mobile">
            <div>
              <h4 class="settings-toolbar-mobile-title">{{ $t('modules.budget.modal.update_budget_form.header') }}</h4>
            </div>
          </div>

          <div class="responsive-modal-header">{{ $t('modules.budget.modal.update_budget_form.header') }}</div>
          <budget-form :name="name"
                       :excluded="accountsExcluded"
                       :can-update-name="canUpdateName"
                       :currency-id="currencyId"
                       v-on:update-name="value => name = value"
                       v-on:update-excluded="value => accountsExcluded = value"
                       v-on:update-currency="value => currencyId = value"></budget-form>
        </q-card-section>

        <q-card-actions class="responsive-modal-actions">
          <q-btn class="econumo-btn -medium -grey responsive-modal-actions-button" flat :label="$t('elements.button.cancel.label')" v-close-popup @click="cancel"/>
          <q-btn class="econumo-btn -medium -magenta responsive-modal-actions-button" flat :label="$t('elements.button.update.label')" type="submit" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Id } from '../../modules/types';
import BudgetForm from './BudgetForm.vue';

const props = defineProps<{
  id: Id | null,
  name: string,
  excluded: Id[],
  canUpdateName: boolean,
  currencyId: Id
}>();

const emit = defineEmits([
  'update-name',
  'update-excluded',
  'update-currency',
  'submit',
  'close'
]);

defineOptions({
  name: 'BudgetUpdateFormModal'
});

const name = computed({
  get() {
    return props.name
  },
  set(value) {
    emit('update-name', value);
  }
});
const accountsExcluded = computed({
  get() {
    return props.excluded
  },
  set(value) {
    emit('update-excluded', value);
  }
});

const currencyId = computed({
  get() {
    return props.currencyId
  },
  set(value) {
    emit('update-currency', value);
  }
});

function handleSubmit() {
  emit('submit', {
    id: props.id,
    name: name.value,
    excludedAccounts: accountsExcluded.value,
    currencyId: currencyId.value
  });
}

function cancel() {
  emit('close');
}
</script>

