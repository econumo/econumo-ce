import { Id } from '../../../types';

export type {
  PayeeDto,
  PayeeItemDto,
  PayeeListDto
} from '@shared/dto/payee.dto';

import {
  PayeeItemDto,
  PayeeListDto
} from '@shared/dto/payee.dto';

export interface PayeeListResponseDto {
  data: PayeeListDto;
}

export interface PayeeItemResponseDto {
  data: PayeeItemDto;
}

export interface CreatePayeeDto {
  id: Id;
  name: string;
  accountId: Id | null;
}

export interface UpdatePayeeDto {
  id: Id;
  name: string;
}
