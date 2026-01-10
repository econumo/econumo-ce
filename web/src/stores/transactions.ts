import { defineStore } from 'pinia';
import _ from 'lodash';
import { date } from 'quasar';
import TransactionsAPIv1 from '../modules/api/v1/transaction';
import { METRICS, trackEvent } from '../modules/metrics';
import {useAccountsStore} from './accounts';
import {useBudgetsStore} from './budgets';
import {useCategoriesStore} from './categories';
import {useTagsStore} from './tags';
import {usePayeesStore} from './payees';
import { AccountDto } from 'modules/api/v1/dto/account.dto';
import { DateString, Id } from 'modules/types';
import { useLocalStorage } from '@vueuse/core';
import { StorageKeys } from '../modules/storage';
import { RemovableRef } from '@vueuse/shared';
import {
  CreateTransactionDto,
  TransactionDto,
  TransactionItemDto,
  TransactionListDto, UpdateTransactionDto
} from '../modules/api/v1/dto/transaction.dto';
import { computed, ComputedRef } from 'vue';
import { DATE_TIME_FORMAT } from '../modules/constants';

interface ViewTransactionDto extends TransactionDto {
  account: AccountDto | null;
  accountRecipient: AccountDto | null;
  category: any | null;
  tag: any | null;
  payee: any | null;
  search: string | null;
}

export const useTransactionsStore = defineStore('transactions', () => {
  const _transactions = useLocalStorage(StorageKeys.TRANSACTIONS, []) as RemovableRef<TransactionDto[]>;
  const transactionsLoadedAt = useLocalStorage(StorageKeys.TRANSACTIONS_LOADED_AT, null) as RemovableRef<DateString | null>;

  const isTransactionsLoaded = computed(() => !!transactionsLoadedAt.value);
  const transactions: ComputedRef<ViewTransactionDto[]> = computed(() => {
    const categoriesStore = useCategoriesStore();
    const tagsStore = useTagsStore();
    const payeesStore = usePayeesStore();
    const accountsStore = useAccountsStore();
    const categories = categoriesStore.categories,
      tags = tagsStore.tags,
      payees = payeesStore.payees,
      accounts = accountsStore.accounts;
    const result: ViewTransactionDto[] = [];
    _.orderBy(_transactions.value, 'date', 'desc').forEach((item) => {
      const newItem: ViewTransactionDto = {
        ...item,
        amount: Number(item.amount),
        amountRecipient: item.amountRecipient !== null ? Number(item.amountRecipient) : null,
        account: null,
        accountRecipient: null,
        category: null,
        tag: null,
        payee: null,
        search: null,
      };
      newItem.account = (_.find(accounts, {id: item.accountId}) as AccountDto) ?? null;
      newItem.accountRecipient = (_.find(accounts, {id: item.accountRecipientId}) as AccountDto) ?? null;
      newItem.category = _.find(categories, {id: item.categoryId});
      newItem.tag = _.find(tags, {id: item.tagId});
      newItem.payee = _.find(payees, {id: item.payeeId});
      newItem.search = [
        newItem.amount,
        newItem.amountRecipient,
        _.toLower('@' + item.author.name),
        _.toLower(newItem.category?.name || ''),
        _.toLower(item.description),
        _.toLower(newItem.payee?.name || ''),
        _.toLower(newItem.tag?.name || ''),
        (item.type === 'expense' ? '-' : '+'),
        _.toLower(item.type),
        item.date,
      ].join('|');
      result.push(newItem);
    })

    return result;
  });
  const transactionsOrdered = computed(() => transactions.value);

  function createTransaction(dto: CreateTransactionDto) {
    trackEvent(METRICS.TRANSACTION_CREATE);
    const accountsStore = useAccountsStore();
    const budgetsStore = useBudgetsStore();
    return TransactionsAPIv1.add(dto, (response: TransactionItemDto) => {
      accountsStore.updateAccounts(response.accounts);
      TRANSACTION_ADDED(response.item);
      budgetsStore.resetCachedBudget();
      return response.item
    }, (error: any) => {
      console.log('[ERROR] Transactions store', error);
      return error
    })
  }

  function updateTransaction(dto: UpdateTransactionDto) {
    trackEvent(METRICS.TRANSACTION_UPDATE);
    const accountsStore = useAccountsStore();
    const budgetsStore = useBudgetsStore();
    return TransactionsAPIv1.update(dto, (response: TransactionItemDto) => {
      accountsStore.updateAccounts(response.accounts);
      TRANSACTION_UPDATED(response.item);
      budgetsStore.resetCachedBudget();
      return response.item
    }, (error: any) => {
      console.log('[ERROR] Transactions store', error);
      return error
    })
  }

  function fetchTransactions() {
    return TransactionsAPIv1.getList((response: TransactionListDto) => {
      TRANSACTIONS_INIT(response.items);
    }, (error: any) => {
      console.log('[ERROR] Transactions store', error);
      return error
    })
  }

  function deleteTransaction(id: Id) {
    trackEvent(METRICS.TRANSACTION_DELETE);
    const accountsStore = useAccountsStore();
    const budgetsStore = useBudgetsStore();
    return TransactionsAPIv1.delete(id, (response: TransactionItemDto) => {
      accountsStore.updateAccounts(response.accounts);
      TRANSACTION_DELETED(id);
      budgetsStore.resetCachedBudget();
      return response.item;
    }, (error: any) => {
      console.log('[ERROR] Transactions store', error);
      return error
    })
  }

  function TRANSACTION_ADDED(transaction: TransactionDto) {
    console.log('TRANSACTION_ADDED', transaction);
    _transactions.value.push({
      ...transaction,
      amount: Number(transaction.amount),
      amountRecipient: transaction.amountRecipient !== null ? Number(transaction.amountRecipient) : null
    });
  }

  function TRANSACTION_UPDATED(newTransaction: TransactionDto) {
    _.remove(_transactions.value, (item) => {
      return item.id === newTransaction.id;
    })
    _transactions.value.push({
      ...newTransaction,
      amount: Number(newTransaction.amount),
      amountRecipient: newTransaction.amountRecipient !== null ? Number(newTransaction.amountRecipient) : null
    });
  }

  function TRANSACTION_DELETED(transactionId: Id) {
    _.remove(_transactions.value, (item) => {
      return item.id === transactionId;
    });
  }

  function TRANSACTIONS_INIT(items: TransactionDto[]) {
    _transactions.value = items.map(item => ({
      ...item,
      amount: Number(item.amount),
      amountRecipient: item.amountRecipient !== null ? Number(item.amountRecipient) : null
    }));
    transactionsLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
  }

  function TRANSACTIONS_TAG_DELETE(tagId: Id) {
    _transactions.value.forEach((item) => {
      if (item.tagId === tagId) {
        item.tagId = null;
      }
    });
  }

  function TRANSACTIONS_PAYEE_DELETE(payeeId: Id) {
    _transactions.value.forEach((item) => {
      if (item.payeeId === payeeId) {
        item.payeeId = null;
      }
    });
  }

  function TRANSACTIONS_CATEGORY_DELETE(categoryId: Id) {
    _transactions.value.forEach((item) => {
      if (item.categoryId === categoryId) {
        item.categoryId = null;
      }
    });
  }

  function TRANSACTIONS_CATEGORY_REPLACE(options: any) {
    const copyTransactions = _.cloneDeep(_transactions.value);
    copyTransactions.forEach((item) => {
      if (item.categoryId === options.oldId) {
        item.categoryId = options.newId;
      }
    });
    _transactions.value = copyTransactions;
  }

  function TRANSACTIONS_ACCOUNT_DELETE(accountId: Id) {
    const copyTransactions = _.cloneDeep(_transactions.value);
    _.remove(copyTransactions, (item) => {
      return item.accountId === accountId
    });
    _transactions.value = copyTransactions;
  }

  return {
    _transactions,
    transactions,
    transactionsLoadedAt,
    isTransactionsLoaded,
    transactionsOrdered,
    createTransaction,
    updateTransaction,
    fetchTransactions,
    deleteTransaction,
    TRANSACTION_ADDED,
    TRANSACTIONS_TAG_DELETE,
    TRANSACTIONS_PAYEE_DELETE,
    TRANSACTIONS_CATEGORY_DELETE,
    TRANSACTIONS_CATEGORY_REPLACE,
    TRANSACTIONS_ACCOUNT_DELETE,
  }
});
