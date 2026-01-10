export function sanitizeInput(value: string): string {
  // Convert the input to a string (if not already a string)
  value = value.toString();

  // Remove any character that is not a number, an operator (+, -, *, /), or a decimal separator (.,)
  value = value.replace(/[^0-9+\-*/=.,]/g, '');

  // Replace ',' with '.' to standardize the decimal separator
  value = value.replace(/,/g, '.');

  // Remove consecutive operators (except for '-')
  value = value.replace(/(?<!\d)[\+\*\/\.]{2,}/g, '');

  // Split the formula into parts based on operators to handle leading zeros only for the integer part
  const parts = value.split(/([+\-*/])/); // Split by operators to handle each part independently

  // Remove leading zeros in non-decimal numbers only
  const sanitizedParts = parts.map(part => {
    if (part.includes('.')) {
      return part; // Keep decimal numbers intact
    }
    return part.replace(/\b0+(\d+)/g, '$1'); // Remove leading zeros from non-decimal numbers
  });

  // Join the sanitized parts back together
  return sanitizedParts.join('');
}

export function validateFormula(formula: string | number): boolean {
  // Validate the formula dynamically
  formula = formula.toString();
  if (formula === '') {
    return true;
  }
  try {
    // Evaluate the formula in a safe way
    if (formula && formula !== '=') {
      const result = eval(formula.replace('=', ''));
      if (isNaN(result)) {
        return false;
      }
    }
    return true;
  } catch (error) {
    return false;
  }
}

export function evaluateFormula(value: string | number): string {
  value = value.toString();
  if (value.includes('=')) {
    try {
      const valueWithoutEqual = value.replace('=', '');
      const result = eval(valueWithoutEqual);

      // Check if the result is a valid number and round it to a fixed number of decimal places (e.g., 10)
      if (!isNaN(result)) {
        return roundToPrecision(result, 10).toString(); // Replace the formula with the rounded result
      }

      return value.replace('=', ''); // Remove "=" if formula is incorrect
    } catch (error) {
      return value.replace('=', ''); // Remove "=" if formula is incorrect
    }
  }
  return value;
}

function roundToPrecision(num: number, precision: number): number {
  const factor = Math.pow(10, precision);
  return Math.round(num * factor) / factor;
}
