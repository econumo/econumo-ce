import { Id } from '../../../types';

export type {
  TagDto,
  TagItemDto,
  TagListDto
} from '@shared/dto/tag.dto';

import {
  TagItemDto,
  TagListDto
} from '@shared/dto/tag.dto';

export interface TagListResponseDto {
  data: TagListDto;
}

export interface TagItemResponseDto {
  data: TagItemDto;
}

export interface CreateTagDto {
  id: Id;
  name: string;
  accountId: Id | null;
}

export interface UpdateTagDto {
  id: Id;
  name: string;
}
