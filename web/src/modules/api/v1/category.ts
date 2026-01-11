import axios, { AxiosError, AxiosResponse } from 'axios';
import {setDefaultIfNotFunction} from '../helper'
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';
import {
  CategoryItemDto,
  CategoryItemResponseDto,
  CategoryListResponseDto,
  CategoryListDto,
  CreateCategoryDto,
  UpdateCategoryDto
} from './dto/category.dto';
import { Id } from '../../types';

export default Object.assign(new apiClient(), {
  getList(success: any, failure: any): Promise<CategoryListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] category.getList');
    return axios.get<CategoryListResponseDto>(`${appConfig.backendHost()}/api/v1/category/get-category-list`)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  create(params: CreateCategoryDto, success: any, failure: any): Promise<CategoryItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] category.create', params);
    return axios.post<CategoryItemResponseDto>(`${appConfig.backendHost()}/api/v1/category/create-category`, params)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  update(params: UpdateCategoryDto, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] category.update', params);
    return axios.post(`${appConfig.backendHost()}/api/v1/category/update-category`, params)
      .then((response: AxiosResponse<any>) => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  orderList(changes: ({id: Id, position: number}[]), success: any, failure: any): Promise<CategoryListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] category.orderList', changes);
    return axios.post<CategoryListResponseDto>(`${appConfig.backendHost()}/api/v1/category/order-category-list`, {changes: changes})
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error))
  },
  delete(id: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] category.delete', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/category/delete-category`, {id: id, mode: 'delete'})
      .then((response: AxiosResponse<any>) => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  replace(oldId: Id, newId: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] category.replace', oldId, newId);
    return axios.post(`${appConfig.backendHost()}/api/v1/category/delete-category`, {id: oldId, replaceId: newId, mode: 'replace'})
      .then(_ => success())
      .catch((error: AxiosError) => failure(error.response))
  },
  archive(id: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] category.archive', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/category/archive-category`, {id: id})
      .then((response: AxiosResponse<any>) => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  unarchive(id: Id, success: any, failure: any): Promise<any> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] category.unarchive', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/category/unarchive-category`, {id: id})
      .then((response: AxiosResponse<any>) => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  }
})
