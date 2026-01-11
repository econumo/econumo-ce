import axios, { AxiosError, AxiosResponse } from 'axios';
import {setDefaultIfNotFunction} from '../helper'
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';
import { Id } from '../../types';
import { AccessRole } from './dto/connection.dto';

export default Object.assign(new apiClient(), {
  getConnectionList(params: any, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] connection.getUserList', params);
    return axios.get(`${appConfig.backendHost()}/api/v1/connection/get-connection-list`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  generateInvite(success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] connection.generateInvite');
    return axios.post(`${appConfig.backendHost()}/api/v1/connection/generate-invite`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  deleteInvite(success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] connection.deleteInvite');
    return axios.post(`${appConfig.backendHost()}/api/v1/connection/delete-invite`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  acceptInvite(code: string, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] connection.acceptInvite', code);
    return axios.post(`${appConfig.backendHost()}/api/v1/connection/accept-invite`, {code: code})
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  deleteConnection(userId: Id, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] connection.delete', userId);
    return axios.post(`${appConfig.backendHost()}/api/v1/connection/delete-connection`, {id: userId})
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  setAccountAccess(params: {userId: Id, accountId: Id, role: AccessRole}, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] connection.setAccountAccess', params);
    return axios.post(`${appConfig.backendHost()}/api/v1/connection/set-account-access`, {userId: params.userId, accountId: params.accountId, role: params.role})
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  revokeAccountAccess(params: {userId: Id, accountId: Id}, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] connection.revokeAccountAccess', params);
    return axios.post(`${appConfig.backendHost()}/api/v1/connection/revoke-account-access`, {userId: params.userId, accountId: params.accountId})
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
})
