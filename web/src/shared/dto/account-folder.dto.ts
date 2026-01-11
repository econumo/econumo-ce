import { DateString, Id } from '../types';

export enum AccountFolderVisibility {
  Hidden = 0,
  Visible = 1,
}

export interface AccountFolderListDto {
  items: AccountFolderDto[];
}

export interface AccountFolderItemDto {
  item: AccountFolderDto;
}

export interface AccountFolderDto {
  id: Id;
  name: string;
  position: number;
  isVisible: AccountFolderVisibility;
}

export interface UpdateAccountFolderDto {
  id: Id;
  name: string;
}

export interface CreateAccountFolderDto {
  name: string;
}