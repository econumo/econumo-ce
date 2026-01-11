import { defineStore } from 'pinia';
import BudgetAPIv1 from '../modules/api/v1/budget';
import { v4 as uuid } from 'uuid';
import _ from 'lodash';
import { date } from 'quasar';
import { METRICS, trackEvent } from '../modules/metrics';
import { useUsersStore } from './users';
import {
  BudgetDto,
  BudgetMetaDto,
  BudgetListDto,
  CreateBudgetRequestDto,
  CreateBudgetResponseDto,
  GetBudgetRequestDto,
  GetBudgetResponseDto,
  BudgetBalanceDto,
  BudgetTransactionListRequestDto,
  GetTransactionListResponseDto,
  BudgetTransactionDto,
  UpdateBudgetRequestDto,
  UpdateBudgetResponseDto,
  CreateBudgetFolderRequestDto,
  CreateBudgetFolderResponseDto,
  DeleteBudgetFolderResponseDto,
  BudgetElementDto,
  MoveElementResponseDto,
  BudgetElementType,
  UpdateBudgetFolderRequestDto,
  UpdateBudgetFolderResponseDto,
  BudgetFolderDto,
  OrderFolderListResponseDto,
  AffectedFolderRequestDto,
  CreateBudgetEnvelopeRequestDto,
  CreateEnvelopeResponseDto,
  BudgetChildElementDto,
  UpdateBudgetEnvelopeRequestDto,
  UpdateEnvelopeResponseDto,
  DeleteBudgetEnvelopeRequestDto,
  DeleteEnvelopeResponseDto,
  SetElementLimitRequestDto,
  SetLimitResponseDto,
  GrantBudgetAccessRequestDto,
  GrantAccessResponseDto,
  AcceptBudgetAccessRequestDto,
  AcceptAccessResponseDto,
  RevokeBudgetAccessRequestDto,
  RevokeAccessResponseDto,
  DeclineBudgetAccessRequestDto,
  DeclineAccessResponseDto,
  DeleteBudgetResponseDto,
  ChangeCurrencyRequestDto, ChangeCurrencyResponseDto
} from '../modules/api/v1/dto/budget.dto';
import { useLocalStorage, StorageSerializers } from '@vueuse/core';
import { RemovableRef } from '@vueuse/shared';
import { computed, ComputedRef, ref, isRef } from 'vue';
import { DateString, Id } from '../modules/types';
import { DATE_FORMAT, DATE_TIME_FORMAT } from '../modules/constants';
import { StorageKeys } from '../modules/storage';
import { AccessRole, UserAccessDto } from '@shared/dto/access.dto';

interface IdBooleanMap {
  [id: Id]: boolean;
}

interface MoveElementLocallyResponseDto {
  element: {
    id: Id,
    origin: {
      position: number,
      folderId: Id | null
    },
    new: {
      position: number,
      folderId: Id | null
    }
  },
  elements: BudgetElementDto[]
}

export const useBudgetsStore = defineStore('budgets', () => {
  const budgets = useLocalStorage(StorageKeys.BUDGETS, []) as RemovableRef<BudgetMetaDto[]>;
  const budgetsLoadedAt = useLocalStorage(StorageKeys.BUDGETS_LOADED_AT, null) as RemovableRef<DateString | null>;
  const budget = useLocalStorage(StorageKeys.BUDGET, null, { serializer: StorageSerializers.object }) as RemovableRef<BudgetDto | null>;
  const budgetLoadedAt = useLocalStorage(StorageKeys.BUDGET_LOADED_AT, null) as RemovableRef<DateString | null>;
  const budgetSelectedDate = useLocalStorage(StorageKeys.BUDGET_SELECTED_DATE, null) as RemovableRef<DateString | null>;
  const budgetFoldedFolders = useLocalStorage(StorageKeys.BUDGET_FOLDED_FOLDERS, {}) as RemovableRef<IdBooleanMap>;
  const budgetUnfoldedElements = useLocalStorage(StorageKeys.BUDGET_UNFOLDED_ELEMENTS, {}) as RemovableRef<IdBooleanMap>;
  const budgetMeta = useLocalStorage(StorageKeys.BUDGET_META, null, { serializer: StorageSerializers.object }) as RemovableRef<BudgetMetaDto | null>;
  const budgetCurrencies = useLocalStorage(StorageKeys.BUDGET_CURRENCIES, []) as RemovableRef<Id[]>;
  const budgetTransactionListLoading = ref<boolean>(false);
  const budgetTransactionList = ref<BudgetTransactionDto[]>([]);

  const isBudgetTransactionListLoading: ComputedRef<boolean> = computed(() => {
    return budgetTransactionListLoading.value;
  });
  const isBudgetLoading: ComputedRef<boolean> = computed(() => {
    return !budgetsLoadedAt.value && !budget.value;
  });
  const isBudgetsLoaded: ComputedRef<boolean> = computed(() => !!budgetsLoadedAt.value);
  const ownBudgets = computed(() => {
    const userStore = useUsersStore();
    return _.orderBy(_.filter(_.cloneDeep(budgets.value), { ownerUserId: userStore.userId }), 'position', 'asc');
  });
  const budgetsOrdered = computed(() => _.orderBy(budgets.value, 'name', 'asc'));
  const isBudgetLoaded: ComputedRef<boolean> = computed(() => !!budgetLoadedAt.value);
  const budgetsNotAccepted = computed(() => {
    const result: BudgetMetaDto[] = [];
    const userStore = useUsersStore();
    budgets.value.forEach(item => {
      item.access.forEach((sharedAccess) => {
        console.log(sharedAccess, userStore.userId, sharedAccess.isAccepted);
        if (sharedAccess.user.id === userStore.userId && !sharedAccess.isAccepted) {
          result.push(_.cloneDeep(item));
        }
      });
    });
    return result;
  });
  const isBudgetAvailable = computed(() => {
    if (!budgets.value) {
      return false;
    }
    let result = false;
    const userStore = useUsersStore();
    budgets.value.forEach(item => {
      item.access.forEach((sharedAccess: any) => {
        if (sharedAccess.user.id === userStore.userId && !!sharedAccess.isAccepted) {
          result = true;
        }
      });
    });
    return result;
  });
  const canUserConfigureBudget = computed(() => {
    if (!budget.value) {
      return false;
    }
    let result = false;
    const userStore = useUsersStore();
    budget.value.meta.access.forEach((access: UserAccessDto) => {
      if (access.user.id === userStore.userId) {
        if (access.role === AccessRole.ADMIN || access.role === AccessRole.OWNER) {
          result = true;
        }
      }
    });
    return result;
  });
  const canUserUpdateLimits = computed(() => {
    if (!budget.value) {
      return false;
    }
    let result = false;
    const userStore = useUsersStore();
    budget.value.meta.access.forEach((access: UserAccessDto) => {
      if (access.user.id === userStore.userId) {
        if (access.role === AccessRole.USER || access.role === AccessRole.ADMIN || access.role === AccessRole.OWNER) {
          result = true;
        }
      }
    });
    return result;
  });

  const budgetDate: ComputedRef<DateString> = computed((): DateString => {
    if (!budgetSelectedDate.value) {
      const now = new Date();
      now.setDate(1);
      budgetSelectedDate.value = date.formatDate(now, DATE_FORMAT);
    }
    return budgetSelectedDate.value;
  });

  function fetchBudgets() {
    return BudgetAPIv1.getList((response: BudgetListDto) => {
      BUDGETS_INIT(response.items);
    }, (error: any) => {
      console.log('Budgets error', error);
      return error;
    });
  }

  function createBudget(dto: CreateBudgetRequestDto) {
    trackEvent(METRICS.BUDGET_CREATE);
    const userStore = useUsersStore();
    const foundBudget = _.findLast(_.filter(budgets.value, { ownerUserId: userStore.userId }), (item: BudgetMetaDto) => {
      return item.name.toLowerCase() === dto.name.toLowerCase();
    });
    if (foundBudget) {
      return new Promise((resolve, reject) => {
        if (!dto.name) {
          reject('Budget name is empty');
          return;
        }

        if (foundBudget) {
          resolve(foundBudget);
        }
      });
    }

    return BudgetAPIv1.create(dto, (response: CreateBudgetResponseDto) => {
      if (!response.success) {
        return response;
      }
      budgets.value.push(response.data.item.meta);
      budget.value = response.data.item;
      budgetLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
      budgetMeta.value = response.data.item.meta;
      budgetCurrencies.value = [];
      response.data.item.balances.forEach((item: BudgetBalanceDto) => {
        budgetCurrencies.value.push(item.currencyId);
      });

      const userStore = useUsersStore();
      userStore.fetchUserData();
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] createBudget', error);
      return error;
    });
  }

  function updateBudget(dto: UpdateBudgetRequestDto) {
    trackEvent(METRICS.BUDGET_UPDATE);
    const userStore = useUsersStore();
    if (dto.name !== null) {
      const foundBudget = _.findLast(_.filter(budgets.value, { ownerUserId: userStore.userId }), (item: BudgetMetaDto) => {
        return dto.name !== null && item.name.toLowerCase() === dto.name.toLowerCase() && item.id !== dto.id;
      });
      if (foundBudget) {
        return new Promise((_, reject) => {
          reject('Budget name exists');
        });
      }
    }

    return BudgetAPIv1.update(dto, (response: UpdateBudgetResponseDto) => {
      if (!response.success) {
        return response;
      }
      _.remove(budgets.value, (budget) => {
        return budget.id === dto.id;
      });
      budgets.value.push(response.data.item);
      budgetMeta.value = response.data.item;
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] updateBudget', error);
      return error;
    });
  }

  function deleteBudget(budgetId: Id) {
    trackEvent(METRICS.BUDGET_DELETE);
    const userStore = useUsersStore();
    return BudgetAPIv1.delete(budgetId, (response: DeleteBudgetResponseDto) => {
      resetCachedBudget();
      fetchBudgets();
      userStore.fetchUserData();
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] deleteBudget', error);
      return error;
    });
  }

  function grantAccess(request: GrantBudgetAccessRequestDto) {
    trackEvent(METRICS.BUDGET_GRANT_ACCESS);
    return BudgetAPIv1.grantAccess(request, (response: GrantAccessResponseDto) => {
      BUDGETS_INIT(response.data.items);
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] grantAccess', error);
      return error;
    });
  }

  function revokeAccess(request: RevokeBudgetAccessRequestDto) {
    trackEvent(METRICS.BUDGET_REVOKE_ACCESS);
    return BudgetAPIv1.revokeAccess(request, (response: RevokeAccessResponseDto) => {
      fetchBudgets();
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] revokeAccess', error);
      return error;
    });
  }

  function acceptAccess(request: AcceptBudgetAccessRequestDto) {
    trackEvent(METRICS.BUDGET_ACCEPT_ACCESS);
    return BudgetAPIv1.acceptAccess(request, (response: AcceptAccessResponseDto) => {
      fetchBudgets();
      useUsersStore().fetchUserData().then(() => {
        resetCachedBudget();
      });
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] acceptAccess', error);
      return error;
    });
  }

  function declineAccess(request: DeclineBudgetAccessRequestDto) {
    trackEvent(METRICS.BUDGET_DECLINE_ACCESS);
    return BudgetAPIv1.declineAccess(request, (response: DeclineAccessResponseDto) => {
      fetchBudgets();
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] declineAccess', error);
      return error;
    });
  }

  function fetchBudget(dto: GetBudgetRequestDto) {
    if (!budget.value || budget.value.meta.id !== dto.id) {
      budgetFoldedFolders.value = {};
      budgetUnfoldedElements.value = {};
    }
    budgetLoadedAt.value = null;
    budget.value = null;
    return BudgetAPIv1.getItem(dto, (response: GetBudgetResponseDto) => {
      if (response.data.item.structure?.elements) {
        response.data.item.structure.elements = response.data.item.structure.elements.map(element => ({
          ...element,
          spent: Number(element.spent),
          budgetSpent: Number(element.budgetSpent),
          budgeted: Number(element.budgeted),
          available: Number(element.available),
          children: element.children.map(child => ({
            ...child,
            spent: Number(child.spent),
            budgetSpent: Number(child.budgetSpent)
          }))
        }));
      }
      if (response.data.item.balances) {
        response.data.item.balances = response.data.item.balances.map(balance => ({
          ...balance,
          startBalance: balance.startBalance !== null ? Number(balance.startBalance) : null,
          endBalance: balance.endBalance !== null ? Number(balance.endBalance) : null,
          income: balance.income !== null ? Number(balance.income) : null,
          expenses: balance.expenses !== null ? Number(balance.expenses) : null,
          exchanges: balance.exchanges !== null ? Number(balance.exchanges) : null,
          holdings: balance.holdings !== null ? Number(balance.holdings) : null
        }));
      }
      if (response.data.item.currencyRates) {
        response.data.item.currencyRates = response.data.item.currencyRates.map(rate => ({
          ...rate,
          rate: Number(rate.rate)
        }));
      }
      budget.value = response.data.item;
      budgetLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
      budgetMeta.value = response.data.item.meta;
      budgetCurrencies.value = [];
      response.data.item.balances.forEach((item: BudgetBalanceDto) => {
        budgetCurrencies.value.push(item.currencyId);
      });
    }, (error: any) => {
      budgetLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
      budget.value = null;
      budgetMeta.value = null;
      budgetCurrencies.value = [];
      console.error('[Budgets Store] fetchBudget', error);
      return error;
    });
  }

  function fetchUserBudget() {
    const userStore = useUsersStore();
    if (!userStore.userDefaultBudgetId) {
      budgetLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
      budget.value = null;
      return new Promise((resolve, reject) => {
        reject('Budget is not selected');
      });
    }

    return fetchBudget({
      id: userStore.userDefaultBudgetId,
      date: date.formatDate(date.extractDate(budgetDate.value, DATE_FORMAT), DATE_FORMAT)
    });
  }

  function resetCachedBudget() {
    budgetLoadedAt.value = null;
    budget.value = null;
    budgetMeta.value = null;
    budgetCurrencies.value = [];
  }

  function createFolder(dto: CreateBudgetFolderRequestDto) {
    trackEvent(METRICS.BUDGET_FOLDER_CREATE);
    return BudgetAPIv1.createFolder(dto, (response: CreateBudgetFolderResponseDto) => {
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] createFolder', error, dto);
      return error;
    });
  }

  function updateFolder(dto: UpdateBudgetFolderRequestDto) {
    if (!budget.value || !budget.value.structure) {
      throw new Error('Budget or its properties are not available.');
    }

    trackEvent(METRICS.BUDGET_FOLDER_UPDATE);
    const folder = budget.value.structure.folders.find(el => el.id === dto.id);
    if (!folder) {
      throw new Error('Folder not found');
    }
    const oldName = folder.name;
    updateFolderLocally(dto.id, dto.name);
    return BudgetAPIv1.updateFolder(dto, (response: UpdateBudgetFolderResponseDto) => {
      return response;
    }, (error: any) => {
      updateFolderLocally(dto.id, oldName);
      console.error('[Budgets Store] updateFolder', error, dto);
      return error;
    });
  }

  function updateFolderLocally(id: Id, name: string) {
    if (!budget.value || !budget.value.structure) {
      throw new Error('Budget or its properties are not available.');
    }

    budget.value.structure.folders.forEach((folder) => {
      if (folder.id === id) {
        folder.name = name;
      }
    });

    if (isRef(budget)) {
      budget.value = { ...budget.value };
    }
  }

  function deleteFolder(budgetId: Id, folderId: Id) {
    trackEvent(METRICS.BUDGET_FOLDER_DELETE);
    return BudgetAPIv1.deleteFolder(budgetId, folderId, (response: DeleteBudgetFolderResponseDto) => {
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] deleteFolder', error);
      return error;
    });
  }

  function orderFolders(folderId: Id, newPosition: number) {
    if (!budget.value || !budget.value.meta || !budget.value.structure) {
      throw new Error('Budget or its properties are not available.');
    }
    trackEvent(METRICS.BUDGET_FOLDER_CHANGE_ORDER);
    const folder = budget.value.structure.folders.find(folder => folder.id === folderId);
    if (!folder) {
      throw new Error('Folder not found');
    }
    const oldPosition = folder.position;
    const affectedFolders = orderFoldersLocally(folderId, newPosition);
    return BudgetAPIv1.orderFolderList({
      budgetId: budget.value.meta.id,
      items: affectedFolders
    }, (response: OrderFolderListResponseDto) => {
      if (!response.success) {
        orderFoldersLocally(folderId, oldPosition);
      }
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] orderFolders', error, folderId, newPosition);
      orderFoldersLocally(folderId, oldPosition);
      return error;
    });
  }

  function orderFoldersLocally(folderId: Id, newPosition: number): AffectedFolderRequestDto[] {
    if (!budget.value || !budget.value.structure) {
      throw new Error('Budget or its properties are not available.');
    }

    const folders = budget.value.structure.folders;
    const oldPosition = folders.findIndex(folder => folder.id === folderId);

    if (oldPosition === -1 || newPosition < 0 || newPosition >= folders.length) {
      return []; // Return an empty list if invalid operation
    }

    // Remove the folder from the old position
    const [movedFolder] = folders.splice(oldPosition, 1);

    // Insert the folder in the new position
    folders.splice(newPosition, 0, movedFolder);

    // Collect folders with changed positions
    const changedFolders: AffectedFolderRequestDto[] = [];

    // Recalculate positions and collect changes
    folders.forEach((folder, index) => {
      if (folder.position !== index) {
        changedFolders.push({ id: folder.id, position: index });
      }
      folder.position = index;
    });

    // Ensure reactivity if budget is a reactive reference
    if (isRef(budget)) {
      budget.value = { ...budget.value };
    }

    return changedFolders;
  }

  function updateBudgetDate(dateObject: any) {
    trackEvent(METRICS.BUDGET_CHANGE_DATE);
    BUDGET_DATE(date.formatDate(dateObject, 'YYYY-MM-01 00:00:00'));
  }

  function setLimit(request: SetElementLimitRequestDto) {
    trackEvent(METRICS.BUDGET_UPDATE_ELEMENT_LIMIT);
    if (!budget.value?.structure?.elements) {
      throw new Error('Budget structure or elements are not defined');
    }
    const element = budget.value?.structure?.elements.find(el => el.id === request.elementId);
    if (!element) {
      throw new Error('Budget element is missing');
    }
    const oldElement = _.cloneDeep(element);
    element.budgeted = request.amount === null ? 0 : Number(request.amount);
    return BudgetAPIv1.setLimit(request, (response: SetLimitResponseDto) => {
      if (!response.success) {
        element.budgeted = oldElement.budgeted;
      }
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] setLimit', error, request);
      element.budgeted = oldElement.budgeted;
      return error;
    });
  }

  function transferEnvelopeBudget(params: any) {
    trackEvent(METRICS.BUDGET_TRANSFER_ENVELOPE_BUDGET);
    // BUDGET_TRANSFER_ENVELOPE_BUDGET({
    //   fromEnvelopeId: params.fromEnvelopeId,
    //   toEnvelopeId: params.toEnvelopeId,
    //   budget: params.amount,
    //   period: params.period
    // });
    // return BudgetAPIv1.transferEnvelopeBudget({
    //   fromEnvelopeId: params.fromEnvelopeId,
    //   toEnvelopeId: params.toEnvelopeId,
    //   amount: params.amount,
    //   period: params.period
    // }, (response: any) => {
    //   if (!!response.data) {
    //     return true;
    //   }
    //   BUDGET_TRANSFER_ENVELOPE_BUDGET({
    //     fromEnvelopeId: params.toEnvelopeId,
    //     toEnvelopeId: params.fromEnvelopeId,
    //     budget: params.amount,
    //     period: params.period
    //   });
    //   return false;
    // }, (error: any) => {
    //   console.log('Budgets error', error);
    //   BUDGET_TRANSFER_ENVELOPE_BUDGET({
    //     fromEnvelopeId: params.toEnvelopeId,
    //     toEnvelopeId: params.fromEnvelopeId,
    //     budget: params.amount,
    //     period: params.period
    //   });
    //   return error;
    // });
  }

  function createEnvelope(request: CreateBudgetEnvelopeRequestDto) {
    trackEvent(METRICS.BUDGET_ENVELOPE_CREATE);
    budget.value?.structure.elements.push({
      id: request.id,
      type: BudgetElementType.ENVELOPE,
      name: request.name,
      icon: request.icon,
      isArchived: 0,
      spent: 0,
      budgetSpent: 0,
      ownerUserId: useUsersStore().userId as Id,
      currencyId: request.currencyId,
      folderId: request.folderId,
      position: -1,
      budgeted: 0,
      available: 0,
      children: []
    });
    moveElementLocally(request.id, 0, request.folderId);
    return BudgetAPIv1.createEnvelope(request, (response: CreateEnvelopeResponseDto) => {
      if (!response.success) {
        deleteElementLocally(request.id);
      }
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] createEnvelope', error, request);
      deleteElementLocally(request.id);
      return error;
    });
  }

  function updateEnvelope(request: UpdateBudgetEnvelopeRequestDto) {
    trackEvent(METRICS.BUDGET_ENVELOPE_UPDATE);
    if (!budget.value?.structure?.elements) {
      throw new Error('Budget structure or elements are not defined');
    }
    const element = budget.value?.structure?.elements.find(el => el.id === request.id);
    if (!element) {
      throw new Error('Budget element is missing');
    }
    const oldElement = _.cloneDeep(element);
    _.merge(element, request);
    return BudgetAPIv1.updateEnvelope(request, (response: UpdateEnvelopeResponseDto) => {
      if (!response.success) {
        _.merge(element, oldElement);
      }
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] updateEnvelope', error, request);
      _.merge(element, oldElement);
      return error;
    });
  }

  function deleteEnvelope(request: DeleteBudgetEnvelopeRequestDto) {
    trackEvent(METRICS.BUDGET_ENVELOPE_DELETE);
    return BudgetAPIv1.deleteEnvelope(request, (response: DeleteEnvelopeResponseDto) => {
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] deleteEnvelope', error, request);
      return error;
    });
  }

  function changeElementCurrency(request: ChangeCurrencyRequestDto) {
    trackEvent(METRICS.BUDGET_ELEMENT_CHANGE_CURRENCY);
    return BudgetAPIv1.changeElementCurrency(request, (response: ChangeCurrencyResponseDto) => {
      if (response.success) {
        if (budget.value?.structure?.elements) {
          const element = budget.value?.structure?.elements.find(el => el.id === request.elementId);
          if (element) {
            element.currencyId = request.currencyId;
          }
        }
      }
      return response;
    }, (error: any) => {
      console.error('[Budgets Store] changeElementCurrency', error, request);
      return error;
    });
  }

  function copyEnvelopesBudget(params: any) {
    trackEvent(METRICS.BUDGET_ENVELOPE_COPY_BUDGET);
    // const planId = params.planId;
    // return BudgetAPIv1.copyEnvelopesBudget(params, (response: any) => {
    //   if (!!response.data) {
    //     BUDGET_FOLDER_DELETE(params.id);
    //     fetchBudget({ id: planId });
    //     return true;
    //   }
    // }, (error: any) => {
    //   console.log('Budgets error', error);
    //   return error;
    // });
  }

  function resetBudget(params: any) {
    trackEvent(METRICS.BUDGET_RESET);
    // const planId = params.id;
    // return BudgetAPIv1.resetBudget(params, (response: any) => {
    //   if (!!response.data) {
    //     BUDGET_FOLDER_DELETE(params.id);
    //     fetchBudget({ id: planId });
    //     return true;
    //   }
    // }, (error: any) => {
    //   console.log('Budgets error', error);
    //   return error;
    // });
  }

  function BUDGETS_INIT(items: BudgetMetaDto[]) {
    budgets.value = items;
    budgetsLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
  }

  function BUDGET_DELETE(id: string) {
    const copyBudgets: any = _.cloneDeep(budgets.value);
    _.remove(copyBudgets, (item: any) => {
      return item.id === id;
    });
    budgets.value = copyBudgets;
  }

  function BUDGET_DATE(date: any) {
    budgetSelectedDate.value = date;
  }

  function generateId(): Id {
    return uuid();
  }

  function foldFolder(id: Id) {
    budgetFoldedFolders.value[id] = true;
  }

  function unfoldFolder(id: Id) {
    delete budgetFoldedFolders.value[id];
  }

  function unfoldElement(id: Id) {
    budgetUnfoldedElements.value[id] = true;
  }

  function foldElement(id: Id) {
    delete budgetUnfoldedElements.value[id];
  }

  function changeBudgetPeriod(period: DateString | Date) {
    let extracted = period;
    if (typeof period === 'string') {
      if (/^\d{4}-\d{2}-\d{2}$/.test(period)) {
        extracted = date.extractDate(period, 'YYYY-MM-DD');
      } else {
        extracted = date.extractDate(period, 'YYYY-MM-DD HH:mm:ss');
      }
    }
    budgetSelectedDate.value = date.formatDate(extracted, DATE_FORMAT);
  }

  function fetchTransactionList(dto: BudgetTransactionListRequestDto) {
    budgetTransactionListLoading.value = true;
    budgetTransactionList.value = [];
    return BudgetAPIv1.getTransactionList(dto, (response: GetTransactionListResponseDto) => {
      budgetTransactionListLoading.value = false;
      budgetTransactionList.value = response.data.items;
    }, (error: any) => {
      budgetTransactionListLoading.value = false;
      budgetTransactionList.value = [];
      console.error('[Budgets Store] fetchTransactionList', error);
      return error;
    });
  }

  function moveElement(elementId: Id, position: number, targetFolderId: Id | null) {
    trackEvent(METRICS.BUDGET_CHANGE_ORDER_ELEMENT);
    if (!budget.value || !budget.value.meta || !budget.value.meta.id) {
      throw new Error('Budget or its properties are not available.');
    }
    const result = moveElementLocally(elementId, position, targetFolderId);
    const affectedElements = result.elements.map(element => ({
      id: element.id,
      folderId: element.folderId,
      position: element.position
    }));
    return BudgetAPIv1.moveElementList({
      budgetId: budget.value.meta.id,
      items: affectedElements
    }, (response: MoveElementResponseDto) => {
      if (!response.success) {
        moveElementLocally(result.element.id, result.element.origin.position, result.element.origin.folderId);
      }
      return response;
    }, (error: any) => {
      moveElementLocally(result.element.id, result.element.origin.position, result.element.origin.folderId);
      console.error('[Budgets Store] moveElement', error);
      return error;
    });
  }

  function moveElementLocally(elementId: Id, targetPosition: number, targetFolderId: Id | null): MoveElementLocallyResponseDto {
    if (!budget.value?.structure?.elements) {
      throw new Error('Budget structure or elements are not defined');
    }

    const archivedElements: BudgetElementDto[] = _.cloneDeep(budget.value.structure.elements).filter((item: BudgetElementDto) => item.isArchived === 1);
    const elements: BudgetElementDto[] = _.cloneDeep(budget.value.structure.elements).filter((item: BudgetElementDto) => item.isArchived !== 1);
    const affectedElements: BudgetElementDto[] = [];

    // Find the element to move
    const elementToMoveIndex = elements.findIndex(el => el.id === elementId);
    if (elementToMoveIndex === -1) {
      throw new Error('Element not found');
    }
    const elementToMove = elements[elementToMoveIndex];
    const originFolderId = elementToMove.folderId;
    const originPosition = elementToMove.position;

    const folderElements = elements.filter(el => el.folderId === targetFolderId);
    if (folderElements.length > 0) {
      // Special handling for new elements (position -1)
      if (originPosition === -1) {
        folderElements.forEach((element) => {
          if (element.position >= targetPosition) {
            element.position += 1;
          }
        });
      } else {
        folderElements.forEach((element) => {
          if (originPosition < targetPosition) {
            if (element.position > originPosition && element.position <= targetPosition) {
              element.position -= 1;
            }
          } else {
            if (element.position >= targetPosition && element.position < originPosition) {
              element.position += 1;
            }
          }
        });
      }
    }
    elementToMove.folderId = targetFolderId;
    elementToMove.position = targetPosition;
    reorderFolderLocally(elements, targetFolderId);
    reorderFolderLocally(elements, originFolderId);

    elements.forEach((item) => {
      if (!budget.value) {
        return;
      }
      const originalElement = budget.value.structure.elements.find(el => el.id === item.id);
      if (originalElement && (originalElement.position !== item.position || originalElement.folderId !== item.folderId)) {
        affectedElements.push(item);
      }
    });
    affectedElements.sort((a, b) => a.position - b.position);

    budget.value.structure.elements = [...elements, ...archivedElements];

    return {
      element: {
        id: elementId,
        origin: {
          position: originPosition,
          folderId: originFolderId
        },
        new: {
          position: targetPosition,
          folderId: targetFolderId
        }
      },
      elements: affectedElements
    };
  }

  function reorderFolderLocally(elements: BudgetElementDto[], folderId: Id | null) {
    const folderElements = elements.filter(el => el.folderId === folderId);
    if (folderElements.length === 0) {
      return;
    }

    // Sort by their position first
    folderElements.sort((a, b) => a.position - b.position);

    // Reassign positions to ensure there are no gaps
    for (let index = 0; index < folderElements.length; index++) {
      const element = folderElements[index];
      if (element.position !== index) {
        element.position = index;
      }
    }

    return elements;
  }

  function deleteElementLocally(elementId: Id) {
    if (!budget.value?.structure?.elements) {
      throw new Error('Budget structure or elements are not defined');
    }

    const elements: BudgetElementDto[] = _.cloneDeep(budget.value.structure.elements);

    const element = elements.find(el => el.id === elementId);
    if (!element) {
      return;
    }

    const oldFolderId = element.folderId;
    const elementPosition = elements.findIndex(el => el.id === elementId);
    if (elementPosition === -1) {
      return;
    }
    elements.splice(elementPosition, 1);
    reorderFolderLocally(elements, oldFolderId);
    budget.value.structure.elements = elements;
  }

  return {
    budgets,
    budgetsLoadedAt,
    budget,
    budgetMeta,
    budgetCurrencies,
    budgetLoadedAt,
    budgetSelectedDate,
    isBudgetsLoaded,
    isBudgetLoaded,
    isBudgetLoading,
    ownBudgets,
    budgetsOrdered,
    budgetsNotAccepted,
    isBudgetAvailable,
    budgetDate,
    budgetFoldedFolders,
    budgetUnfoldedElements,
    budgetTransactionListLoading,
    budgetTransactionList,
    isBudgetTransactionListLoading,
    canUserConfigureBudget,
    canUserUpdateLimits,
    fetchBudgets,
    createBudget,
    updateBudget,
    deleteBudget,
    grantAccess,
    revokeAccess,
    acceptAccess,
    declineAccess,
    fetchBudget,
    fetchUserBudget,
    resetCachedBudget,
    createFolder,
    updateFolder,
    deleteFolder,
    generateId,
    foldFolder,
    unfoldFolder,
    unfoldElement,
    foldElement,
    changeBudgetPeriod,
    fetchTransactionList,
    moveElement,
    orderFolders,
    createEnvelope,
    updateEnvelope,
    deleteEnvelope,
    setLimit,
    changeElementCurrency
  };
});
