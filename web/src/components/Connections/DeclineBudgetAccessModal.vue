<template>
  <q-dialog
    class="decline-shared-access-modal"
    :model-value="isOpened"
    @update:model-value="$emit('update:isOpened', $event)"
    @hide="$emit('cancel')"
    :no-backdrop-dismiss="$q.screen.gt.md"
  >
    <q-card class="decline-shared-access-modal-card">
      <q-card-section class="decline-shared-access-modal-section -padding">
        <div class="decline-shared-access-modal-user">
          <q-avatar class="decline-shared-access-modal-user-avatar">
            <img
              :src="avatarUrl(owner.avatar, 100)"
              class="decline-shared-access-modal-user-avatar-img"
              width="100"
              height="100"
            />
          </q-avatar>
          <div class="decline-shared-access-modal-user-name">
            <div class="decline-shared-access-modal-user-name-title">
              {{ owner.name }}
            </div>
          </div>
        </div>
      </q-card-section>
      <q-card-section
        class="decline-shared-access-modal-section decline-shared-access-hint-section"
      >
        <div class="decline-shared-access-hint">
          {{ budgetName }}
        </div>
      </q-card-section>
      <q-card-section class="decline-shared-access-modal-section">
        <q-list class="decline-shared-access-modal-list">
          <q-item
            class="decline-shared-access-modal-item -delete"
            clickable
            @click="$emit('decline', budgetId)"
          >
            <q-item-section>{{
              $t('modules.connections.modals.decline_access.decline_access')
            }}</q-item-section>
          </q-item>
          <q-item
            class="decline-shared-access-modal-item decline-shared-access-modal-item-cancel"
            clickable
            @click="$emit('cancel')"
          >
            <q-item-section>{{
              $t('elements.button.cancel.label')
            }}</q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { useAvatar } from '../../composables/useAvatar';

defineOptions({
  name: 'DeclineBudgetAccessModal',
});

interface User {
  id: string;
  name: string;
  avatar: string;
}

interface Props {
  isOpened: boolean;
  owner: User;
  budgetId: string;
  budgetName: string;
}

defineProps<Props>();
defineEmits<{
  'update:isOpened': [value: boolean];
  cancel: [];
  decline: [budgetId: string];
}>();

const { avatarUrl } = useAvatar();
</script>

<style scoped>
.decline-shared-access-hint-section {
  padding-top: 0;
  padding-bottom: 8px;
}

.decline-shared-access-hint {
  color: #666;
  font-size: 14px;
  padding-left: 16px;
  font-weight: 500;
}

.decline-shared-access-modal-item-cancel {
  color: #666;
}
</style>
