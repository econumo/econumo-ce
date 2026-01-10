import axios, { AxiosError, AxiosResponse } from 'axios';
import {setDefaultIfNotFunction} from '../helper'
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';
import { Id } from '../../types';
import { PayeeItemDto, PayeeListDto, PayeeListResponseDto, PayeeItemResponseDto } from './dto/payee.dto';

export default Object.assign(new apiClient(), {
  getList(success: any, failure: any): Promise<PayeeListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] payee.getList');
    return axios.get<PayeeListResponseDto>(`${appConfig.backendHost()}/api/v1/payee/get-payee-list`)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  create(params = {}, success: any, failure: any): Promise<PayeeItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] payee.create', params);
    return axios.post(`${appConfig.backendHost()}/api/v1/payee/create-payee`, params)
      .then((response: AxiosResponse<PayeeItemResponseDto>) => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  update(params = {}, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] payee.update', params);
    return axios.post(`${appConfig.backendHost()}/api/v1/payee/update-payee`, params)
      .then((response: AxiosResponse<any>) => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  orderList(changes: ({id: Id, position: number}[]), success: any, failure: any): Promise<PayeeListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] payee.orderList', changes);
    return axios.post<PayeeListResponseDto>(`${appConfig.backendHost()}/api/v1/payee/order-payee-list`, {changes: changes})
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  delete(id: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] payee.delete', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/payee/delete-payee`, {id: id})
      .then((response: AxiosResponse<any>) => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  archive(id: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] payee.archive', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/payee/archive-payee`, {id: id})
      .then((response: AxiosResponse<any>) => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  unarchive(id: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] payee.unarchive', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/payee/unarchive-payee`, {id: id})
      .then((response: AxiosResponse<any>) => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  }
})
