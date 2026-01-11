import { Cookies } from 'quasar'
import jwt_decode from 'jwt-decode'
import config from './config';

export enum StorageKeys {
  ACCOUNTS = 'store/accounts/accounts',
  ACCOUNTS_LOADED_AT = 'store/accounts/accounts_loaded_at',
  ACCOUNT_FOLDERS = 'store/account_folders/account_folders',
  ACCOUNT_FOLDERS_LOADED_AT = 'store/account_folders/account_folders_loaded_at',
  ACCOUNT_FOLDERS_OPENED = 'store/account_folders/account_folders_opened',
  BUDGET = 'store/budgets/budget',
  BUDGET_META = 'store/budgets/budget_meta',
  BUDGET_CURRENCIES = 'store/budgets/budget_currencies',
  BUDGET_LOADED_AT = 'store/budgets/budget_loaded_at',
  BUDGET_SELECTED_DATE = 'store/budgets/budget_selected_date',
  BUDGET_FOLDED_FOLDERS = 'store/budgets/budget_folded_folders',
  BUDGET_UNFOLDED_ELEMENTS = 'store/budgets/budget_unfolded_elements',
  BUDGETS = 'store/budgets/budgets',
  BUDGETS_LOADED_AT = 'store/budgets/budgets_loaded_at',
  CATEGORIES = 'store/categories/categories',
  CATEGORIES_LOADED_AT = 'store/categories/categories_loaded_at',
  CONNECTIONS = 'store/connections/connections',
  CONNECTIONS_LOADED_AT = 'store/connections/connections_loaded_at',
  CURRENCIES = 'store/currencies/currencies',
  CURRENCIES_LOADED_AT = 'store/currencies/currencies_loaded_at',
  CURRENCY_RATES = 'store/currency_rates/currency_rates',
  CURRENCY_RATES_LOADED_AT = 'store/currency_rates/currency_rates_loaded_at',
  PAYEES = 'store/payees/payees',
  PAYEES_LOADED_AT = 'store/payees/payees_loaded_at',
  TAGS = 'store/tags/tags',
  TAGS_LOADED_AT = 'store/tags/tags_loaded_at',
  TRANSACTIONS = 'store/transactions/transactions',
  TRANSACTIONS_LOADED_AT = 'store/transactions/transactions_loaded_at',
  USER_DATA = 'store/users/user_data',
  USER_DATA_LOADED_AT = 'store/users/user_data_loaded_at',
}

export const hasToken = function (): boolean {
  return !!Cookies.get('token')
};

export const getToken = function (): string | null {
  return Cookies.get('token') || null;
};

export const setToken = function (token: string): void {
  const data: any = jwt_decode(token);
  let expires = 30;
  if (data.exp) {
    expires = (data.exp - Math.floor(Date.now() / 1000)) / 60 / 60 / 24;
  }
  Cookies.set('token', token, {expires: expires, secure: config.isHttps()});
};

export const removeToken = function (): void {
  Cookies.remove('token');
};

export function getItem(key: string) {
  const value = localStorage.getItem(key)
  if (value !== undefined && value !== null) {
    try {
      return JSON.parse(value)
    } catch (e) {
      return null
    }
  }
  return value
}

export function setItem(key: string, value: string) {
  localStorage.setItem(key, JSON.stringify(value))
}

export function removeItem(key: string) {
  localStorage.removeItem(key)
}
