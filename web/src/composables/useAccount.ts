import { AccountDto } from '@shared/dto/account.dto';
import { Id } from '@shared/types';
import _ from 'lodash';

export function useAccount() {
  const accountName = (accounts: AccountDto[], accountId: Id, defaultName: string | null = null): string => {
    const account = _.find(accounts, { id: accountId });
    if (account === undefined) {
      return defaultName === null ? '' : defaultName;
    }

    return account.name ?? defaultName;
  };

  return {
    accountName
  };
} 