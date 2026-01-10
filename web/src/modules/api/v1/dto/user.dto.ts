export type {
  UserDto,
  CurrentUserDto,
  UserOptionDto
} from '@shared/dto/user.dto';
export { UserOptions } from '@shared/dto/user.dto';

import { CurrentUserDto } from '@shared/dto/user.dto';


export interface UserLoginResponseDto {
  data: UserLoginItemDto;
}

export interface CurrentUserResponseDto {
  data: CurrentUserItemDto;
}

export interface CurrentUserItemDto {
  user: CurrentUserDto;
}

export interface UserLoginItemDto {
  user: CurrentUserDto;
  token: string;
}

export interface UserEmptyResponseDto {
}

