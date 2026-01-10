import { CurrencyDto } from './currency.dto';
import { LegacyUserAccessDto } from './access.dto';
import { UserDto } from './user.dto';
import { DateString, Id } from '../types';
import { TransactionDto } from './transaction.dto';

export enum AccountType {
  CASH = 1,
  CREDIT_CARD = 2,
}

export interface AccountListDto {
  items: AccountDto[];
}

export interface AccountItemDto {
  item: AccountDto;
  transaction?: TransactionDto | null;
}

export interface AccountDto {
  id: Id;
  owner: UserDto;
  folderId: Id | null;
  name: string;
  position: number;
  currency: CurrencyDto;
  balance: number;
  type: AccountType;
  icon: string;
  sharedAccess: LegacyUserAccessDto[];
}

export interface AccountUpdateDto {
  id: Id;
  name: string;
  balance: number;
  icon: string;
  updatedAt: DateString;
}

export interface AccountCreateDto {
  id: Id;
  name: string;
  currencyId: Id;
  balance: number;
  icon: string;
  folderId: Id | null;
}
