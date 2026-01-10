import axios, { AxiosError, AxiosResponse } from 'axios';
import {setDefaultIfNotFunction} from '../helper'
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';
import { TagItemDto, TagListDto } from '@shared/dto/tag.dto';
import { CreateTagDto, TagItemResponseDto, TagListResponseDto } from './dto/tag.dto';
import { Id } from '../../types';
import { UpdatePayeeDto } from 'modules/api/v1/dto/payee.dto';

export default Object.assign(new apiClient(), {
  getList(success: any, failure: any): Promise<TagListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] tag.getList');
    return axios.get<TagListResponseDto>(`${appConfig.backendHost()}/api/v1/tag/get-tag-list`)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error))
  },
  create(params: CreateTagDto, success: any, failure: any): Promise<TagItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] tag.create', params);
    return axios.post<TagItemResponseDto>(`${appConfig.backendHost()}/api/v1/tag/create-tag`, params)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error))
  },
  update(params: UpdatePayeeDto, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] tag.update', params);
    return axios.post(`${appConfig.backendHost()}/api/v1/tag/update-tag`, params)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error))
  },
  orderList(changes: ({id: Id, position: number}[]), success: any, failure: any): Promise<TagListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] tag.orderList', changes);
    return axios.post<TagItemResponseDto>(`${appConfig.backendHost()}/api/v1/tag/order-tag-list`, {changes: changes})
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error))
  },
  delete(id: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] tag.delete', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/tag/delete-tag`, {id: id})
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error))
  },
  archive(id: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] tag.archive', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/tag/archive-tag`, {id: id})
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error))
  },
  unarchive(id: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] tag.unarchive', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/tag/unarchive-tag`, {id: id})
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error))
  }
})
