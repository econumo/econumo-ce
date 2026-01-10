import axios, { AxiosError, AxiosResponse } from 'axios';
import {setDefaultIfNotFunction} from '../helper'
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';
import {
  AccountItemDto,
  AccountListDto,
  AccountListResponseDto,
  AccountItemResponseDto,
  AccountEmptyResponseDto,
  AccountCreateDto,
  AccountUpdateDto
} from './dto/account.dto';
import { Id } from '../../types';

export default Object.assign(new apiClient(), {
  getList(success: any, failure: any): Promise<AccountListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.getList');
    return axios.get<AccountListResponseDto>(`${appConfig.backendHost()}/api/v1/account/get-account-list`)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  create(params: AccountCreateDto, success: any, failure: any): Promise<AccountItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.add', params);
    return axios.post<AccountItemResponseDto>(`${appConfig.backendHost()}/api/v1/account/create-account`, params)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  update(params: AccountUpdateDto, success: any, failure: any): Promise<AccountItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.update', params);
    return axios.post<AccountItemResponseDto>(`${appConfig.backendHost()}/api/v1/account/update-account`, params)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  delete(id: Id, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.delete', id);
    return axios.post<AccountEmptyResponseDto>(`${appConfig.backendHost()}/api/v1/account/delete-account`, {id: id})
      .then(response => success())
      .catch((error: AxiosError) => failure(error.response))
  },
  orderList(changes: any, success: any, failure: any): Promise<AccountListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.orderList', changes);
    return axios.post<AccountListResponseDto>(`${appConfig.backendHost()}/api/v1/account/order-account-list`, {changes: changes})
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
})
