import { defineStore } from 'pinia';
import AccountFoldersAPIv1 from '../modules/api/v1/account-folder';
import { getChangedPositions } from '../modules/helpers';
import { METRICS, trackEvent } from '../modules/metrics';
import _ from 'lodash';
import { date } from 'quasar';
import { useAccountsStore } from './accounts';
import {
  AccountFolderDto, AccountFolderItemDto, AccountFolderListDto,
  AccountFolderVisibility, CreateAccountFolderDto, UpdateAccountFolderDto
} from '../modules/api/v1/dto/account-folder.dto';
import { useLocalStorage } from '@vueuse/core';
import { RemovableRef } from '@vueuse/shared';
import { computed, ComputedRef } from 'vue';
import { DateString, Id } from '../modules/types';
import { DATE_TIME_FORMAT } from '../modules/constants';
import { StorageKeys } from '../modules/storage';

export const useAccountFoldersStore = defineStore('account-folders', () => {
  const accountFolders = useLocalStorage(StorageKeys.ACCOUNT_FOLDERS, []) as RemovableRef<AccountFolderDto[]>;
  const accountFoldersLoadedAt = useLocalStorage(StorageKeys.ACCOUNT_FOLDERS_LOADED_AT, null) as RemovableRef<DateString | null>;
  const accountFoldersOpened = useLocalStorage(StorageKeys.ACCOUNT_FOLDERS_OPENED, {}) as RemovableRef<Record<Id, boolean>>;

  const isAccountFoldersLoaded: ComputedRef<boolean> = computed(() => !!accountFoldersLoadedAt.value);
  const accountFoldersOrdered = computed(() => {
    const folders = _.orderBy(_.cloneDeep(accountFolders.value), 'position', 'asc');
    folders.forEach((value: any) => {
      value.opened = accountFoldersOpened.value[value.id] ?? true;
    });
    return folders;
  });

  async function fetchAccountFolders() {
    return AccountFoldersAPIv1.getList((response: AccountFolderListDto) => {
      ACCOUNT_FOLDERS_INIT(response.items);
      return response.items;
    }, (error: any) => {
      console.log('AccountFolders error', error);
      return error;
    });
  }

  function openAccountFolder(id: Id) {
    trackEvent(METRICS.ACCOUNT_FOLDER_EXPAND);
    accountFoldersOpened.value[id] = true;
  }

  function closeAccountFolder(id: Id) {
    trackEvent(METRICS.ACCOUNT_FOLDER_COLLAPSE);
    accountFoldersOpened.value[id] = false;
  }

  function updateAccountFolder(data: UpdateAccountFolderDto) {
    trackEvent(METRICS.ACCOUNT_FOLDER_UPDATE);
    const foundFolder = _.findLast(accountFolders.value, (item) => {
      return item.name.toLowerCase() === data.name.toLowerCase() && item.id !== data.id;
    });
    if (foundFolder) {
      throw new Error('Folder already exists');
    }

    return AccountFoldersAPIv1.update(data, (response: AccountFolderItemDto) => {
      ACCOUNT_FOLDER_UPDATE(response.item);
      return response.item;
    }, (error: any) => {
      console.log('AccountFolders error', error);
      return error;
    });
  }

  function createAccountFolder(data: CreateAccountFolderDto) {
    trackEvent(METRICS.ACCOUNT_FOLDER_CREATE);
    const foundFolder = _.findLast(accountFolders.value, (item: any) => {
      return item.name.toLowerCase() === data.name.toLowerCase();
    });
    if (foundFolder) {
      throw new Error('Folder already exists');
    }

    return AccountFoldersAPIv1.create(data, (response: AccountFolderItemDto) => {
      ACCOUNT_FOLDER_CREATE(response.item);
      return response.item;
    }, (error: any) => {
      console.log('AccountFolders error', error);
      return error;
    });
  }

  function replaceAccountFolder(params: { id: Id, replaceId: Id }) {
    trackEvent(METRICS.ACCOUNT_FOLDER_REPLACE);
    const accountsStore = useAccountsStore();
    return AccountFoldersAPIv1.replace(params.id, params.replaceId, (response: any) => {
      if (!!response.data) {
        ACCOUNT_FOLDER_DELETE(params.id);
        accountsStore.replaceFolder(params);
        return true;
      }
      return false;
    }, (error: any) => {
      console.log('AccountFolders error', error);
      return error;
    });
  }

  function orderAccountFolderList(folderIds: Id[]) {
    trackEvent(METRICS.ACCOUNT_FOLDER_ORDER_LIST);
    const changes = getChangedPositions(accountFolders.value, folderIds);
    if (!changes.length) {
      throw new Error('No changes');
    }
    return AccountFoldersAPIv1.orderList(changes, (response: AccountFolderListDto) => {
      ACCOUNT_FOLDERS_INIT(response.items);
      return response.items;
    }, (error: any) => {
      console.log('AccountFolders error', error);
      return error;
    });
  }

  function hideAccountFolder(params: { id: Id }) {
    trackEvent(METRICS.ACCOUNT_FOLDER_HIDE);
    return AccountFoldersAPIv1.hide(params.id, () => {
      ACCOUNT_FOLDER_HIDE(params.id);
      return true;
    }, (error: any) => {
      console.log('AccountFolders error', error);
      return error;
    });
  }

  function showAccountFolder(params: { id: string }) {
    trackEvent(METRICS.ACCOUNT_FOLDER_SHOW);
    return AccountFoldersAPIv1.show(params.id, () => {
      ACCOUNT_FOLDER_SHOW(params.id);
      return true;
    }, (error: any) => {
      console.log('AccountFolders error', error);
      return error;
    });
  }

  function ACCOUNT_FOLDERS_INIT(items: AccountFolderDto[]) {
    accountFolders.value = items;
    accountFoldersLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
    const accountFoldersOpenedCopy: any = {};
    accountFolders.value.forEach((value) => {
      if (!_.isBoolean(accountFoldersOpened.value[value.id])) {
        accountFoldersOpenedCopy[value.id] = true;
      } else {
        accountFoldersOpenedCopy[value.id] = accountFoldersOpened.value[value.id];
      }
    });
    accountFoldersOpened.value = accountFoldersOpenedCopy;
  }

  function ACCOUNT_FOLDER_CREATE(folder: AccountFolderDto) {
    accountFolders.value.push(folder);
  }

  function ACCOUNT_FOLDER_UPDATE(options: any) {
    const copyFolders = _.cloneDeep(accountFolders.value);
    _.remove(copyFolders, (item) => {
      return item.id === options.id;
    });
    copyFolders.push(options);
    accountFolders.value = copyFolders;
  }

  function ACCOUNT_FOLDER_DELETE(id: Id) {
    const copyFolders = _.cloneDeep(accountFolders.value);
    _.remove(copyFolders, (item) => {
      return item.id === id;
    });
    accountFolders.value = copyFolders;
  }

  function ACCOUNT_FOLDER_HIDE(id: string) {
    const copyFolders = _.cloneDeep(accountFolders.value);
    _.remove(copyFolders, (item) => {
      return item.id === id;
    });
    const folder = _.cloneDeep(_.find(accountFolders.value, { id: id }));
    if (!folder) {
      throw new Error('Folder not found');
    }
    folder.isVisible = 0;
    copyFolders.push(folder);
    accountFolders.value = copyFolders;
  }

  function ACCOUNT_FOLDER_SHOW(id: string) {
    const copyFolders = _.cloneDeep(accountFolders.value);
    _.remove(copyFolders, (item) => {
      return item.id === id;
    });
    const folder = _.cloneDeep(_.find(accountFolders.value, { id: id }));
    if (!folder) {
      throw new Error('Folder not found');
    }
    folder.isVisible = AccountFolderVisibility.Visible;
    copyFolders.push(folder);
    accountFolders.value = copyFolders;
  }

  return {
    accountFolders,
    accountFoldersLoadedAt,
    accountFoldersOpened,
    isAccountFoldersLoaded,
    accountFoldersOrdered,
    fetchAccountFolders,
    openAccountFolder,
    closeAccountFolder,
    updateAccountFolder,
    createAccountFolder,
    replaceAccountFolder,
    orderAccountFolderList,
    hideAccountFolder,
    showAccountFolder
  };
});
