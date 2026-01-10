import axios, { AxiosError, AxiosResponse } from 'axios';
import {setDefaultIfNotFunction} from '../helper'
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';

export default Object.assign(new apiClient(), {
  getBalance(from: any, to: any, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] analytics.getBalance', from, to);
    return axios.get(`${appConfig.backendHost()}/api/v1/analytics/get-balance?from=${from}&to=${to}`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  }
})
