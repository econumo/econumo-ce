import { BooleanType, DateString, Id } from '../types';

export interface PayeeDto {
  id: Id;
  ownerUserId: Id;
  name: string;
  position: number;
  isArchived: BooleanType;
  updatedAt: DateString;
  createdAt: DateString;
}

export interface PayeeListDto {
  items: PayeeDto[];
}

export interface PayeeItemDto {
  item: PayeeDto;
}