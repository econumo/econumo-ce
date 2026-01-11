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
          <div class="settings-connections-modal-card-item-hint">{{ $t('modules.connections.modals.preview_connection.tap_to_manage') }}</div>
          <q-list class="settings-connections-modal-card-item-list">
            <q-item v-for="budget in connection.sharedBudgets" v-bind:key="budget.id" clickable v-ripple class="settings-connections-modal-card-item-item" @click="onBudgetClick(budget)">
              <q-item-section class="settings-connections-modal-card-item-item-avatar" avatar>
                <q-icon name="menu_book" />
              </q-item-section>
              <q-item-section class="settings-connections-modal-card-item-item-name-block">
                <div>
                  {{ budget.name }}
                  <span v-if="isBudgetOwnedByCurrentUser(budget)" class="settings-connections-modal-card-item-item-owner-hint">{{ $t('modules.connections.modals.preview_connection.your_budget') }}</span>
                  <span v-if="!isBudgetOwnedByCurrentUser(budget)" class="settings-connections-modal-card-item-item-shared-hint">{{ $t('modules.connections.modals.preview_connection.shared_with_you') }}</span>
                </div>
                <span class="settings-connections-modal-card-item-item-role">{{ $t('modules.connections.budgets.roles.' + budget.role) }}</span>
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
          <div class="settings-connections-modal-card-item-hint">{{ $t('modules.connections.modals.preview_connection.tap_to_manage') }}</div>
          <q-list class="settings-connections-modal-card-item-list">
            <q-item v-for="account in connection.sharedAccounts" v-bind:key="account.id" clickable v-ripple class="settings-connections-modal-card-item-item" @click="onAccountClick(account)">
              <q-item-section class="settings-connections-modal-card-item-item-avatar" avatar>
                <q-icon :name="account.icon" />
              </q-item-section>
              <q-item-section class="settings-connections-modal-card-item-item-name-block">
                <div>
                  {{ account.name }}
                  <span v-if="isAccountOwnedByCurrentUser(account)" class="settings-connections-modal-card-item-item-owner-hint">{{ $t('modules.connections.modals.preview_connection.your_account') }}</span>
                  <span v-if="!isAccountOwnedByCurrentUser(account)" class="settings-connections-modal-card-item-item-shared-hint">{{ $t('modules.connections.modals.preview_connection.shared_with_you') }}</span>
                </div>
                <span class="settings-connections-modal-card-item-item-role">{{ $t('modules.connections.accounts.roles.' + account.role) }}</span>
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

  <DeclineSharedAccessModal
    v-if="selectedAccount && selectedAccountOwner && !isAccountOwnedByCurrentUser(selectedAccount)"
    :is-opened="isDeclineModalOpened"
    @update:is-opened="isDeclineModalOpened = $event"
    :owner="selectedAccountOwner"
    :account-id="selectedAccount.id"
    :account-name="selectedAccount.name"
    @decline="onDeclineAccess"
    @cancel="onCancelDecline"
  />

  <AccessLevelDialogModal
    v-if="selectedAccount && isAccountOwnedByCurrentUser(selectedAccount)"
    :user="connection.user"
    :item-id="selectedAccount.id"
    :role="selectedAccount.role"
    @allow="onAllowAccess"
    @revoke="onRevokeAccess"
    @cancel="onCancelAccessLevel"
  />

  <BudgetAccessLevelModal
    v-if="selectedBudget && isBudgetOwnedByCurrentUser(selectedBudget)"
    :budget-id="selectedBudget.id"
    :user="connection.user"
    :role="selectedBudget.role"
    @share="onShareBudgetAccess"
    @revoke="onRevokeBudgetAccess"
    @cancel="onCancelBudgetAccessLevel"
  />
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useQuasar } from 'quasar';
import type { SharedBudget, SharedAccount } from '../../stores/connections';
import { useAvatar } from '../../composables/useAvatar';
import { useUsersStore } from '../../stores/users';
import { useAccountsStore } from '../../stores/accounts';
import { useBudgetsStore } from '../../stores/budgets';
import DeclineSharedAccessModal from './DeclineSharedAccessModal.vue';
import AccessLevelDialogModal from '../AccessLevelDialogModal.vue';
import BudgetAccessLevelModal from '../Budget/BudgetAccessLevelModal.vue';

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
  'decline-account': [accountId: string];
  'allow-account': [userId: string, accountId: string, role: string];
  'revoke-account': [userId: string, accountId: string];
  'share-budget': [userId: string, budgetId: string, role: string];
  'revoke-budget': [userId: string, budgetId: string];
}>();

const $q = useQuasar();
const usersStore = useUsersStore();
const accountsStore = useAccountsStore();
const budgetsStore = useBudgetsStore();

const { avatarUrl } = useAvatar();

const isDeclineModalOpened = ref(false);
const isAccessLevelModalOpened = ref(false);
const selectedAccount = ref<SharedAccount | null>(null);
const selectedAccountOwner = ref<User | null>(null);
const selectedBudget = ref<SharedBudget | null>(null);

const currentUserId = computed(() => usersStore.userId);

const isAccountOwnedByCurrentUser = (account: SharedAccount) => {
  // Look up the full account to check ownership
  const fullAccount = accountsStore.accounts.find(a => a.id === account.id);
  if (!fullAccount) {
    return false;
  }
  return fullAccount.owner.id === currentUserId.value;
};

const getAccountOwner = (account: SharedAccount): User => {
  const fullAccount = accountsStore.accounts.find(a => a.id === account.id);
  if (!fullAccount) {
    return props.connection.user; // Fallback
  }
  return {
    id: fullAccount.owner.id,
    name: fullAccount.owner.name,
    avatar: fullAccount.owner.avatar
  };
};

const isBudgetOwnedByCurrentUser = (budget: SharedBudget) => {
  // Look up the full budget to check ownership
  const fullBudget = budgetsStore.budgets.find(b => b.id === budget.id);
  if (!fullBudget) {
    return false;
  }
  return fullBudget.ownerUserId === currentUserId.value;
};

const onHide = () => {
  emit('hide');
  emit('update:isOpened', false);
};

const onDelete = () => {
  emit('delete', props.connection.user.id);
};

const onAccountClick = (account: SharedAccount) => {
  selectedAccount.value = account;
  selectedAccountOwner.value = getAccountOwner(account);

  if (isAccountOwnedByCurrentUser(account)) {
    // Account I own and share with the connected user - show access level dialog
    isAccessLevelModalOpened.value = true;
  } else {
    // Account shared with me - show decline dialog
    isDeclineModalOpened.value = true;
  }
};

const onDeclineAccess = (accountId: string) => {
  isDeclineModalOpened.value = false;
  selectedAccount.value = null;
  selectedAccountOwner.value = null;
  emit('decline-account', accountId);
};

const onCancelDecline = () => {
  isDeclineModalOpened.value = false;
  selectedAccount.value = null;
  selectedAccountOwner.value = null;
};

const onAllowAccess = (userId: string, accountId: string, role: string) => {
  isAccessLevelModalOpened.value = false;
  selectedAccount.value = null;
  selectedAccountOwner.value = null;
  emit('allow-account', userId, accountId, role);
};

const onRevokeAccess = (userId: string, accountId: string) => {
  isAccessLevelModalOpened.value = false;
  selectedAccount.value = null;
  selectedAccountOwner.value = null;
  emit('revoke-account', userId, accountId);
};

const onCancelAccessLevel = () => {
  isAccessLevelModalOpened.value = false;
  selectedAccount.value = null;
  selectedAccountOwner.value = null;
};

const onBudgetClick = (budget: SharedBudget) => {
  selectedBudget.value = budget;

  if (isBudgetOwnedByCurrentUser(budget)) {
    // Budget I own and share with the connected user - show access level dialog
    // The modal will appear via the v-if condition in the template
  } else {
    // Budget shared with me - for now, do nothing
    // Could add a decline dialog here in the future if needed
  }
};

const onShareBudgetAccess = (budgetId: string, userId: string, role: string) => {
  selectedBudget.value = null;
  emit('share-budget', userId, budgetId, role);
};

const onRevokeBudgetAccess = (budgetId: string, userId: string) => {
  selectedBudget.value = null;
  emit('revoke-budget', userId, budgetId);
};

const onCancelBudgetAccessLevel = () => {
  selectedBudget.value = null;
};
</script>
