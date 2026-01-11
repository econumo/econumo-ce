import { UserDto } from './user.dto';
import { LegacyAccountAccessDto } from './access.dto';

export interface ConnectionDto {
  user: UserDto;
  sharedAccounts: LegacyAccountAccessDto[];
}