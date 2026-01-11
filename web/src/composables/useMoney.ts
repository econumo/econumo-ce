import { Id } from '../modules/types';
import { useCurrenciesStore } from '../stores/currencies';
import { useDecimalNumber } from './useDecimalNumber';

/**
 * Composable for money formatting operations
 * @returns Object containing money formatting methods
 */
export function useMoney() {
  const currenciesStore = useCurrenciesStore();
  const { normalizeNumber, formatNumber, getFormattedParts } = useDecimalNumber();

  /**
   * Parse amount and get currency information
   */
  const getFormatParams = (amount: number | string, currencyId: Id | null, useNativePrecision = true) => {
    const normalizedAmount = normalizeNumber(amount);
    const currency = currencyId ? currenciesStore.currenciesHash[currencyId] : null;
    // When useNativePrecision is true, use currency's fractionDigits (or 8 if no currency)
    // When false, use actual decimal places (min: currency.fractionDigits, max: 8)
    const digits = useNativePrecision 
      ? (currency?.fractionDigits ?? 8) 
      : Number.isInteger(Number(normalizedAmount))
        ? (currency?.fractionDigits ?? 0)
        : Math.max(
            currency?.fractionDigits ?? 0,
            Math.min(
              (normalizedAmount.split('.')[1] || '').length,
              8
            )
          );
    return { normalizedAmount, currency, digits };
  };

  /**
   * Formats a monetary amount with HTML spans for styling
   * @param amount - Amount to format
   * @param currencyId - Currency ID (defaults to null)
   * @param showCurrency - Whether to show the currency symbol (defaults to true)
   * @param useNativePrecision - Whether to use currency's native fraction digits (defaults to true)
   * @returns HTML string with formatted amount
   */
  const moneyHTML = (
    amount: number | string, 
    currencyId: Id | null = null, 
    showCurrency = true,
    useNativePrecision = true
  ): string => {
    const { normalizedAmount, currency, digits } = getFormatParams(amount, currencyId, useNativePrecision);
    const formattedNumber = formatNumber(normalizedAmount, digits, useNativePrecision);
    const parts = getFormattedParts(formattedNumber);

    const result = [
      '<span class="money-amount">',
      parts[0],
      '</span>'
    ];
    if (parts.length > 1) {
      result.push(
        '<span class="money-decimal">.',
        parts[1],
        '</span>'
      );
    }
    if (showCurrency && currency) {
      result.push('<span class="money-currency">' + currency.symbol + '</span>');
    }
    return result.join('');
  };

  /**
   * Formats a monetary amount as a string
   * @param amount - Amount to format
   * @param currencyId - Currency ID (defaults to null)
   * @param showCurrency - Whether to show the currency symbol (defaults to true)
   * @param useNativePrecision - Whether to use currency's native fraction digits (defaults to true)
   * @param useThousandSeparator - Whether to use thousand separators (defaults to false)
   * @returns Formatted amount string
   */
  const moneyFormat = (
    amount: number | string, 
    currencyId: Id | null = null, 
    showCurrency = true,
    useNativePrecision = true,
    useThousandSeparator = true
  ): string => {
    const { normalizedAmount, currency, digits } = getFormatParams(amount, currencyId, useNativePrecision);
    const formattedNumber = formatNumber(normalizedAmount, digits, useNativePrecision);
    const parts = useThousandSeparator ? getFormattedParts(formattedNumber) : formattedNumber.split('.');

    let result = parts[0];
    if (parts.length > 1) {
      result += '.' + parts[1];
    }
    if (showCurrency && currency) {
      result += ' ' + currency.symbol;
    }
    return result;
  };

  return {
    moneyHTML,
    moneyFormat
  };
} 