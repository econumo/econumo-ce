<template>
  <q-dialog class="settings-connections-modal" :model-value="isOpened" @update:model-value="$emit('update:isOpened', $event)" @hide="onHide" :position="$q.screen.gt.md ? 'standard' : 'bottom'" no-backdrop-dismiss>
    <q-card class="settings-connections-modal-card" v-if="connection">
        <q-card-section class="settings-connections-modal-section">
        <div class="settings-connections-modal-card-container">
          <div class="settings-connections-modal-card-label">{{ $t('modules.connections.modals.preview_connection.budgets') }}</div>
          <div class="settings-connections-modal-card-user">
            <q-avatar class="settings-connections-modal-card-user-avatar">
              <img class="settings-connections-modal-card-user-avatar-img" :src="connection.user.avatar">
            </q-avatar>
            <div class="settings-connections-modal-card-user-name">
              {{ connection.user.name }}
            </div>
          </div>
        </div>

        <div class="settings-connections-modal-card-item" v-if="connection.sharedBudgets.length === 0">
            <div class="settings-connections-modal-card-item-label">{{ $t('modules.connections.modals.preview_connection.budgets_empty') }}</div>  
            <q-list class="settings-connections-modal-card-item-list">
                <q-item v-ripple class="settings-connections-modal-card-item-item">
                    <q-item-section class="settings-connections-modal-card-item-item-name-block">
                        {{ $t('modules.connections.modals.preview_connection.budgets_empty') }}
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
        <div class="settings-connections-modal-card-item" v-else>
          <div class="settings-connections-modal-card-item-label">{{ $t('modules.connections.modals.preview_connection.budgets') }}</div>
          <q-list class="settings-connections-modal-card-item-list">
            <q-item v-for="budget in connection.sharedBudgets" v-bind:key="budget.id" clickable v-ripple class="settings-connections-modal-card-item-item">
              <q-item-section class="settings-connections-modal-card-item-item-avatar" avatar>
                <q-icon name="menu_book" />
              </q-item-section>
              <q-item-section class="settings-connections-modal-card-item-item-name-block">
                {{ budget.name }}
                <span class="settings-connections-modal-card-item-item-role">{{ $t('modules.connections.elements.roles.' + budget.role) }}</span>
              </q-item-section>

              <q-item-section side class="settings-connections-modal-card-item-item-shared">
                <q-avatar class="settings-connections-modal-card-item-item-shared-avatar">
                    <img class="settings-connections-modal-card-user-avatar-img" :src="budget.user.avatar" :title="budget.user.name">
                </q-avatar>
              </q-item-section>
            </q-item>
          </q-list>
        </div>
      </q-card-section>

      <q-card-section class="settings-connections-modal-section">
        <div class="settings-connections-modal-card-container">
          <div class="settings-connections-modal-card-label">{{ $t('modules.connections.modals.preview_connection.accounts') }}</div>
        </div>

        <div class="settings-connections-modal-card-item settings-connections-modal-card-item-last" v-if="connection.sharedAccounts.length === 0">
            <div class="settings-connections-modal-card-item-label">{{ $t('modules.connections.modals.preview_connection.accounts_empty') }}</div>  
            <q-list class="settings-connections-modal-card-item-list">
                <q-item v-ripple class="settings-connections-modal-card-item-item">
                    <q-item-section class="settings-connections-modal-card-item-item-name-block">
                        {{ $t('modules.connections.modals.preview_connection.accounts_empty') }}
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
        <div class="settings-connections-modal-card-item  settings-connections-modal-card-item-last" v-else>
          <div class="settings-connections-modal-card-item-label">{{ $t('modules.connections.modals.preview_connection.accounts') }}</div>
          <q-list class="settings-connections-modal-card-item-list">
            <q-item v-for="account in connection.sharedAccounts" v-bind:key="account.id" clickable v-ripple class="settings-connections-modal-card-item-item">
              <q-item-section class="settings-connections-modal-card-item-item-avatar" avatar>
                <q-icon :name="account.icon" />
              </q-item-section>
              <q-item-section class="settings-connections-modal-card-item-item-name-block">
                {{ account.name }}
                <span class="settings-connections-modal-card-item-item-role">{{ $t('modules.connections.elements.roles.' + account.role) }}</span>
              </q-item-section>

              <q-item-section side class="settings-connections-modal-card-item-item-shared">
                <q-avatar class="settings-connections-modal-card-item-item-shared-avatar">
                  <img :src="avatarUrl(account.user.avatar, 30)" :title="account.user.name" width="30" height="30" />
                </q-avatar>
              </q-item-section>
            </q-item>
          </q-list>
        </div>
      </q-card-section>
      <q-card-actions class="responsive-modal-actions">
        <q-btn
          class="econumo-btn -medium -grey responsive-modal-actions-button"
          flat
          :label="$t('elements.button.delete.label')"
          icon="delete"
          @click="onDelete"
          v-if="allowChanges" />

        <q-btn
          class="econumo-btn -medium -grey responsive-modal-actions-button"
          :label="$t('elements.button.cancel.label')"
          flat
          v-close-popup />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { useQuasar } from 'quasar';
import { type SharedBudget, type SharedAccount } from '../../stores/connections';
import _ from 'lodash';
import { useAvatar } from '../../composables/useAvatar';

defineOptions({
  name: 'PreviewConnectionModal'
});

interface User {
  id: string;
  name: string;
  avatar: string;
}

interface Connection {
  user: User;
  sharedAccounts: SharedAccount[];
  sharedBudgets: SharedBudget[];
}

interface Props {
  isOpened: boolean;
  connection: Connection;
  allowChanges: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:isOpened': [value: boolean];
  'hide': [];
  'delete': [userId: string];
}>();

const $q = useQuasar();

const { avatarUrl } = useAvatar();

const onHide = () => {
  emit('hide');
  emit('update:isOpened', false);
};

const onDelete = () => {
  emit('delete', props.connection.user.id);
};
</script> 