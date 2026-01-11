import axios, { AxiosError, AxiosResponse } from 'axios';
import { setDefaultIfNotFunction } from '../helper';
import apiClient from '../apiClient';
import appConfig from '../../../modules/config';
import { Id } from '../../types';
import {
  BudgetListDto,
  BudgetListResponseDto,
  BudgetTransactionListDto,
  BudgetTransactionListRequestDto,
  CreateBudgetRequestDto,
  UpdateBudgetRequestDto,
  GetBudgetRequestDto,
  CreateBudgetFolderRequestDto,
  MoveElementRequestDto,
  UpdateBudgetFolderRequestDto,
  OrderFolderListRequestDto,
  CreateBudgetEnvelopeRequestDto,
  UpdateBudgetEnvelopeRequestDto,
  DeleteBudgetEnvelopeRequestDto,
  SetElementLimitRequestDto,
  GrantBudgetAccessRequestDto,
  AcceptBudgetAccessRequestDto,
  RevokeBudgetAccessRequestDto,
  DeclineBudgetAccessRequestDto, ChangeCurrencyRequestDto
} from 'modules/api/v1/dto/budget.dto';

export default Object.assign(new apiClient(), {
  getList(success: any, failure: any): Promise<BudgetListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.getList');
    return axios.get<BudgetListResponseDto>(`${appConfig.backendHost()}/api/v1/budget/get-budget-list`)
      .then(response => success(response.data.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  create(request: CreateBudgetRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.create', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/create-budget`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  update(request: UpdateBudgetRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.update', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/update-budget`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  delete(id: Id, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.delete', id);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/delete-budget`, { id: id })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  grantAccess(request: GrantBudgetAccessRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.grantAccess', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/grant-access`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  revokeAccess(request: RevokeBudgetAccessRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.revokeAccess', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/revoke-access`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  acceptAccess(request: AcceptBudgetAccessRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.acceptAccess', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/accept-access`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  declineAccess(request: DeclineBudgetAccessRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.declineAccess', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/decline-access`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  getItem(request: GetBudgetRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.getItem', request);
    return axios.get(`${appConfig.backendHost()}/api/v1/budget/get-budget?id=${request.id}&date=${request.date}`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  getTransactionList(request: BudgetTransactionListRequestDto, success: any, failure: any): Promise<BudgetTransactionListDto> {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.getTransactionList', request);
    return axios.get(`${appConfig.backendHost()}/api/v1/budget/get-transaction-list?budgetId=${request.budgetId}&periodStart=${request.periodStart}&categoryId=${request.categoryId}&tagId=${request.tagId}&envelopeId=${request.envelopeId}`)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  createFolder(request: CreateBudgetFolderRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.createFolder', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/create-folder`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  updateFolder(request: UpdateBudgetFolderRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.updateFolder', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/update-folder`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  deleteFolder(budgetId: Id, folderId: Id, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.deleteFolder', budgetId, folderId);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/delete-folder`, { budgetId: budgetId, id: folderId })
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  orderFolderList(request: OrderFolderListRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.orderFolderList', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/order-folder-list`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  moveElementList(request: MoveElementRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.moveElementList', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/move-element-list`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  createEnvelope(request: CreateBudgetEnvelopeRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.createEnvelope', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/create-envelope`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  updateEnvelope(request: UpdateBudgetEnvelopeRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.updateEnvelope', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/update-envelope`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  deleteEnvelope(request: DeleteBudgetEnvelopeRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.deleteEnvelope', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/delete-envelope`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  setLimit(request: SetElementLimitRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.setLimit', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/set-limit`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  changeElementCurrency(request: ChangeCurrencyRequestDto, success: any, failure: any) {
    success = setDefaultIfNotFunction(success);
    failure = setDefaultIfNotFunction(failure);
    console.log('[API] budget.changeElementCurrency', request);
    return axios.post(`${appConfig.backendHost()}/api/v1/budget/change-element-currency`, request)
      .then(response => success(response.data))
      .catch((error: AxiosError) => failure(error.response));
  },
  // transferEnvelopeBudget(params: any, success: any, failure: any) {
  //   success = setDefaultIfNotFunction(success);
  //   failure = setDefaultIfNotFunction(failure);
  //   console.log('[API] budget.transferEnvelopeBudget', params);
  //   return axios.post(`${appConfig.backendHost()}/api/v1/budget/transfer-envelope-plan`, params)
  //     .then(response => success(response.data))
  //     .catch((error: AxiosError) => failure(error.response));
  // },
  // copyEnvelopesBudget(params: any, success: any, failure: any) {
  //   success = setDefaultIfNotFunction(success);
  //   failure = setDefaultIfNotFunction(failure);
  //   console.log('[API] budget.copyEnvelopesPlan', params);
  //   return axios.post(`${appConfig.backendHost()}/api/v1/budget/copy-envelope-plan`, params)
  //     .then(response => success(response.data))
  //     .catch((error: AxiosError) => failure(error.response));
  // },
  // resetBudget(params: any, success: any, failure: any) {
  //   success = setDefaultIfNotFunction(success);
  //   failure = setDefaultIfNotFunction(failure);
  //   console.log('[API] budget.resetBudget', params);
  //   return axios.post(`${appConfig.backendHost()}/api/v1/budget/reset-plan`, params)
  //     .then(response => success(response.data))
  //     .catch((error: AxiosError) => failure(error.response));
  // }
});
