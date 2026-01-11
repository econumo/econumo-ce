<template>
  <q-dialog class="responsive-modal" :model-value="true" @hide="cancel" no-backdrop-dismiss>
    <q-card class="responsive-modal-card">
      <q-form
        ref="budgetCreateEnvelopeForm"
        @submit.prevent="handleSubmit"
        @reset="cancel"
      >
        <q-card-section>
          <div class="settings-toolbar-mobile">
            <div>
              <h4 class="settings-toolbar-mobile-title">{{ $t('modules.budget.modal.create_envelope_form.header') }}</h4>
            </div>
          </div>

          <div class="responsive-modal-header">{{ $t('modules.budget.modal.create_envelope_form.header') }}</div>

          <envelope-form :id="id" :name="name" :icon="icon" :currency-id="currencyId"
                         :categories-ids="categoriesIncluded" :elements="elements" :access="access"
                         v-on:update-name="value => name = value"
                         v-on:update-icon="value => icon = value"
                         v-on:update-currency="value => currencyId = value"
                         v-on:update-categories="value => categoriesIncluded = value"
          />
        </q-card-section>
        <q-card-section class="responsive-modal-section-icon">
          <responsive-modal-icons :header="$t('modules.budget.form.budget_envelope.icon.label')" :icon="icon" v-on:update-icon="value => icon = value" />
        </q-card-section>

        <q-card-actions class="responsive-modal-actions">
          <q-btn class="econumo-btn -medium -grey responsive-modal-actions-button" flat
                 :label="$t('elements.button.cancel.label')" v-close-popup @click="cancel" />
          <q-btn class="econumo-btn -medium -magenta responsive-modal-actions-button" flat
                 :label="$t('elements.button.add.label')" type="submit" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Id } from '../../modules/types';
import EnvelopeForm from 'components/Budget/EnvelopeForm.vue';
import ResponsiveModalIcons from 'components/ResponsiveModal/Icons.vue';
import { BudgetElementDto } from 'modules/api/v1/dto/budget.dto';
import { UserAccessDto } from '@shared/dto/access.dto';
import { defaultEnvelopeIcon } from '../../modules/icons';

const props = defineProps<{
  budgetId: Id | null,
  id: Id | null,
  budgetCurrencyId: Id,
  folderId: Id | null,
  elements: BudgetElementDto[],
  access: UserAccessDto[],
}>();

const emit = defineEmits([
  'create',
  'close'
]);

defineOptions({
  name: 'BudgetCreateEnvelopeModal'
});

const name = ref('');
const icon = ref(defaultEnvelopeIcon);
const currencyId = ref(props.budgetCurrencyId);
const categoriesIncluded = ref([]);

function handleSubmit() {
  emit('create', {
    budgetId: props.budgetId,
    id: props.id,
    icon: icon.value,
    name: name.value,
    currencyId: currencyId.value,
    folderId: props.folderId,
    categories: categoriesIncluded.value
  });
}

function cancel() {
  emit('close');
}
</script>

