import { isNil } from 'lodash';
import { CurrencyRateDto } from '../modules/api/v1/dto/currency.dto';
import { useCurrenciesStore } from '../stores/currencies';
import { useCurrencyRatesStore } from '../stores/currency-rates';

/**
 * Composable for currency-related operations
 * @returns Object containing currency conversion methods
 */
export function useCurrency() {
  const currenciesStore = useCurrenciesStore();
  const currencyRatesStore = useCurrencyRatesStore();

  /**
   * Converts an amount from one currency to another using exchange rates
   * @param fromCurrencyId - Source currency ID
   * @param toCurrencyId - Target currency ID
   * @param amount - Amount to convert
   * @param rates - Array of exchange rates, if null will use rates from currencyRatesStore
   * @returns Converted amount as a number
   */
  const exchange = (
    fromCurrencyId: string,
    toCurrencyId: string,
    amount: number | string,
    rates: CurrencyRateDto[] | null = null
  ): number => {
    const parsedAmount = parseFloat(amount.toString());

    // Early return if currencies are the same
    if (fromCurrencyId === toCurrencyId) {
      return parsedAmount;
    }

    // Use rates from store if not provided
    const effectiveRates = rates || currencyRatesStore.currencyRates;

    // Validate rates array
    if (!Array.isArray(effectiveRates) || effectiveRates.length === 0) {
      console.warn('Exchange rates are not available');
      return parsedAmount;
    }

    let result = parsedAmount;
    const toCurrency = currenciesStore.currencies.find(currency => currency.id === toCurrencyId);

    // Validate currency exists
    if (isNil(toCurrency)) {
      console.warn('Target currency not found');
      return parsedAmount;
    }

    // Step 1: Convert from source currency to base currency if needed
    const fromRate = effectiveRates.find(rate => rate.currencyId === fromCurrencyId);
    if (fromRate === undefined) {
      console.warn('Source currency rate not found');
      return parsedAmount;
    }
    if (fromCurrencyId !== fromRate.baseCurrencyId) {
      result = result / parseFloat(fromRate.rate.toString());
    }

    // Step 2: Convert from base currency to target currency if needed
    const toRate = effectiveRates.find(rate => rate.currencyId === toCurrencyId);
    if (toRate === undefined) {
      console.warn('Target currency rate not found');
      return parsedAmount;
    }
    if (toCurrencyId !== toRate.baseCurrencyId) {
      result = result * parseFloat(toRate.rate.toString());
    }

    // Round to the appropriate number of decimal places
    return Number(result.toFixed(toCurrency.fractionDigits));
  };

  return {
    exchange
  };
} 