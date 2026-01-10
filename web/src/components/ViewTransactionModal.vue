<template>
  <q-dialog class="account-preview-modal" :model-value="true" :position="position" @hide="cancel">
    <q-card class="account-preview-modal-card">
      <q-card-section class="account-preview-modal-section">
        <div class="account-preview-modal-head">
          <div class="account-preview-modal-head-title">{{ $t('pages.account.preview_transaction_modal.header') }}</div>
          <div class="account-preview-modal-head-badge">
            <q-badge
              class="account-preview-modal-head-badge-label"
              :class="(isTransfer(transaction) ? '-transfer' : '')"
            >
              {{ $t('pages.account.preview_transaction_modal.type.' + transaction.type) }}
            </q-badge>
          </div>
        </div>

        <div class="account-preview-modal-account">
          <div class="account-preview-modal-account-title" v-if="isTransfer(transaction)">{{ $t('pages.account.preview_transaction_modal.sender.label') }}</div>
          <q-item class="account-preview-modal-account-item">
            <q-item-section class="account-preview-modal-account-item-avatar" avatar>
              <q-icon class="account-preview-modal-account-item-avatar-icon" :name="account.icon" />
            </q-item-section>
            <div class="account-preview-modal-account-item-box">
              <q-item-section class="account-preview-modal-account-item-box-name">{{ account.name }}</q-item-section>
              <q-item-section class="account-preview-modal-account-item-box-balance" side>{{ moneyFormat(transaction.amount, account.currency.id, true, false) }}</q-item-section>
            </div>
          </q-item>
        </div>
      </q-card-section>

      <q-card-section class="account-preview-modal-section -info">
        <div v-if="accountRecipient" class="account-preview-modal-account -recipient">
          <div class="account-preview-modal-account-title" v-if="isTransfer(transaction)">{{ $t('pages.account.preview_transaction_modal.recipient.label') }}</div>
          <q-item class="account-preview-modal-account-item">
            <q-item-section class="account-preview-modal-account-item-avatar" avatar>
              <q-icon class="account-preview-modal-account-item-avatar-icon" :name="accountRecipient.icon" />
            </q-item-section>
            <div class="account-preview-modal-account-item-box">
              <q-item-section class="account-preview-modal-account-item-box-name">{{ accountRecipient.name }}</q-item-section>
              <q-item-section class="account-preview-modal-account-item-box-balance" side v-if="transaction.amountRecipient">{{ moneyFormat(transaction.amountRecipient, accountRecipient.currency.id, true, false) }}</q-item-section>
            </div>
          </q-item>
        </div>

        <div class="account-preview-modal-account-info">
          <div class="account-preview-modal-account-info-item" v-if="transaction.category">
            <div class="account-preview-modal-account-info-item-label">{{ $t('pages.account.preview_transaction_modal.category.label') }}</div>
            <div class="account-preview-modal-account-info-item-content">{{ transaction.category.name }}</div>
          </div>
          <div class="account-preview-modal-account-info-item" v-if="transaction.description">
            <div class="account-preview-modal-account-info-item-label">{{ $t('pages.account.preview_transaction_modal.description.label') }}</div>
            <div class="account-preview-modal-account-info-item-content">{{ transaction.description || "–" }}</div>
          </div>
          <div v-if="!isTransfer(transaction) && transaction.payee">
            <div class="account-preview-modal-account-info-item">
              <div class="account-preview-modal-account-info-item-label">{{ $t('pages.account.preview_transaction_modal.payee.label') }}</div>
              <div class="account-preview-modal-account-info-item-content">{{ transaction.payee?.name || "–" }}</div>
            </div>
          </div>
          <div v-if="!isTransfer(transaction) && transaction.tag">
            <div class="account-preview-modal-account-info-item">
              <div class="account-preview-modal-account-info-item-label">{{ $t('pages.account.preview_transaction_modal.tags.label') }}</div>
              <div class="account-preview-modal-account-info-item-content -tags">{{ transaction.tag?.name || "–" }}</div>
            </div>
          </div>
          <div class="account-preview-modal-account-info-item" v-if="account.sharedAccess.length > 0 || (isTransfer(transaction) && accountRecipient && accountRecipient.sharedAccess.length > 0)">
            <div class="account-preview-modal-account-info-item-label">{{ $t('pages.account.preview_transaction_modal.author.label') }}</div>
            <div class="account-preview-modal-account-info-item-content">
              <q-chip>
                <q-avatar>
                  <img :src="avatarUrl(transaction.author.avatar, 20)" width="20" height="20">
                </q-avatar>
                {{ transaction.author.name }}
              </q-chip>
            </div>
          </div>
          <div class="account-preview-modal-account-info-item">
            <div class="account-preview-modal-account-info-item-label">{{ $t('pages.account.preview_transaction_modal.created_at.label') }}</div>
            <div class="account-preview-modal-account-info-item-content">{{ transaction.date || "–" }}</div>
          </div>
        </div>
      </q-card-section>

      <q-card-actions class="preview-modal-account-info-access-actions">
        <q-btn class="econumo-btn -large -grey preview-modal-account-info-access-actions-btn" :title="$t('elements.button.delete.label')" flat icon="delete" @click="openDeleteModal(transaction.id)" :disable="!canChangeTransaction || (transaction.type === 'transfer' && (!transaction.account || !transaction.accountRecipient))" />
        <q-btn class="econumo-btn -large -grey preview-modal-account-info-access-actions-btn -wide" :label="$t('elements.button.edit.label')" flat @click="openUpdateModal(transaction.id)" :disable="!canChangeTransaction || (transaction.type === 'transfer' && (!transaction.account || !transaction.accountRecipient))" />
        <q-btn class="econumo-btn -large -grey preview-modal-account-info-access-actions-btn" :title="$t('elements.button.cancel.label')" flat icon="expand_more" v-close-popup @click="cancel" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { AccountDto } from '@shared/dto/account.dto';
import { useAvatar } from '../composables/useAvatar';
import { useMoney } from '../composables/useMoney';
import { Id } from '../modules/types';
import { TransactionDto } from '@shared/dto/transaction.dto';
import { CategoryDto } from '@shared/dto/category.dto';
import { PayeeDto } from '@shared/dto/payee.dto';
import { TagDto } from '@shared/dto/tag.dto';

interface Transaction extends TransactionDto {
  category: CategoryDto;
  payee: PayeeDto;
  tag: TagDto;
  account: AccountDto;
  accountRecipient: AccountDto | null;
}

const { avatarUrl } = useAvatar();
const { moneyFormat } = useMoney();

const props = defineProps<{
  position: 'standard' | 'top' | 'right' | 'bottom' | 'left' | undefined;
  transaction: Transaction;
  account: AccountDto;
  accountRecipient: AccountDto | null;
  canChangeTransaction: boolean;
}>();

const emit = defineEmits<{
  (e: 'cancel'): void;
  (e: 'update', id: Id): void;
  (e: 'delete', id: Id): void;
}>();

const cancel = () => {
  emit('cancel');
};

const openUpdateModal = (id: Id) => {
  emit('update', id);
};

const openDeleteModal = (id: Id) => {
  emit('delete', id);
};

const isTransfer = (transaction: Transaction) => {
  return transaction.type === 'transfer';
};
</script>
