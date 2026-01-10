import { defineStore } from 'pinia';
import { v4 as uuid } from 'uuid';
import { defaultAccountIcon } from '../modules/icons';
import { METRICS, trackEvent } from '../modules/metrics';
import { DateString, Id } from '../modules/types';
import { ref, computed, Ref } from 'vue';
import { Icon } from '@shared/types';
import { useUsersStore } from '../stores/users';
import { useMoney } from '../composables/useMoney';

export const useAccountModalStore = defineStore('account-modal', () => {
  const { moneyFormat } = useMoney();
  const accountFoldersLoaded = ref(false);
  const accountFolders = ref<any[]>([]);
  const accountFoldersLoadedAt = ref(null) as Ref<DateString | null>;
  const accountFoldersOpened = ref<Record<Id, boolean>>({});
  const accountModalOpened = ref(false);
  const accountModalNew = ref(false);
  const accountModalId = ref<Id | null>(null);
  const accountModalName = ref<string | null>(null);
  const accountModalBalance = ref<number | null>(null);
  const accountModalCurrencyId = ref<Id | null>(null);
  const accountModalIcon = ref<Icon | null>(null);
  const accountModalFolderId = ref<Id | null>(null);

  const isAccountModalCreation = computed(() => accountModalNew.value);
  const isAccountModalOpened = computed(() => accountModalOpened.value);

  function openAccountModal(params: any) {
    trackEvent(METRICS.UI_MODAL_ACCOUNT_OPEN);
    const accountModal: any = {};
    accountModal.isNew = !params.id;
    accountModal.id = params.id || uuid();
    accountModal.name = params.name ?? '';
    if (params.currency) {
      accountModal.currencyId = params.currency?.id ?? null;
    } else if (params.currencyId) {
      accountModal.currencyId = params.currencyId ?? null;
    } else {
      const userStore = useUsersStore();
      accountModal.currencyId = userStore.userCurrencyId;
    }
    accountModal.balance = params.id ? moneyFormat(params.balance, accountModal.currencyId, false, false, false) : 0;
    accountModal.icon = params.icon ?? defaultAccountIcon;
    accountModal.folderId = params.folderId ?? null;
    OPEN_ACCOUNT_MODAL(accountModal);
  }

  function closeAccountModal() {
    trackEvent(METRICS.UI_MODAL_ACCOUNT_CLOSE);
    CLOSE_ACCOUNT_MODAL();
  }

  function changeAccountModalName(value: string) {
    trackEvent(METRICS.UI_MODAL_ACCOUNT_CHANGE_NAME);
    accountModalName.value = value;
  }

  function changeAccountModalBalance(value: number) {
    trackEvent(METRICS.UI_MODAL_ACCOUNT_CHANGE_BALANCE);
    accountModalBalance.value = value;
  }

  function changeAccountModalCurrencyId(value: Id) {
    trackEvent(METRICS.UI_MODAL_ACCOUNT_CHANGE_CURRENCY);
    accountModalCurrencyId.value = value;
  }

  function changeAccountModalIcon(value: Icon) {
    trackEvent(METRICS.UI_MODAL_ACCOUNT_CHANGE_ICON);
    accountModalIcon.value = value;
  }

  function OPEN_ACCOUNT_MODAL(options: any) {
    accountModalOpened.value = true;
    accountModalNew.value = options.isNew;
    accountModalId.value = options.id;
    accountModalName.value = options.name;
    accountModalBalance.value = options.balance;
    accountModalCurrencyId.value = options.currencyId;
    accountModalIcon.value = options.icon;
    accountModalFolderId.value = options.folderId;
  }

  function CLOSE_ACCOUNT_MODAL() {
    accountModalOpened.value = false;
    accountModalId.value = null;
    accountModalName.value = null;
    accountModalBalance.value = null;
    accountModalCurrencyId.value = null;
    accountModalIcon.value = null;
    accountModalFolderId.value = null;
  }

  return {
    accountFoldersLoaded,
    accountFolders,
    accountFoldersLoadedAt,
    accountFoldersOpened,
    accountModalOpened,
    accountModalNew,
    accountModalId,
    accountModalName,
    accountModalBalance,
    accountModalCurrencyId,
    accountModalIcon,
    accountModalFolderId,
    isAccountModalCreation,
    isAccountModalOpened,
    openAccountModal,
    closeAccountModal,
    changeAccountModalName,
    changeAccountModalBalance,
    changeAccountModalCurrencyId,
    changeAccountModalIcon
  };
});
