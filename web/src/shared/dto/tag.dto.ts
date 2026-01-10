import { BooleanType, DateString, Id } from '../types';

export interface TagDto {
  id: Id;
  ownerUserId: Id;
  name: string;
  position: number;
  isArchived: BooleanType;
  updatedAt: DateString;
  createdAt: DateString;
}

export interface TagListDto {
  items: TagDto[];
}

export interface TagItemDto {
  item: TagDto;
}