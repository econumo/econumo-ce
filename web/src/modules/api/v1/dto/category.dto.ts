export type { CategoryType, CategoryDto, CategoryListDto, CategoryItemDto } from '@shared/dto/category.dto';

import { Icon, Id } from '@shared/types';
import { CategoryListDto, CategoryItemDto, CategoryType } from '@shared/dto/category.dto';


export interface CategoryListResponseDto {
  data: CategoryListDto;
}

export interface CategoryItemResponseDto {
  data: CategoryItemDto;
}

export interface CreateCategoryDto {
  id: Id;
  name: string;
  type: CategoryType;
  accountId: Id | null;
  icon: Icon | null;
}

export interface UpdateCategoryDto {
  id: Id;
  name: string;
  icon: Icon;
}
