import { Id } from '../types';

export enum UserOptions {
  CURRENCY = 'currency',
  CURRENCY_ID = 'currency_id',
  REPORT_PERIOD = 'report_period',
  BUDGET = 'budget',
  ONBOARDING = 'onboarding',
}

export interface UserDto {
  id: Id;
  avatar: string;
  name: string;
}

export interface CurrentUserDto {
  id: Id;
  name: string;
  email: string;
  avatar: string;
  options: UserOptionDto[];
  /** @deprecated */
  currency: string;
  /** @deprecated */
  reportPeriod: string;
}

export interface UserOptionDto {
  name: UserOptions;
  value: string | null;
}