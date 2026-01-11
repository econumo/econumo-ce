export type {
  ConnectionDto
} from '@shared/dto/connection.dto';
export { AccessRole } from '@shared/dto/access.dto';

import {
  ConnectionDto
} from '@shared/dto/connection.dto';

export interface ConnectionsListResponseDto {
  data: ConnectionsListItemsResponseDto;
}

export interface ConnectionsListItemsResponseDto {
  items: ConnectionDto[];
}
