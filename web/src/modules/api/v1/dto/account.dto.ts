export type {
  AccountDto,
  AccountItemDto,
  AccountListDto,
  AccountUpdateDto,
  AccountCreateDto,
} from '@shared/dto/account.dto';
export type { AccountType } from '@shared/dto/account.dto';

import {
  AccountItemDto,
  AccountListDto
} from '@shared/dto/account.dto';

export interface AccountListResponseDto {
  data: AccountListDto;
}

export interface AccountItemResponseDto {
  data: AccountItemDto;
}

export interface AccountEmptyResponseDto {
}
