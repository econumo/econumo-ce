import {
  isValidFormula,
  isValidHttpUrl,
  isValidEmail,
  isValidNumber,
  isValidDecimalNumber,
  isValidName,
  isValidFolderName,
  isValidAccountName,
  isValidCategoryName,
  isValidTagName,
  isValidPayeeName,
  isValidBudgetName,
  isValidPassword,
  isValidBudgetFolderName,
  isValidBudgetEnvelopeName,
  isNotEmpty,
  isValidRecoveryCode,
  hasIncompleteFormula
} from '../modules/helpers/validation';

/**
 * Composable function providing form validation utilities
 * @returns Object containing validation methods
 */
export function useValidation() {
  return {
    isValidHttpUrl,
    isValidEmail,
    isValidNumber,
    isValidDecimalNumber,
    isValidName,
    isValidFolderName,
    isValidAccountName,
    isValidCategoryName,
    isValidTagName,
    isValidPayeeName,
    isValidBudgetName,
    isValidPassword,
    isValidBudgetFolderName,
    isValidBudgetEnvelopeName,
    isNotEmpty,
    isValidRecoveryCode,
    isValidFormula,
    hasIncompleteFormula,
  };
}

// Export type for TypeScript support
export type ValidationUtils = ReturnType<typeof useValidation>; 