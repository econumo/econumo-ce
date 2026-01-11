import axios, { AxiosError, AxiosResponse } from 'axios';
import { setDefaultIfNotFunction } from '../helper';
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';
import { CurrentUserDto, CurrentUserResponseDto, UserLoginItemDto, UserLoginResponseDto } from './dto/user.dto';
import { Id } from '@shared/types';

export default Object.assign(new apiClient(), {
  login(username: string, password: string, success: any, failure: any): Promise<UserLoginItemDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.login', { username });
    return axios.post<UserLoginResponseDto>(`${appConfig.backendHost()}/api/v1/user/login-user`, {
      username: username,
      password: password
    })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response?.data));
  },
  logout(success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.logout');
    return axios.post(`${appConfig.backendHost()}/api/v1/user/logout-user`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  register(email = '', password = '', name = '', success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.register', { email });
    return axios.post(`${appConfig.backendHost()}/api/v1/user/register-user`, {
      email: email,
      password: password,
      name: name
    })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  updateName(name = '', success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.updateName', { name });
    return axios.post(`${appConfig.backendHost()}/api/v1/user/update-name`, { name: name })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  updatePassword(oldPassword = '', newPassword = '', success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.updatePassword');
    return axios.post(`${appConfig.backendHost()}/api/v1/user/update-password`, {
      oldPassword: oldPassword,
      newPassword: newPassword
    })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  updateCurrency(currency = '', success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.updateCurrency', { currency });
    return axios.post(`${appConfig.backendHost()}/api/v1/user/update-currency`, { currency: currency })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  updateDefaultBudget(budgetId: Id, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.updateDefaultBudget', budgetId);
    return axios.post(`${appConfig.backendHost()}/api/v1/user/update-budget`, { value: budgetId })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  getUserData(success: (user: CurrentUserDto) => any, failure: (error: any) => any): Promise<CurrentUserDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.getUserData');
    return axios.get<CurrentUserResponseDto>(`${appConfig.backendHost()}/api/v1/user/get-user-data`)
      .then(response => success(response.data.data.user))
      .catch((error: AxiosError) => failure(error.response));
  },
  remindPassword(username = '', success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.userRemindPassword', username);
    return axios.post(`${appConfig.backendHost()}/api/v1/user/remind-password`, { username: username })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  resetPassword(params: any, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.userResetPassword', params);
    return axios.post(`${appConfig.backendHost()}/api/v1/user/reset-password`, {
      username: params.username,
      code: params.code,
      password: params.password
    })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  completeOnboarding(success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] user.completeOnboarding');
    return axios.post(`${appConfig.backendHost()}/api/v1/user/complete-onboarding`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  }
});
