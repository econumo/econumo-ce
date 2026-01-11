import axios from 'axios'
import { defaults } from 'lodash'
import { setDefaultIfNotFunction, restDefaults, getTimezone } from './helper'
import {getToken} from '../storage';
import applicationConfig from '../config'

axios.defaults.headers.common = {
  'Accept': 'application/json'
};
axios.interceptors.request.use((config) => {
  config.headers.Accept = 'application/json';
  if (getToken()) {
    config.headers.Authorization = 'Bearer ' + getToken();
  }
  config.headers['Accept-Language'] = applicationConfig.locale();
  config.headers['X-Timezone'] = getTimezone();
  return config;
}, null, { synchronous: true });

export default class restApi {
  constructor (options = {}) {
    defaults(this, options, restDefaults);
    this.$client = axios.create()
  }
}

