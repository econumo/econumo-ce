import { UserDto } from './user.dto';
import { Id } from '../types';

export enum AccessRole {
  OWNER = 'owner',
  ADMIN = 'admin',
  USER = 'user',
  READ_ONLY = 'guest',
}

export enum AcceptanceStatus {
  ACCEPTED = 1,
  NOT_ACCEPTED = 0,
}

export interface LegacyUserAccessDto {
  user: UserDto;
  role: AccessRole;
}

export interface UserAccessDto extends LegacyUserAccessDto {
  isAccepted: AcceptanceStatus;
}

export interface LegacyAccountAccessDto {
  id: Id,
  ownerUserId: Id,
  role: AccessRole
}