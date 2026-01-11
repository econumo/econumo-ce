import axios, { AxiosError, AxiosResponse } from 'axios';
import {setDefaultIfNotFunction} from '../helper'
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';
import { Id } from '../../types';
import {
  TransactionItemDto,
  TransactionListResponseDto,
  TransactionItemResponseDto,
  CreateTransactionDto, UpdateTransactionDto
} from '../../../modules/api/v1/dto/transaction.dto';

export default Object.assign(new apiClient(), {
  add(dto: CreateTransactionDto, success: any, failure: any): Promise<TransactionItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] transaction.add', dto);
    return axios.post<TransactionItemResponseDto>(`${appConfig.backendHost()}/api/v1/transaction/create-transaction`, dto)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  update(dto: UpdateTransactionDto, success: any, failure: any): Promise<TransactionItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] transaction.update', dto);
    return axios.post<TransactionItemResponseDto>(`${appConfig.backendHost()}/api/v1/transaction/update-transaction`, dto)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  getList(success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] transaction.getList');
    return axios.get<TransactionListResponseDto>(`${appConfig.backendHost()}/api/v1/transaction/get-transaction-list`)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  delete(id: Id, success: any, failure: any): Promise<TransactionItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] transaction.delete', id);
    return axios.post<TransactionItemResponseDto>(`${appConfig.backendHost()}/api/v1/transaction/delete-transaction`, {id: id})
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  importTransactionList(
    file: File,
    mapping: Record<string, string | null>,
    success: any,
    failure: any,
    options?: {
      accountId?: Id;
      date?: string;
      categoryId?: Id;
      description?: string;
      payeeId?: Id;
      tagId?: Id;
    }
  ) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] transaction.importTransactionList', { file: file.name, mapping, options });

    const formData = new FormData();
    formData.append('file', file);
    formData.append('mapping', JSON.stringify(mapping));
    if (options?.accountId) {
      formData.append('accountId', String(options.accountId));
    }
    if (options?.date) {
      formData.append('date', String(options.date));
    }
    if (options?.categoryId) {
      formData.append('categoryId', String(options.categoryId));
    }
    if (options?.description) {
      formData.append('description', String(options.description));
    }
    if (options?.payeeId) {
      formData.append('payeeId', String(options.payeeId));
    }
    if (options?.tagId) {
      formData.append('tagId', String(options.tagId));
    }

    return axios.post(`${appConfig.backendHost()}/api/v1/transaction/import-transaction-list`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response || error))
  },
  exportTransactionList(accountIds: Id[], success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] transaction.exportTransactionList', { accountIds });

    const accountIdParam = accountIds.join(',');
    return axios.get(`${appConfig.backendHost()}/api/v1/transaction/export-transaction-list`, {
      params: { accountId: accountIdParam },
      responseType: 'blob'
    })
      .then(response => success(response))
      .catch((error: AxiosError) => failure(error.response || error))
  }
})
