<template>
  <q-dialog class="responsive-modal" :model-value="true" @hide="cancel" no-backdrop-dismiss>
    <q-card class="responsive-modal-card">
      <q-form
        ref="budgetUpdateEnvelopeForm"
        @submit.prevent="handleSubmit"
        @reset="cancel"
      >
        <q-card-section class="q-pb-none">
          <div class="settings-toolbar-mobile">
            <div>
              <h4 class="settings-toolbar-mobile-title">{{ $t('modules.budget.modal.update_envelope_form.header') }}</h4>
            </div>
          </div>

          <div class="responsive-modal-header">{{ $t('modules.budget.modal.update_envelope_form.header') }}</div>

          <envelope-form :id="id" :name="name" :icon="icon" :currency-id="currencyId"
                         :categories-ids="categories" :elements="elements" :access="access"
                         v-on:update-name="value => name = value"
                         v-on:update-icon="value => icon = value"
                         v-on:update-currency="value => currencyId = value"
                         v-on:update-categories="value => categories = value"
          />
        </q-card-section>
        <q-card-section class="responsive-modal-section-status">
          <div class="responsive-modal-section">
            <q-select class="form-select"
                      outlined
                      v-model="isArchived"
                      use-input
                      :options="archiveOptions"
                      :label="$t('modules.budget.form.budget_envelope.status.label')" />
          </div>
        </q-card-section>
        <q-card-section class="responsive-modal-section-icon">
          <responsive-modal-icons :header="$t('modules.budget.form.budget_envelope.icon.label')" :icon="icon" v-on:update-icon="value => icon = value" />
        </q-card-section>

        <q-card-actions class="responsive-modal-actions">
          <q-btn class="econumo-btn -medium -grey responsive-modal-actions-button" flat
                 :label="$t('elements.button.cancel.label')" v-close-popup @click="cancel" />
          <q-btn class="econumo-btn -medium -magenta responsive-modal-actions-button" flat
                 :label="$t('elements.button.update.label')" type="submit" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Id } from '../../modules/types';
import EnvelopeForm from 'components/Budget/EnvelopeForm.vue';
import ResponsiveModalIcons from 'components/ResponsiveModal/Icons.vue';
import { BudgetElementDto } from 'modules/api/v1/dto/budget.dto';
import { UserAccessDto } from '@shared/dto/access.dto';
import { Icon } from '@shared/types';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  budgetId: Id,
  id: Id,
  name: string,
  icon: Icon,
  currencyId: Id,
  isArchived: number,
  categories: Id[],
  elements: BudgetElementDto[],
  access: UserAccessDto[],
}>();

const emit = defineEmits([
  'save',
  'close'
]);

defineOptions({
  name: 'BudgetUpdateEnvelopeModal'
});

const { t } = useI18n();
const name = ref(props.name);
const icon = ref(props.icon);
const currencyId = ref(props.currencyId);
const categories = ref(props.categories);
const archiveOptions = ref([
  {
    label: t('modules.budget.form.budget_envelope.status.option.active'),
    value: 0
  },
  {
    label: t('modules.budget.form.budget_envelope.status.option.archive'),
    value: 1
  }
]);
const elementStatus = ref(null);
const isArchived = computed({
  get() {
    return elementStatus.value || archiveOptions.value[props.isArchived]
  },
  set(value) {
    elementStatus.value = value;
  }
});

function handleSubmit() {
  emit('save', {
    budgetId: props.budgetId,
    id: props.id,
    name: name.value,
    icon: icon.value,
    currencyId: currencyId.value,
    isArchived: isArchived.value.value,
    categories: categories.value
  });
}

function cancel() {
  emit('close');
}
</script>

