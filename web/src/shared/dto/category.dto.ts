import { BooleanType, DateString, Icon, Id } from '../types';

export enum CategoryType {
  INCOME = 'income',
  EXPENSE = 'expense',
}

export interface CategoryDto {
  id: Id;
  ownerUserId: Id;
  name: string;
  position: number;
  type: CategoryType;
  icon: Icon;
  isArchived: BooleanType;
  updatedAt: DateString;
  createdAt: DateString;
}

export interface CategoryListDto {
  items: CategoryDto[];
}

export interface CategoryItemDto {
  item: CategoryDto;
}