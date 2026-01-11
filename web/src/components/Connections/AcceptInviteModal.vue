<template>
  <q-dialog class="settings-connections-accept-modal" :model-value="isOpened" @update:model-value="$emit('update:isOpened', $event)" @hide="onHide" persistent>
    <div class="econumo-modal">
      <q-form ref="inviteForm" @submit.prevent="onSubmit">
        <div class="settings-connections-accept-modal-label">{{ $t('modules.connections.modals.accept_invite.label') }}</div>
        <div class="settings-connections-accept-modal-instruction">{{ $t('modules.connections.modals.accept_invite.instruction') }}</div>
        <div class="settings-connections-accept-modal-control">
          <q-input 
            class="form-input -narrow full-width" 
            outlined 
            v-model="code" 
            lazy-rules
            :label="$t('modules.connections.modals.accept_invite.code.label')"
            :rules="[val => isNotEmpty(val) || $t('modules.connections.forms.invitation_code.validation.required_field')]"
          />
        </div>
        <div class="settings-connections-accept-modal-actions">
          <q-btn class="econumo-btn -medium -grey settings-connections-accept-modal-actions-btn" flat :label="$t('elements.button.cancel.label')" v-close-popup/>
          <q-btn class="econumo-btn -medium -magenta settings-connections-accept-modal-actions-btn" flat :label="$t('elements.button.accept.label')" type="submit"/>
        </div>
      </q-form>
    </div>
  </q-dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { QForm } from 'quasar';
import { useValidation } from '../../composables/useValidation';

defineOptions({
  name: 'AcceptInviteModal'
});

interface Props {
  isOpened: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:isOpened': [value: boolean];
  'hide': [];
  'accept': [code: string];
}>();

const code = ref('');
const inviteForm = ref<InstanceType<typeof QForm> | null>(null);
const { isNotEmpty } = useValidation();

const onHide = () => {
  code.value = '';
  emit('hide');
  emit('update:isOpened', false);
};

const onSubmit = async () => {
  const isValid = await inviteForm.value?.validate();
  if (isValid) {
    emit('accept', code.value);
    onHide();
  }
};
</script> 