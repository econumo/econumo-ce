import { defineStore } from 'pinia';
import { date } from 'quasar';
import CurrenciesAPIv1 from '../modules/api/v1/currency'
import { useLocalStorage } from '@vueuse/core';
import { RemovableRef } from '@vueuse/shared';
import { DATE_TIME_FORMAT } from '../modules/constants';
import { StorageKeys } from '../modules/storage';
import { CurrencyRateDto } from '../modules/api/v1/dto/currency.dto';
import { DateString } from '../modules/types';
import { computed, ComputedRef } from 'vue';

export const useCurrencyRatesStore = defineStore('currency-rates', () => {
  const currencyRates = useLocalStorage(StorageKeys.CURRENCY_RATES, []) as RemovableRef<CurrencyRateDto[]>;
  const currencyRatesLoadedAt = useLocalStorage(StorageKeys.CURRENCY_RATES_LOADED_AT, null) as RemovableRef<DateString | null>;

  const isCurrencyRatesLoaded: ComputedRef<boolean> = computed(() => !!currencyRatesLoadedAt.value);

  function fetchCurrencyRates() {
    return CurrenciesAPIv1.getCurrencyRatesList({}, (response: any) => {
      currencyRates.value = response.data.items.map((item: CurrencyRateDto) => ({
        ...item,
        rate: Number(item.rate)
      }));
      currencyRatesLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
    }, (error: any) => {
      console.log('fetchCurrencyRates error', error);
      return false;
    })
  }

  return {
    currencyRates,
    currencyRatesLoadedAt,
    isCurrencyRatesLoaded,
    fetchCurrencyRates,
  }
});
