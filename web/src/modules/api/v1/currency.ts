import axios, { AxiosError, AxiosResponse } from 'axios';
import {setDefaultIfNotFunction} from '../helper'
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';

export default Object.assign(new apiClient(), {
  getList(params: any, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] currency.getList', params);
    return axios.get(`${appConfig.backendHost()}/api/v1/currency/get-currency-list`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
  getCurrencyRatesList(params: any, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] currency.getCurrencyRatesList', params);
    return axios.get(`${appConfig.backendHost()}/api/v1/currency/get-currency-rate-list`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response))
  },
})
