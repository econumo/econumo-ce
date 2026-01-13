<template>
  <q-dialog class="account-access-dialog-modal" :model-value="true" @hide="$emit('cancel')" :no-backdrop-dismiss="$q.screen.gt.md">
    <q-card class="account-access-dialog-modal-card">
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
          <q-item class="account-access-dialog-modal-item" clickable @click="$emit('allow', user.id, itemId, 'guest')" :class="'control_point ' + (role === 'guest' ? 'active' : '')">
            <q-item-section>{{ $t('modules.connections.accounts.roles.guest') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item" clickable @click="$emit('allow', user.id, itemId, 'user')" :class="'control_point ' + (role === 'user' ? 'active' : '')">
            <q-item-section>{{ $t('modules.connections.accounts.roles.user') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item" clickable @click="$emit('allow', user.id, itemId, 'admin')" :class="'control_point ' + (role === 'admin' ? 'active' : '')">
            <q-item-section>{{ $t('modules.connections.accounts.roles.admin') }}</q-item-section>
          </q-item>
          <q-item class="account-access-dialog-modal-item -delete" v-if="role" clickable @click="$emit('revoke', user.id, itemId)">
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

<script>
import { defineComponent } from 'vue'
import { useQuasar } from 'quasar';
import { useAvatar } from '../composables/useAvatar';

export default defineComponent({
  props: ['user', 'itemId', 'role'],
  setup() {
    const $q = useQuasar();
    const { avatarUrl } = useAvatar();
    return { $q, avatarUrl };
  }
})
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
