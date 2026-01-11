import { defineStore } from 'pinia';
import { date } from 'quasar';
import PayeeAPIv1 from '../modules/api/v1/payee';
import { getChangedPositions } from '../modules/helpers';
import { v4 as uuid } from 'uuid';
import _ from 'lodash';
import { METRICS, trackEvent } from '../modules/metrics';
import {useUsersStore} from './users';
import {useTransactionsStore} from './transactions';
import { useLocalStorage } from '@vueuse/core';
import { StorageKeys } from '../modules/storage';
import { RemovableRef } from '@vueuse/shared';
import { CreatePayeeDto, PayeeDto, PayeeItemDto, PayeeListDto, UpdatePayeeDto } from '../modules/api/v1/dto/payee.dto';
import { DateString, Id, BooleanType } from '../modules/types';
import { computed } from 'vue';
import { DATE_TIME_FORMAT } from '../modules/constants';

interface ChangePayeeSortModeForm {
  type: string,
  direction: 'asc' | 'desc'
}

export const usePayeesStore = defineStore('payees', () => {
  const payees = useLocalStorage(StorageKeys.PAYEES, []) as RemovableRef<PayeeDto[]>;
  const payeesLoadedAt = useLocalStorage(StorageKeys.PAYEES_LOADED_AT, null) as RemovableRef<DateString | null>;

  const isPayeesLoaded = computed(() => !!payeesLoadedAt.value);
  const ownPayees = computed(() => {
    const userStore = useUsersStore();
    return _.orderBy(_.filter(_.cloneDeep(payees.value), { ownerUserId: userStore.userId }), 'position', 'asc') as PayeeDto[];
  });
  const payeesOrdered = computed(() => {
    return _.orderBy(_.filter(_.cloneDeep(payees.value), { isArchived: BooleanType.FALSE }), 'position', 'asc') as PayeeDto[];
  });
  const payeesArchived = computed(() => {
    return _.orderBy(_.filter(_.cloneDeep(payees.value), { isArchived: BooleanType.TRUE }), 'updatedAt', 'desc') as PayeeDto[];
  });

  function fetchPayees() {
    return PayeeAPIv1.getList((response: PayeeListDto) => {
      PAYEES_INIT(response.items);
    }, (error: any) => {
      console.log('Payees error', error);
      return error
    });
  }

  function createPayee(params: CreatePayeeDto) {
    trackEvent(METRICS.PAYEE_CREATE);
    params.id = uuid();
    const userStore = useUsersStore();
    const foundPayee = _.findLast(_.filter(payees.value, {ownerUserId: userStore.userId}), (item: PayeeDto) => {
      return item.name.toLowerCase() === params.name.toLowerCase();
    })
    if (foundPayee) {
      return new Promise((resolve, reject) => {
        if (!params.name) {
          reject('Payee name is empty');
          return
        }

        if (foundPayee) {
          resolve(foundPayee);
        }
      });
    }

    return PayeeAPIv1.create(params, (response: PayeeItemDto) => {
      PAYEE_CREATE(response.item);
      return response.item
    }, (error: any) => {
      console.log('Payees error', error);
      return error
    })
  }

  function updatePayee(params: UpdatePayeeDto) {
    trackEvent(METRICS.PAYEE_UPDATE);
    const userStore = useUsersStore();
    const foundPayee = _.findLast(_.filter(payees.value, {ownerUserId: userStore.userId}), (item: PayeeDto) => {
      return item.name.toLowerCase() === params.name.toLowerCase() && item.id !== params.id;
    })
    if (foundPayee) {
      return new Promise((_, reject) => {
        reject('Payee name exists');
      });
    }

    return PayeeAPIv1.update(params, () => {
      PAYEE_UPDATE(params);
      return params
    }, (error: any) => {
      console.log('Payees error', error);
      return error
    })
  }

  function orderPayeeList(payeeIds: Id[]) {
    trackEvent(METRICS.PAYEE_ORDER_LIST);
    const changes = getChangedPositions(payees.value, payeeIds);
    if (!changes.length) {
      return new Promise((resolve, reject) => {
        reject('No changes');
      });
    }
    return PayeeAPIv1.orderList(changes, (response: PayeeListDto) => {
      PAYEES_INIT(response.items);
      return response.items
    }, (error: any) => {
      console.log('Payees error', error);
      return error
    })
  }

  function deletePayee(payeeId: Id) {
    trackEvent(METRICS.PAYEE_DELETE);
    const transactionsStore = useTransactionsStore();
    return PayeeAPIv1.delete(payeeId, (response: any) => {
      if (!!response.data) {
        PAYEE_DELETE(payeeId);
        transactionsStore.TRANSACTIONS_PAYEE_DELETE(payeeId);
        return true
      }
      return false
    }, (error: any) => {
      console.log('Payees error', error);
      return error
    })
  }

  function changePayeesSortMode(options: ChangePayeeSortModeForm) {
    trackEvent(METRICS.PAYEE_CHANGE_ORDER, options);
    let orderedPayees = _.cloneDeep(ownPayees.value);
    const usageCounts: { [key: string]: number } = {};
    if (options.type === 'name') {
      orderedPayees = _.orderBy(orderedPayees, ['name'], [options.direction]);
    } else if (options.type === 'count') {
      const transactionsStore = useTransactionsStore();
      orderedPayees.forEach((item) => {
        usageCounts[item.id] = _.filter(transactionsStore.transactions, {payeeId: item.id}).length;
      })
      orderedPayees = _.orderBy(orderedPayees, item => usageCounts[item.id], [options.direction]);
    }
    const payeeIds: any[] = [];
    orderedPayees.forEach((item: any) => {
      payeeIds.push(item.id)
    })
    return orderPayeeList(payeeIds);
  }

  function archivePayee(payeeId: Id) {
    trackEvent(METRICS.PAYEE_ARCHIVE);
    return PayeeAPIv1.archive(payeeId, (response: any) => {
      if (!!response.data) {
        PAYEE_UPDATE_ARCHIVE({id: payeeId, isArchived: BooleanType.TRUE});
        return true
      }
      return false
    }, (error: any) => {
      console.log('Payees error', error);
      return error
    })
  }

  function unarchivePayee(payeeId: Id) {
    trackEvent(METRICS.PAYEE_UNARCHIVE);
    return PayeeAPIv1.unarchive(payeeId, (response: any) => {
      if (!!response.data) {
        PAYEE_UPDATE_ARCHIVE({id: payeeId, isArchived: BooleanType.FALSE});
        return true
      }
      return false
    }, (error: any) => {
      console.log('Payees error', error);
      return error
    })
  }

  function PAYEES_INIT(items: PayeeDto[]) {
    payees.value = items;
    payeesLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
  }

  function PAYEE_CREATE(options: PayeeDto) {
    payees.value.push(options);
  }

  function PAYEE_UPDATE(options: UpdatePayeeDto) {
    const copyPayees: any = _.cloneDeep(payees.value);
    let payee = _.cloneDeep(_.find(copyPayees, {id: options.id}));
    payee = _.extend(payee, options)
    _.remove(copyPayees, (item: any) => {
      return item.id === options.id;
    });
    copyPayees.push(payee);
    payees.value = copyPayees;
  }

  function PAYEE_DELETE(id: Id) {
    const copyPayees: any = _.cloneDeep(payees.value);
    _.remove(copyPayees, (item: any) => {
      return item.id === id;
    });
    payees.value = copyPayees;
  }

  function PAYEE_UPDATE_ARCHIVE(options: {id: Id, isArchived: BooleanType}) {
    const payee: any = _.cloneDeep(_.find(payees.value, {id: options.id}));
    payee.isArchived = options.isArchived;
    const copyPayees: any = _.cloneDeep(payees.value);
    _.remove(copyPayees, (item: any) => {
      return item.id === options.id;
    });
    copyPayees.push(payee);
    payees.value = copyPayees;
  }


  return {
    payees,
    payeesLoadedAt,
    isPayeesLoaded,
    ownPayees,
    payeesOrdered,
    payeesArchived,
    fetchPayees,
    createPayee,
    updatePayee,
    orderPayeeList,
    deletePayee,
    changePayeesSortMode,
    archivePayee,
    unarchivePayee
  }
});
