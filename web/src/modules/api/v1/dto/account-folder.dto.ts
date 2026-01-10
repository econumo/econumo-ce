export type {
  AccountFolderDto,
  AccountFolderItemDto,
  AccountFolderListDto,
  UpdateAccountFolderDto,
  CreateAccountFolderDto
} from '@shared/dto/account-folder.dto';
export {AccountFolderVisibility} from '@shared/dto/account-folder.dto';

import {
  AccountFolderItemDto,
  AccountFolderListDto
} from '@shared/dto/account-folder.dto';

export interface AccountFolderListResponseDto {
  data: AccountFolderListDto;
}

export interface AccountFolderItemResponseDto {
  data: AccountFolderItemDto;
}

export interface AccountFolderEmptyResponseDto {
}
