<template>
  <q-dialog class="responsive-modal" :model-value="true" @hide="cancel" no-backdrop-dismiss>
    <q-card class="responsive-modal-card">
      <q-form
        ref="budgetForm"
        @submit.prevent="handleSubmit"
        @reset="cancel"
      >
        <q-card-section>
          <div class="settings-toolbar-mobile">
            <div>
              <h4 class="settings-toolbar-mobile-title">{{ $t('modules.budget.modal.create_budget_form.header') }}</h4>
            </div>
          </div>

          <div class="responsive-modal-header">{{ $t('modules.budget.modal.create_budget_form.header') }}</div>
          <budget-form 
            :name="name" 
            :excluded="accountsExcluded"
            :can-update-name="true"
            :start-date="startDate"
            :currency-id="currencyId"
            @update-name="updateName"
            @update-excluded="updateExcluded"
            @update-start-date="updateStartDate"
            @update-currency="updateCurrency"
          />
        </q-card-section>

        <q-card-actions class="responsive-modal-actions">
          <q-btn 
            class="econumo-btn -medium -grey responsive-modal-actions-button" 
            flat 
            :label="$t('elements.button.cancel.label')" 
            v-close-popup 
            @click="cancel"
          />
          <q-btn 
            class="econumo-btn -medium -magenta responsive-modal-actions-button" 
            flat 
            :label="$t('elements.button.create.label')" 
            type="submit" 
          />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Id } from '../../modules/types';
import BudgetForm from './BudgetForm.vue';

const props = defineProps<{
  id: Id | null,
  name: string,
  excluded: Id[],
  currencyId: Id
}>();

const emit = defineEmits<{
  'update-name': [value: string],
  'update-excluded': [value: Id[]],
  'submit': [value: { id: Id | null, name: string, excludedAccounts: Id[], startDate: string, currencyId: string }],
  'close': []
}>();

defineOptions({
  name: 'BudgetCreateFormModal'
});

const name = ref(props.name);
const accountsExcluded = ref(props.excluded);
const startDate = ref('');
const currencyId = ref(props.currencyId);

const updateName = (value: string) => {
  name.value = value;
  emit('update-name', value);
};

const updateExcluded = (value: Id[]) => {
  accountsExcluded.value = value;
  emit('update-excluded', value);
};

const updateStartDate = (value: string) => {
  startDate.value = value;
};

const updateCurrency = (value: Id) => {
  currencyId.value = value;
};

function handleSubmit() {
  emit('submit', {
    id: props.id,
    name: name.value,
    excludedAccounts: accountsExcluded.value,
    startDate: startDate.value,
    currencyId: currencyId.value
  });
}

function cancel() {
  emit('close');
}
</script>

