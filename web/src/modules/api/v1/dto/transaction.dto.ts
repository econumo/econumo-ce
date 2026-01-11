export type { TransactionDto, TransactionListDto, TransactionItemDto, CreateTransactionDto, UpdateTransactionDto } from '@shared/dto/transaction.dto';
export { TransactionType } from '@shared/dto/transaction.dto';

import { TransactionListDto, TransactionItemDto, TransactionDto } from '@shared/dto/transaction.dto';

export interface TransactionListResponseDto {
  data: TransactionListDto;
}

export interface TransactionItemResponseDto {
  data: TransactionItemDto;
}
