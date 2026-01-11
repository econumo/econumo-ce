import { UserDto } from './user.dto';
import { DateString, Id } from '../types';
import { AccountDto } from './account.dto';

export enum TransactionType {
  EXPENSE = 'expense',
  INCOME = 'income',
  TRANSFER = 'transfer',
}

export interface TransactionDto extends CreateTransactionDto{
  author: UserDto;
  currencyId: Id;
}

export interface TransactionListDto {
  items: TransactionDto[];
}

export interface TransactionItemDto {
  item: TransactionDto;
  accounts: AccountDto[];
}

export interface CreateTransactionDto {
  id: Id,
  type: TransactionType;
  accountId: Id;
  accountRecipientId: Id | null;
  amount: number;
  amountRecipient: number | null;
  categoryId: Id | null;
  description: string;
  payeeId: Id | null;
  tagId: Id | null;
  date: DateString;
}

export interface UpdateTransactionDto extends TransactionDto {
}