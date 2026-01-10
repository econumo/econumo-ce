<template>
  <q-dialog class="prompt-modal" :model-value="true" @hide="cancel">
    <q-card class="prompt-modal-card econumo-modal">
      <q-form
        ref="budgetForm"
        @submit.prevent="handleSubmit"
        @reset="cancel"
        class="prompt-modal-card-form"
      >
      <q-card-section class="prompt-modal-card-section">
          <div class="prompt-modal-card-section-label">{{ $t('modules.budget.modal.set_limit_form.header') }}</div>
          <div class="prompt-modal-card-section-input">
            <calculator-input class="form-input -narrow full-width" 
              autofocus 
              outlined 
              v-model="amount" 
              :label="$t('modules.budget.form.budget_limit.limit.label')"
              :rules="[
                (val: string) => isValidNumber(val) || $t('elements.validation.invalid_number'),
                (val: string) => isValidDecimalNumber(val) || $t('elements.validation.invalid_decimal_number')
              ]" />
          </div>
        </q-card-section>

        <q-card-actions class="prompt-modal-card-actions">
          <q-btn flat class="econumo-btn -medium -grey prompt-modal-card-actions-button" :label="$t('elements.button.cancel.label')" @click="cancel" v-close-popup />
          <q-btn flat class="econumo-btn -medium -magenta prompt-modal-card-actions-button" :label="$t('elements.button.save.label')" type="submit"/>
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Id, DateString } from '../../modules/types';
import { useValidation } from '../../composables/useValidation';
import CalculatorInput from '../Calculator/CalculatorInput.vue';

const props = defineProps<{
  id: Id,
  budgetId: Id,
  elementId: Id,
  period: DateString,
  amount: number | null
}>();

const emit = defineEmits([
  'set-limit',
  'close'
]);

defineOptions({
  name: 'BudgetSetLimitModal'
});

const { isValidNumber, isValidDecimalNumber } = useValidation();
const amount = ref<string | number | undefined>(props.amount?.toString());

function handleSubmit() {
  let amountValue = Number(amount.value ?? 0);
  emit('set-limit', {
    budgetId: props.budgetId,
    elementId: props.elementId,
    amount: amountValue,
    period: props.period
  });
}


function cancel() {
  emit('close');
}
</script>
