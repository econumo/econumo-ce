<template>
  <q-dialog class="account-access-dialog-modal" :model-value="true" @hide="$emit('cancel')">
    <q-card class="account-access-dialog-modal-card" v-if="user">
      <q-card-section class="account-access-dialog-modal-section -padding">
        <div class="account-access-dialog-modal-user">
          <q-avatar class="account-access-dialog-modal-user-avatar">
            <img :src="avatarUrl(user.avatar, 100)" class="account-access-dialog-modal-user-avatar-img" width="100" height="100"/>
          </q-avatar>
          <div class="account-access-dialog-modal-user-name">
            <div class="account-access-dialog-modal-user-name-title">
              {{ user.name }}
            </div>
          </div>
        </div>
      </q-card-section>
      <q-card-section class="account-access-dialog-modal-section">
        <q-list class="account-access-dialog-modal-list">
          <q-item class="account-access-dialog-modal-item" clickable @click="$emit('share', budgetId, user.id, AccessRole.READ_ONLY)" :class="'control_point ' + (role === 'guest' ? 'active' : '')">
            <q-item-section>{{ $t('modules.connections.elements.roles.guest') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item" clickable @click="$emit('share', budgetId, user.id, AccessRole.USER)" :class="'control_point ' + (role === 'user' ? 'active' : '')">
            <q-item-section>{{ $t('modules.connections.elements.roles.user') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item" clickable @click="$emit('share', budgetId, user.id, AccessRole.ADMIN)" :class="'control_point ' + (role === 'admin' ? 'active' : '')">
            <q-item-section>{{ $t('modules.connections.elements.roles.admin') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item -delete" v-if="role && role !== AccessRole.OWNER" clickable @click="$emit('revoke', budgetId, user.id)">
            <q-item-section>{{ $t('modules.connections.modals.share_access.revoke_access') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item control_point -cancel" clickable @click="$emit('cancel')">
            <q-item-section>{{ $t('elements.button.cancel.label') }}</q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { Id } from '@shared/types';
import { AccessRole } from '@shared/dto/access.dto';
import { UserDto } from '@shared/dto/user.dto';
import { useAvatar } from '../../composables/useAvatar';

defineOptions({
  name: 'BudgetAccessLevelModal'
});

const props = defineProps<{
  budgetId: Id,
  user?: UserDto,
  role?: AccessRole | string
}>();

const emit = defineEmits([
  'share',
  'revoke',
  'cancel'
]);

const { avatarUrl } = useAvatar();

</script>
