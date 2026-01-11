import { defineStore } from 'pinia';
import { date } from 'quasar';
import { removeItem, StorageKeys } from '../modules/storage';
import { computed, ref } from 'vue';
import { useAccountsStore } from './accounts';
import { useAccountFoldersStore } from './account-folders';
import { useCategoriesStore } from './categories';
import { useTagsStore } from './tags';
import { usePayeesStore } from './payees';
import { useTransactionsStore } from './transactions';
import { useCurrenciesStore } from './currencies';
import { useCurrencyRatesStore } from './currency-rates';
import { useConnectionsStore } from './connections';
import { useBudgetsStore } from './budgets';
import { useUsersStore } from './users';
import { DATE_TIME_FORMAT } from '../modules/constants';

export const useSyncStore = defineStore('sync', () => {
  const initialSync = ref(false);
  const isInitialSynced = computed(() => initialSync);
  const isFullyLoaded = computed(() => {
    return useAccountFoldersStore().isAccountFoldersLoaded &&
      useAccountsStore().isAccountsLoaded &&
      useCategoriesStore().isCategoriesLoaded &&
      useTagsStore().isTagsLoaded &&
      usePayeesStore().isPayeesLoaded &&
      useBudgetsStore().isBudgetsLoaded &&
      useCurrenciesStore().isCurrenciesLoaded &&
      useCurrencyRatesStore().isCurrencyRatesLoaded &&
      useConnectionsStore().isConnectionsLoaded &&
      useUsersStore().isUserDataLoaded &&
      useTransactionsStore().isTransactionsLoaded;
  });

  function fetchAll() {
    clearCache();
    return Promise.all([
      useCategoriesStore().fetchCategories(),
      useTagsStore().fetchTags(),
      usePayeesStore().fetchPayees(),
      useAccountFoldersStore().fetchAccountFolders(),
      useAccountsStore().fetchAccounts(),
      useTransactionsStore().fetchTransactions(),
      useCurrenciesStore().fetchCurrencies(),
      useCurrencyRatesStore().fetchCurrencyRates(),
      useConnectionsStore().fetchConnections(),
      useBudgetsStore().fetchBudgets(),
      useUsersStore().fetchUserData().then(() => {
        useBudgetsStore().fetchUserBudget();
      })
    ]).then(() => {
      initialSync.value = true;
    });
  }

  function fetchUpdates() {
    const config: (
      { updatedAt: string | null; timeout: { minutes: number; }; update: () => void; } | {
      updatedAt: string | null;
      timeout: { hours: number; };
      update: () => void;
    })[] = [
      {
        updatedAt: useUsersStore().userDataLoadedAt,
        timeout: { minutes: 24 },
        update: () => {
          useUsersStore().fetchUserData();
        }
      },
      {
        updatedAt: useAccountFoldersStore().accountFoldersLoadedAt,
        timeout: { minutes: 10 },
        update: () => {
          useAccountFoldersStore().fetchAccountFolders();
        }
      },
      {
        updatedAt: useAccountsStore().accountsLoadedAt,
        timeout: { minutes: 10 },
        update: () => {
          useAccountsStore().fetchAccounts();
        }
      },
      {
        updatedAt: useCategoriesStore().categoriesLoadedAt,
        timeout: { minutes: 10 },
        update: () => {
          useCategoriesStore().fetchCategories();
        }
      },
      {
        updatedAt: useTagsStore().tagsLoadedAt,
        timeout: { minutes: 10 },
        update: () => {
          useTagsStore().fetchTags();
        }
      },
      {
        updatedAt: usePayeesStore().payeesLoadedAt,
        timeout: { minutes: 10 },
        update: () => {
          usePayeesStore().fetchPayees();
        }
      },
      {
        updatedAt: useTransactionsStore().transactionsLoadedAt,
        timeout: { minutes: 10 },
        update: () => {
          useTransactionsStore().fetchTransactions();
        }
      },
      {
        updatedAt: useCurrenciesStore().currenciesLoadedAt,
        timeout: { hours: 24 },
        update: () => {
          useCurrenciesStore().fetchCurrencies();
        }
      },
      {
        updatedAt: useCurrencyRatesStore().currencyRatesLoadedAt,
        timeout: { hours: 24 },
        update: () => {
          useCurrencyRatesStore().fetchCurrencyRates();
        }
      },
      {
        updatedAt: useConnectionsStore().connectionsLoadedAt,
        timeout: { minutes: 10 },
        update: () => {
          useConnectionsStore().fetchConnections();
        }
      },
      {
        updatedAt: useBudgetsStore().budgetsLoadedAt,
        timeout: { minutes: 10 },
        update: () => {
          useBudgetsStore().fetchBudgets();
        }
      },
      {
        updatedAt: useBudgetsStore().budgetLoadedAt,
        timeout: { minutes: 10 },
        update: () => {
          useBudgetsStore().fetchUserBudget();
        }
      }
    ];
    const now = new Date();
    config.forEach(item => {
      let needUpdate = false;
      if (!item.updatedAt) {
        needUpdate = true;
      } else {
        const tmpDate = date.addToDate(date.extractDate(item.updatedAt, DATE_TIME_FORMAT), item.timeout);
        needUpdate = now > tmpDate;
      }
      if (needUpdate) {
        item.update();
      }
    });
  }

  function clearCache() {
    const storageKeysObj: Record<string, string> = { ...StorageKeys };
    for (const key in storageKeysObj) {
      if (storageKeysObj.hasOwnProperty(key) && typeof storageKeysObj[key] === 'string') {
        removeItem(storageKeysObj[key]);
      }
    }
    initialSync.value = false;
  }


  return {
    initialSync,
    isInitialSynced,
    isFullyLoaded,
    fetchAll,
    fetchUpdates,
    clearCache
  };
});
