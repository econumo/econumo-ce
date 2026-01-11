/**
 * Composable for decimal number formatting operations
 * @returns Object containing decimal number formatting methods
 */
export function useDecimalNumber() {
  /**
   * Parse amount and normalize scientific notation with max 8 decimal places
   */
  const normalizeNumber = (amount: number | string): string => {
    if (amount === null || amount === undefined) {
      return '0';
    }

    // Convert to number and handle very small numbers
    const num = typeof amount === 'string' ? Number(amount) : amount;
    if (!Number.isFinite(num)) {
      return '0';
    }

    // For numbers close to zero, use fixed precision to avoid scientific notation
    if (Math.abs(num) < 0.000001) {
      return num.toFixed(8).replace(/\.?0+$/, '') || '0';
    }

    // For regular numbers, convert to string and limit decimal places
    const str = num.toString();
    const [intPart, decPart = ''] = str.split('.');
    if (!decPart) {
      return intPart;
    }

    // Limit decimal places to 8 and remove trailing zeros
    return `${intPart}.${decPart.slice(0, 8)}`.replace(/\.?0+$/, '');
  };

  /**
   * Format number with proper decimal places
   */
  const formatNumber = (amount: number | string, digits: number, useFixedPrecision: boolean): string => {
    if (amount === null || amount === undefined) {
      return '0';
    }

    // Convert to number and handle invalid inputs
    const num = typeof amount === 'string' ? Number(amount) : amount;
    if (!Number.isFinite(num)) {
      return '0';
    }

    if (digits === 0) {
      return Math.round(num).toString();
    }

    if (useFixedPrecision) {
      // Always use exactly the specified number of digits
      return num.toFixed(digits);
    }

    // Get the actual number of decimal places in the number
    const actualDecimals = num.toString().split('.')[1]?.length ?? 0;
    
    // Use at least the specified number of digits, but keep more if they exist
    const useDigits = Math.max(digits, Math.min(actualDecimals, 8));
    return num.toFixed(useDigits);
  };

  /**
   * Split and format parts of the number with thousand separators
   */
  const getFormattedParts = (formattedNumber: string) => {
    const parts = formattedNumber.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return parts;
  };

  /**
   * Check if a number is in scientific notation
   */
  const isScientificNotation = (num: number | string): boolean => {
    const str = num.toString().toLowerCase();
    return str.includes('e');
  };

  return {
    normalizeNumber,
    formatNumber,
    getFormattedParts,
    isScientificNotation
  };
} 