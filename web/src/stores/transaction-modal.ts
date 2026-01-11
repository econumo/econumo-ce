import { defineStore } from 'pinia';
import { v4 as uuid } from 'uuid';
import _ from 'lodash';
import { date } from 'quasar';
import { METRICS, trackEvent } from '../modules/metrics';
import { useAccountsStore } from './accounts';
import { ref, computed } from 'vue';
import { DateString, Id } from '@shared/types';
import { DATE_TIME_FORMAT } from '../modules/constants';
import { useMoney } from '../composables/useMoney';

export const useTransactionModalStore = defineStore('transaction-modal', () => {
  const { moneyFormat } = useMoney();
  const transactionModalNew = ref(false);
  const transactionModalOpened = ref(false);
  const transactionModalId = ref<Id | null>(null);
  const transactionModalOpenDate = ref<DateString | null>(null);
  const transactionModalType = ref<string | null>(null);
  const transactionModalUserId = ref<Id | null>(null);
  const transactionModalAccountId = ref<Id | null>(null);
  const transactionModalAccountRecipientId = ref<Id | null>(null);
  const transactionModalAmount = ref<number | null>(null);
  const transactionModalAmountRecipient = ref<number | null>(null);
  const transactionModalCategoryId = ref<Id | null>(null);
  const transactionModalDescription = ref<string | null>(null);
  const transactionModalPayeeId = ref<Id | null>(null);
  const transactionModalTagId = ref<Id | null>(null);
  const transactionModalSwitchAccount = ref<boolean | null>(null);

  const isTransactionModalCreation = computed(() => transactionModalNew.value);
  const isTransactionModalOpened = computed(() => transactionModalOpened.value);

  function openTransactionModal(params: any) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_OPEN, { type: params.id ? 'edit' : 'create' });
    const transactionData: any = {};
    transactionData.id = params.id || uuid();
    transactionData.isNew = !params.id;
    transactionData.date = params.date || date.formatDate(Date.now(), DATE_TIME_FORMAT);
    transactionData.type = params.type || 'expense';
    const accountsStore = useAccountsStore();
    transactionData.accountId = params.accountId || accountsStore.selectedAccountId;
    if (transactionData.accountId) {
      transactionData.userId = _.find(accountsStore.accountsOrdered, { id: transactionData.accountId })?.owner.id || null;
    }
    transactionData.accountRecipientId = params.accountRecipientId || null;

    const account = _.find(accountsStore.accountsOrdered, { id: transactionData.accountId });
    const accountCurrencyId = account?.currency.id || null;
    const accountRecipient = _.find(accountsStore.accountsOrdered, { id: transactionData.accountRecipientId });
    const accountRecipientCurrencyId = accountRecipient?.currency.id || null;
    transactionData.amount = params.id ? moneyFormat(params.amount, accountCurrencyId, false, false, false) : null;
    transactionData.amountRecipient = params.id ? moneyFormat(params.amountRecipient, accountRecipientCurrencyId, false, false, false) : null;
    transactionData.categoryId = params.categoryId || null;
    transactionData.description = params.description || '';
    transactionData.payeeId = params.payeeId || null;
    transactionData.tagId = params.tagId || null;

    transactionModalOpened.value = true;
    transactionModalNew.value = transactionData.isNew;
    transactionModalId.value = transactionData.id;
    transactionModalOpenDate.value = transactionData.date;
    transactionModalType.value = transactionData.type;
    transactionModalAccountId.value = transactionData.accountId;
    transactionModalUserId.value = transactionData.userId;
    transactionModalAccountRecipientId.value = transactionData.accountRecipientId;
    transactionModalAmount.value = transactionData.amount;
    transactionModalAmountRecipient.value = transactionData.amountRecipient;
    transactionModalCategoryId.value = transactionData.categoryId;
    transactionModalDescription.value = transactionData.description;
    transactionModalPayeeId.value = transactionData.payeeId;
    transactionModalTagId.value = transactionData.tagId;
    transactionModalSwitchAccount.value = null;
  }

  function closeTransactionModal() {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CLOSE);
    transactionModalOpened.value = false;
    transactionModalSwitchAccount.value = null;
    transactionModalUserId.value = null;
    transactionModalOpenDate.value = null;
    transactionModalType.value = null;
    transactionModalAccountId.value = null;
    transactionModalAccountRecipientId.value = null;
  }

  function changeTransactionModalType(value: string) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_TYPE, { type: value });
    transactionModalType.value = value;
  }

  function changeTransactionModalAccount(value: Id) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_ACCOUNT);
    transactionModalAccountId.value = value;
  }

  function changeTransactionModalAccountRecipient(value: Id) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_ACCOUNT_RECIPIENT);
    transactionModalAccountRecipientId.value = value;
  }

  function changeTransactionModalAmount(value: number) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_AMOUNT);
    transactionModalAmount.value = value;
  }

  function changeTransactionModalAmountRecipient(value: number) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_AMOUNT_RECIPIENT);
    transactionModalAmountRecipient.value = value;
  }

  function changeTransactionModalCategory(value: Id | null) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_CATEGORY);
    transactionModalCategoryId.value = value;
  }

  function changeTransactionModalDescription(value: string) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_DESCRIPTION);
    transactionModalDescription.value = value;
  }

  function changeTransactionModalPayee(value: Id) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_PAYEE);
    transactionModalPayeeId.value = value;
  }

  function changeTransactionModalTag(value: Id) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_TAG);
    transactionModalTagId.value = value;
  }

  function changeTransactionModalOpenDate(value: DateString) {
    trackEvent(METRICS.UI_MODAL_TRANSACTION_CHANGE_DATE);
    transactionModalOpenDate.value = value;
  }

  function changeTransactionModalSwitchAccount(value: boolean | null) {
    transactionModalSwitchAccount.value = value;
  }

  return {
    transactionModalNew,
    transactionModalOpened,
    transactionModalId,
    transactionModalOpenDate,
    transactionModalType,
    transactionModalUserId,
    transactionModalAccountId,
    transactionModalAccountRecipientId,
    transactionModalAmount,
    transactionModalAmountRecipient,
    transactionModalCategoryId,
    transactionModalDescription,
    transactionModalPayeeId,
    transactionModalTagId,
    transactionModalSwitchAccount,
    isTransactionModalCreation,
    isTransactionModalOpened,
    openTransactionModal,
    closeTransactionModal,
    changeTransactionModalType,
    changeTransactionModalAccount,
    changeTransactionModalAccountRecipient,
    changeTransactionModalAmount,
    changeTransactionModalAmountRecipient,
    changeTransactionModalCategory,
    changeTransactionModalDescription,
    changeTransactionModalPayee,
    changeTransactionModalTag,
    changeTransactionModalOpenDate,
    changeTransactionModalSwitchAccount
  };
});
