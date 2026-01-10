import { defineStore } from 'pinia';
import { date } from 'quasar';
import UsersAPIv1 from '../modules/api/v1/user';
import { METRICS, trackEvent } from '../modules/metrics';
import { useCurrenciesStore } from './currencies';
import { useSyncStore } from './sync';
import { computed, ComputedRef, Ref, ref } from 'vue';
import { Id, DateString } from '../modules/types';
import { CurrentUserDto, UserLoginItemDto, UserOptionDto, UserOptions } from '../modules/api/v1/dto/user.dto';
import { useLocalStorage, StorageSerializers } from '@vueuse/core';
import { RemovableRef } from '@vueuse/shared';
import { DATE_TIME_FORMAT } from '../modules/constants';
import { CurrencyDto } from '../modules/api/v1/dto/currency.dto';
import _ from 'lodash';
import { StorageKeys, hasToken, removeToken, setToken, removeItem } from '../modules/storage';
import { useBudgetsStore } from './budgets';


export const useUsersStore = defineStore('users', () => {
  const token: Ref<string | null> = ref(null);
  const userData = useLocalStorage(StorageKeys.USER_DATA, null, { serializer: StorageSerializers.object }) as RemovableRef<CurrentUserDto | null>;
  const userDataLoadedAt = useLocalStorage(StorageKeys.USER_DATA_LOADED_AT, null) as RemovableRef<DateString | null>;

  const isUserDataLoaded: ComputedRef<boolean> = computed(() => !!userDataLoadedAt.value && !!userData.value);
  const isUserAuthenticated: ComputedRef<boolean> = computed(() => hasToken());
  const userId: ComputedRef<Id|null> = computed(() => {
    if (!userData.value?.id) {
      throw new Error('User ID is not available');
    }
    return userData.value.id;
  });
  const userAvatar: ComputedRef<string|null> = computed(() => {
    if (!userData.value) {
      throw new Error('User data is not available');
    }
    return userData.value.avatar || '';
  });
  const userName: ComputedRef<string|null> = computed(() => {
    if (!userData.value?.name) {
      throw new Error('User name is not available');
    }
    return userData.value.name;
  });
  const userLogin: ComputedRef<string|null> = computed(() => {
    if (!userData.value?.email) {
      throw new Error('User email is not available');
    }
    return userData.value.email;
  });
  const userCurrencyId: ComputedRef<Id> = computed(() => {
    if (!userData.value) {
      throw new Error('User data is not available');
    }
    let result: Id | undefined;

    userData.value.options.forEach((option: UserOptionDto) => {
      if (option.name === UserOptions.CURRENCY_ID) {
        result = option.value as Id;
        return;
      }
    });
    if (undefined === result) {
      throw new Error('User currency ID is not available');
    }
    return result;
  });
  const userCurrency: ComputedRef<CurrencyDto|null> = computed(() => {
    if (!userData.value) {
      throw new Error('User data is not available');
    }
    const currenciesStore = useCurrenciesStore();
    let result: CurrencyDto | null = null;

    userData.value.options.forEach((option: UserOptionDto) => {
      if (option.name === UserOptions.CURRENCY) {
        currenciesStore.currencies.forEach(item => {
          if (item.code === option.value) {
            result = item;
            return;
          }
        });
      }
    });
    if (!result) {
      return null;
    }
    return result;
  });
  const userDefaultBudgetId: ComputedRef<string|null> = computed(() => {
    if (!userData.value) {
      throw new Error('User data is not available');
    }
    let result: string | null = null;

    userData.value.options.forEach((item: UserOptionDto) => {
      if (item.name === UserOptions.BUDGET) {
        result = item.value;
        return false;
      }
    });

    if (!result) {
      return null;
    }
    return result;
  });
  const userOnboardingCompleted: ComputedRef<boolean> = computed(() => {
    if (!userData.value) {
      throw new Error('User data is not available');
    }
    let result = true;

    userData.value.options.forEach((option: UserOptionDto) => {
      if (option.name === UserOptions.ONBOARDING) {
        result = option.value === 'completed';
        return false;
      }
    });
    return result;
  });

  function login(username: string, password: string) {
    return UsersAPIv1.login(username, password, (response: UserLoginItemDto) => {
      console.log('[INFO] login', response)
      if (response.token) {
        trackEvent(METRICS.USER_LOGIN);
        USERS_UPDATE_TOKEN(response.token);
        USERS_UPDATE_DATA(response.user);
      }
      return response;
    }, (error: any) => {
      console.log('[ERROR] login', error);
      throw error;
    });
  }

  function register(params: { email: string, password: string, name: string }) {
    trackEvent(METRICS.USER_REGISTRATION);
    return UsersAPIv1.register(params.email, params.password, params.name, (response: any) => {
      if (response.data && response.data.user) {
        USERS_UPDATE_DATA_TEMPORARY(response.data.user);
        return response.data.user;
      }
      return false;

    }, (error: any) => {
      console.log('[ERROR] register', error);
      return error;
    });
  }

  function logout() {
    trackEvent(METRICS.USER_LOGOUT);
    USERS_LOGOUT();
    return UsersAPIv1.logout(() => {
      return true;
    }, (error: any) => {
      console.log('[ERROR] logout', error);
      return error;
    });
  }

  function userUpdateName(name: string) {
    trackEvent(METRICS.USER_UPDATE_NAME);
    return UsersAPIv1.updateName(name, (response: any) => {
      if (response.data.user) {
        USERS_UPDATE_DATA(response.data.user);
      }
      return !!response.data;

    }, (error: any) => {
      console.log('[ERROR] userUpdateName', error);
      return error;
    });
  }

  function userUpdatePassword(params: { oldPassword: string, newPassword: string }) {
    trackEvent(METRICS.USER_UPDATE_PASSWORD);
    return UsersAPIv1.updatePassword(params.oldPassword, params.newPassword, (response: any) => {
      return response;

    }, (error: any) => {
      console.log('[ERROR] userUpdatePassword', error);
      return error;
    });
  }

  function userUpdateCurrency(params: any) {
    trackEvent(METRICS.USER_UPDATE_CURRENCY);
    return UsersAPIv1.updateCurrency(params.currency, (response: any) => {
      if (response.data.user) {
        USERS_UPDATE_DATA(response.data.user);
      }

      return !!response.data;
    }, (error: any) => {
      console.log('[ERROR] userUpdateCurrency', error);
      return error;
    });
  }

  function userCompleteOnboarding() {
    trackEvent(METRICS.USER_COMPLETE_ONBOARDING);
    return UsersAPIv1.completeOnboarding((response: any) => {
      if (response.data.user) {
        USERS_UPDATE_DATA(response.data.user);
      }

      return !!response.data;
    }, (error: any) => {
      console.log('[ERROR] userCompleteOnboarding', error);
      return error;
    });
  }

  function fetchUserData() {
    return UsersAPIv1.getUserData((user: CurrentUserDto) => {
      USERS_UPDATE_DATA(user);
      return true;

    }, (error: any) => {
      console.log('[ERROR] fetchUserData', error);
      return error;
    });
  }

  function userUpdateDefaultBudget(budgetId: Id) {
    trackEvent(METRICS.USER_UPDATE_DEFAULT_BUDGET);
    return UsersAPIv1.updateDefaultBudget(budgetId, (response: any) => {
      if (response.data.user) {
        USERS_UPDATE_DATA(response.data.user);
        useBudgetsStore().resetCachedBudget();
      }
      return !!response.data;
    }, (error: any) => {
      console.log('[ERROR] userUpdateDefaultBudget', error);
      return error;
    });
  }

  function userRemindPassword(username: string) {
    trackEvent(METRICS.USER_REMIND_PASSWORD);
    return UsersAPIv1.remindPassword(username, () => {
      return true;
    }, (error: any) => {
      console.log('[ERROR] userRemindPassword', error);
      return error;
    });
  }

  function userResetPassword(params: any) {
    trackEvent(METRICS.USER_RESET_PASSWORD);
    return UsersAPIv1.resetPassword(params, (response: any) => {
      return !!response.data;
    }, (error: any) => {
      console.log('[ERROR] userResetPassword', error);
      return error;
    });
  }

  function USERS_UPDATE_TOKEN(newToken: string) {
    setToken(newToken);
    token.value = newToken;
  }

  function USERS_UPDATE_DATA_TEMPORARY(data: any) {
    userData.value = data;
    userDataLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
  }

  function USERS_UPDATE_DATA(user: CurrentUserDto) {
    userData.value = _.cloneDeep(user);
    userDataLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
  }

  function USERS_LOGOUT() {
    removeToken();
    token.value = null;
    useSyncStore().clearCache();
  }

  return {
    token,
    userData,
    userDataLoadedAt,
    isUserDataLoaded,
    isUserAuthenticated,
    userId,
    userAvatar,
    userName,
    userLogin,
    userCurrency,
    userCurrencyId,
    userDefaultBudgetId,
    login,
    register,
    logout,
    userUpdateName,
    userUpdatePassword,
    userUpdateCurrency,
    fetchUserData,
    userUpdateDefaultBudget,
    userRemindPassword,
    userResetPassword,
    userOnboardingCompleted,
    userCompleteOnboarding
  };
});
