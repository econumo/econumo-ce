<template>
  <q-dialog class="settings-connections-generate-modal" :model-value="isOpened" @update:model-value="$emit('update:isOpened', $event)" @hide="onHide">
    <div class="econumo-modal">
      <div>
        <div class="settings-connections-generate-modal-label">{{ $t('modules.connections.modals.generate_invite.code.label', {code}) }}</div>
        <div class="settings-connections-generate-modal-instruction">{{ $t('modules.connections.modals.generate_invite.instruction') }}</div>
        <div class="settings-connections-generate-modal-code">{{ code }}</div>
      </div>
      <div class="settings-connections-generate-modal-action">
        <q-btn class="econumo-btn -small -transperent-magenta settings-connections-generate-modal-action-btn" flat :label="$t('elements.button.ok.label')" v-close-popup />
      </div>
    </div>
  </q-dialog>
</template>

<script setup lang="ts">
defineOptions({
  name: 'GenerateInviteModal'
});

interface Props {
  isOpened: boolean;
  code: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:isOpened': [value: boolean];
  'hide': [];
}>();

const onHide = () => {
  emit('hide');
  emit('update:isOpened', false);
};
</script> 