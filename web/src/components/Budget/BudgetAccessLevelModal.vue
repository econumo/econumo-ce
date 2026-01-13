<template>
  <q-dialog class="account-access-dialog-modal" :model-value="true" @hide="$emit('cancel')" :no-backdrop-dismiss="$q.screen.gt.md">
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
      <q-card-section class="account-access-dialog-modal-section account-access-dialog-hint-section">
        <div class="account-access-dialog-hint">
          {{ $t('modules.connections.modals.share_access.choose_access_level') }}
        </div>
      </q-card-section>
      <q-card-section class="account-access-dialog-modal-section">
        <q-list class="account-access-dialog-modal-list">
          <q-item class="account-access-dialog-modal-item" clickable @click="$emit('share', budgetId, user.id, AccessRole.READ_ONLY)" :class="'control_point ' + (role === 'guest' ? 'active' : '')">
            <q-item-section>{{ $t('modules.connections.budgets.roles.guest') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item" clickable @click="$emit('share', budgetId, user.id, AccessRole.USER)" :class="'control_point ' + (role === 'user' ? 'active' : '')">
            <q-item-section>{{ $t('modules.connections.budgets.roles.user') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item" clickable @click="$emit('share', budgetId, user.id, AccessRole.ADMIN)" :class="'control_point ' + (role === 'admin' ? 'active' : '')">
            <q-item-section>{{ $t('modules.connections.budgets.roles.admin') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item -delete" v-if="role && role !== AccessRole.OWNER" clickable @click="$emit('revoke', budgetId, user.id)">
            <q-item-section>{{ $t('modules.connections.modals.share_access.revoke_access') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item account-access-dialog-modal-item-cancel" clickable @click="$emit('cancel')">
            <q-item-section>{{ $t('elements.button.cancel.label') }}</q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { useQuasar } from 'quasar';
import { Id } from '@shared/types';
import { AccessRole } from '@shared/dto/access.dto';
import { UserDto } from '@shared/dto/user.dto';
import { useAvatar } from '../../composables/useAvatar';

const $q = useQuasar();

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

<style scoped>
.account-access-dialog-hint-section {
  padding-top: 0;
  padding-bottom: 8px;
}

.account-access-dialog-hint {
  color: #666;
  font-size: 14px;
  padding-left: 16px;
}

.account-access-dialog-modal-item-cancel {
  color: #666;
}
</style>
