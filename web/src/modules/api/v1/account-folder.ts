import axios, { AxiosError, AxiosResponse } from 'axios';
import { setDefaultIfNotFunction } from '../helper';
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';
import { Id } from '../../types';
import {
  AccountFolderEmptyResponseDto,
  AccountFolderItemDto, AccountFolderListDto, AccountFolderListResponseDto,
  CreateAccountFolderDto,
  UpdateAccountFolderDto
} from './dto/account-folder.dto';
import { AccountFolderItemResponseDto } from '../../../modules/api/v1/dto/account-folder.dto';

export default Object.assign(new apiClient(), {
  getList(success: any, failure: any): Promise<AccountFolderListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.getFolderList');
    return axios.get<AccountFolderListResponseDto>(`${appConfig.backendHost()}/api/v1/account/get-folder-list`)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  create(params: CreateAccountFolderDto, success: any, failure: any): Promise<AccountFolderItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.createFolder', params);
    return axios.post<AccountFolderItemResponseDto>(`${appConfig.backendHost()}/api/v1/account/create-folder`, params)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  update(params: UpdateAccountFolderDto, success: any, failure: any): Promise<AccountFolderItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.updateFolder', params);
    return axios.post<AccountFolderItemResponseDto>(`${appConfig.backendHost()}/api/v1/account/update-folder`, params)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  replace(id: Id, replaceId: Id, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.replaceFolder', id, replaceId);
    return axios.post(`${appConfig.backendHost()}/api/v1/account/replace-folder`, { id: id, replaceId: replaceId })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  orderList(changes: any[], success: any, failure: any): Promise<AccountFolderListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.orderFolderList', changes);
    return axios.post<AccountFolderListResponseDto>(`${appConfig.backendHost()}/api/v1/account/order-folder-list`, { changes: changes })
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  hide(id: Id, success: any, failure: any): Promise<void> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.hideFolder', id);
    return axios.post<AccountFolderEmptyResponseDto>(`${appConfig.backendHost()}/api/v1/account/hide-folder`, { id: id })
      .then(response => success())
      .catch((error: AxiosError) => failure(error.response));
  },
  show(id: Id, success: any, failure: any): Promise<void> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] account.showFolder', id);
    return axios.post<AccountFolderEmptyResponseDto>(`${appConfig.backendHost()}/api/v1/account/show-folder`, { id: id })
      .then(response => success())
      .catch((error: AxiosError) => failure(error.response));
  }
});
