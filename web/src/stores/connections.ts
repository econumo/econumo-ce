import { defineStore } from 'pinia';
import ConnectionAPIv1 from '../modules/api/v1/connection';
import { METRICS, trackEvent } from '../modules/metrics';
import _ from 'lodash';
import { date } from 'quasar';
import {useAccountsStore} from './accounts';
import { ConnectionDto } from 'modules/api/v1/dto/connection.dto';
import { useLocalStorage } from '@vueuse/core';
import { RemovableRef } from '@vueuse/shared';
import { computed } from 'vue';
import { DateString, Id } from '../modules/types';
import { DATE_TIME_FORMAT } from '../modules/constants';
import { StorageKeys } from '../modules/storage';
import { useBudgetsStore } from './budgets';
import { useSyncStore } from './sync';
import { BudgetMetaDto } from 'modules/api/v1/dto/budget.dto';
import { AccountDto } from 'modules/api/v1/dto/account.dto';
import { useUsersStore } from './users';

export interface User {
  id: Id;
  name: string;
  avatar: string;
}

export interface SharedAccount {
  id: Id;
  name: string;
  icon: string;
  user: User;
  role: string;
}

export interface SharedBudget {
  id: Id;
  name: string;
  user: User;
  role: string;
}

export interface Connection {
  user: User;
  sharedAccounts: SharedAccount[];
  sharedBudgets: SharedBudget[];
}

export const useConnectionsStore = defineStore('connections', () => {
  const connections = useLocalStorage(StorageKeys.CONNECTIONS, []) as RemovableRef<ConnectionDto[]>;
  const connectionsLoadedAt = useLocalStorage(StorageKeys.CONNECTIONS_LOADED_AT, null) as RemovableRef<DateString | null>;
  const isConnectionsLoaded = computed(() => !!connectionsLoadedAt.value);

  async function fetchConnections() {
    return ConnectionAPIv1.getConnectionList({}, (response: any) => {
      CONNECTIONS_INIT(response.data.items);
    }, (error = {}) => {
      return error
    })
  }

  async function generateConnectionInvite() {
    trackEvent(METRICS.CONNECTION_GENERATE_INVITE);
    return ConnectionAPIv1.generateInvite((response: any) => {
      return response.data.item || false;
    }, (error = {}) => {
      return error
    })
  }

  async function deleteConnectionInvite() {
    trackEvent(METRICS.CONNECTION_DELETE_INVITE);
    return ConnectionAPIv1.deleteInvite((response: any) => {
      return !!response.data;
    }, (error = {}) => {
      return error
    })
  }

  async function acceptConnectionInvite(code: string) {
    trackEvent(METRICS.CONNECTION_ACCEPT_INVITE);
    return ConnectionAPIv1.acceptInvite(code, (response: any) => {
      CONNECTIONS_INIT(response.data.items);
      return !!response.data;
    }, (error = {}) => {
      return error
    })
  }

  async function deleteConnection(userId: Id) {
    trackEvent(METRICS.CONNECTION_DELETE);
    return ConnectionAPIv1.deleteConnection(userId, (response: any) => {
      CONNECTION_DELETE(userId);
      return !!response.data;
    }, (error = {}) => {
      return error
    })
  }

  async function setAccountAccess(params: any) {
    trackEvent(METRICS.CONNECTION_UPDATE_ACCOUNT_ACCESS);
    const accountsStore = useAccountsStore();
    return ConnectionAPIv1.setAccountAccess(params, (response: any) => {
      accountsStore.accessUpdate({
        request: params,
        connections: connections.value
      });
      return !!response.data;
    }, (error = {}) => {
      return error
    })
  }

  async function revokeAccountAccess(params: any) {
    trackEvent(METRICS.CONNECTION_REVOKE_ACCOUNT_ACCESS);
    const accountsStore = useAccountsStore();
    return ConnectionAPIv1.revokeAccountAccess(params, (response: any) => {
      accountsStore.accessDelete(params);
      return !!response.data;
    }, (error = {}) => {
      return error
    })
  }

  function CONNECTIONS_INIT(items: ConnectionDto[]) {
    connections.value = items;
    connectionsLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
  }

  function CONNECTION_DELETE(id: Id) {
    _.remove(connections.value, (item) => {
      return item.user.id === id;
    });
    const syncStore = useSyncStore();
    syncStore.fetchAll();
  }


  function getSharedBudgets(connectedUserId: Id): SharedBudget[] {
    const usersStore = useUsersStore();
    const currentUserId = usersStore.userId;
    const budgetsStore = useBudgetsStore();
    const budgets: SharedBudget[] = [];
    _.forEach(budgetsStore.budgets, (budget: BudgetMetaDto) => {
      let access = budget.access.find(access => access.user.id === connectedUserId);
      if (!access) {
        return;
      }
      if (access.role !== 'owner') {
        budgets.push({
          id: budget.id,
          name: budget.name,
          user: {
            id: access.user.id,
            name: access.user.name,
            avatar: access.user.avatar
          },
          role: access.role
        });
        return;
      }
      access = budget.access.find(access => access.user.id === currentUserId);
      if (!access) {
        return;
      }

      budgets.push({
        id: budget.id,
        name: budget.name,
        user: {
          id: access.user.id,
          name: access.user.name,
          avatar: access.user.avatar
        },
        role: access.role
      });
    });
    return budgets;
  }

  function getSharedAccounts(connectedUserId: Id): SharedAccount[] {
    const usersStore = useUsersStore();
    const currentUserId = usersStore.userId;
    const accountsStore = useAccountsStore();
    const accounts: SharedAccount[] = [];
    _.forEach(accountsStore.accounts, (account: AccountDto) => {
      let access = account.sharedAccess.find(access => access.user.id === connectedUserId);
      if (!access) {
        access = account.sharedAccess.find(access => access.user.id === currentUserId);
        if (!access) {
          return;
        }
        accounts.push({
          id: account.id,
          name: account.name,
          icon: account.icon,
          user: {
            id: access.user.id,
            name: access.user.name,
            avatar: access.user.avatar
          },
          role: access.role
        });
        return;
      }

      accounts.push({
        id: account.id,
        name: account.name,
        icon: account.icon,
        user: {
          id: access.user.id,
          name: access.user.name,
          avatar: access.user.avatar
        },
        role: access.role
      });
    });
    return accounts;
  }

  return {
    connections,
    connectionsLoadedAt,
    isConnectionsLoaded,
    fetchConnections,
    generateConnectionInvite,
    deleteConnectionInvite,
    acceptConnectionInvite,
    deleteConnection,
    setAccountAccess,
    revokeAccountAccess,
    getSharedBudgets,
    getSharedAccounts
  }
});
