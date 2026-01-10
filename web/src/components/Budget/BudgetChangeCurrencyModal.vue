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
              <h4 class="settings-toolbar-mobile-title">{{ $t('modules.budget.modal.change_element_currency_form.header') }}</h4>
            </div>
          </div>

          <div class="responsive-modal-header">{{ $t('modules.budget.modal.change_element_currency_form.header') }}</div>
          <div class="budget-form-wrapper">
            <div class="responsive-modal-control">
              <currency-select
                v-model="selectedCurrency"
                :outlined="true"
                custom-class="form-select"
                :label="$t('modules.budget.form.budget_envelope.currency.label')"
                :rules="[val => !!val || $t('modules.budget.form.budget_envelope.currency.validation.required_field')]"
                :option-disable="(item) => !item"
              />
            </div>
          </div>
        </q-card-section>

        <q-card-actions class="responsive-modal-actions">
          <q-btn class="econumo-btn -medium -grey responsive-modal-actions-button" flat :label="$t('elements.button.cancel.label')" v-close-popup @click="cancel"/>
          <q-btn class="econumo-btn -medium -magenta responsive-modal-actions-button" flat :label="$t('elements.button.save.label')" type="submit" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Id } from '../../modules/types';
import { useCurrenciesStore } from 'stores/currencies';
import CurrencySelect from '../CurrencySelect.vue';

interface Props {
  id: Id;
  currencyId: Id;
  budgetId: Id;
}

interface CurrencyOptionDto {
  label: string;
  value: Id;
}

interface EmitEvents {
  (event: 'change-currency', payload: { budgetId: Id; elementId: Id; currencyId: Id }): void;
  (event: 'close'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<EmitEvents>();

defineOptions({
  name: 'BudgetChangeCurrencyModal'
});

const currenciesStore = useCurrenciesStore();
const selectedCurrency = ref<CurrencyOptionDto | null>(
  props.currencyId ? {
    value: currenciesStore.currenciesHash[props.currencyId].id,
    label: currenciesStore.currenciesHash[props.currencyId].code
  } : null
);

function handleSubmit(): void {
  if (!selectedCurrency.value) return;
  
  emit('change-currency', {
    budgetId: props.budgetId,
    elementId: props.id,
    currencyId: selectedCurrency.value.value,
  });
}

function cancel(): void {
  emit('close');
}
</script>
