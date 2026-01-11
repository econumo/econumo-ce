import { Id } from '../types';

export interface CurrencyDto {
  id: Id;
  code: string;
  name: string;
  symbol: string;
  fractionDigits: number;
}

export interface CurrencyRateDto {
  currencyId: Id;
  baseCurrencyId: Id;
  rate: number | string;
}