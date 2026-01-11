import { defineStore } from 'pinia';
import CurrenciesAPIv1 from '../modules/api/v1/currency';
import { date } from 'quasar';
import _ from 'lodash';
import { useLocalStorage } from '@vueuse/core';
import { RemovableRef } from '@vueuse/shared';
import { DATE_TIME_FORMAT } from '../modules/constants';
import { StorageKeys } from '../modules/storage';
import { CurrencyDto } from '../modules/api/v1/dto/currency.dto';
import { DateString } from '../modules/types';
import { computed, ComputedRef } from 'vue';

export const useCurrenciesStore = defineStore('currencies', () => {
  const currencies = useLocalStorage(StorageKeys.CURRENCIES, []) as RemovableRef<CurrencyDto[]>;
  const currenciesLoadedAt = useLocalStorage(StorageKeys.CURRENCIES_LOADED_AT, null) as RemovableRef<DateString | null>;

  const isCurrenciesLoaded: ComputedRef<boolean> = computed(() => !!currenciesLoadedAt.value);
  const currenciesHash = computed(() => _.keyBy(currencies.value, 'id'));

  function fetchCurrencies() {
    return CurrenciesAPIv1.getList({}, (response: any) => {
      currencies.value = response.data.items;
      currenciesLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
    }, (error: any) => {
      console.log('fetchCurrencies error', error);
      return false;
    });
  }

  return {
    currencies,
    currenciesLoadedAt,
    isCurrenciesLoaded,
    currenciesHash,
    fetchCurrencies
  };
});
