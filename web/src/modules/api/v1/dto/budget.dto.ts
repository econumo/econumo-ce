import { AccessRole, UserAccessDto } from '@shared/dto/access.dto';
import { DateString, DateTimeString, Icon, Id } from '@shared/types';
import { UserDto } from '@shared/dto/user.dto';

export enum BudgetElementType {
  ENVELOPE = 0,
  CATEGORY = 1,
  TAG = 2,
}

export interface BudgetListDto {
  items: BudgetMetaDto[];
}

export interface BudgetTransactionCategoryDto {
  id: Id;
  name: string;
  icon: Icon;
}

export interface BudgetTransactionPayeeDto {
  id: Id;
  name: string;
}

export interface BudgetTransactionTagDto {
  id: Id;
  name: string;
}

export interface BudgetTransactionDto {
  id: Id;
  author: UserDto;
  currencyId: Id;
  amount: number;
  description: string;
  spentAt: DateString;
  category: BudgetTransactionCategoryDto | null;
  payee: BudgetTransactionPayeeDto | null;
  tag: BudgetTransactionTagDto | null;
}

export interface BudgetTransactionListDto {
  items: BudgetTransactionDto[];
}

export interface BudgetMetaDto {
  id: Id;
  ownerUserId: Id;
  name: string;
  startedAt: DateTimeString;
  currencyId: Id;
  access: UserAccessDto[];
}

export interface BudgetFolderDto {
  id: Id;
  name: string;
  position: number;
}

export interface BudgetBaseElementDto {
  id: Id;
  type: BudgetElementType;
  name: string;
  icon: string;
  isArchived: number;
  spent: number;
  budgetSpent: number;
  ownerUserId: Id;
}

export interface BudgetChildElementDto extends BudgetBaseElementDto {
}

export interface BudgetElementDto extends BudgetBaseElementDto {
  currencyId: Id | null;
  folderId: Id | null;
  position: number;
  budgeted: number;
  available: number;
  children: BudgetChildElementDto[];
}

export interface BudgetBalanceDto {
  currencyId: Id;
  startBalance: number | null;
  endBalance: number | null;
  income: number | null;
  expenses: number | null;
  exchanges: number | null;
  holdings: number | null;
}

export interface BudgetFiltersDto {
  periodStart: DateTimeString;
  periodEnd: DateTimeString;
  excludedAccountsIds: Id[];
}

export interface BudgetStructureDto {
  folders: BudgetFolderDto[];
  elements: BudgetElementDto[];
}

export interface BudgetCurrencyRateDto {
  currencyId: Id;
  baseCurrencyId: Id;
  rate: number;
  periodStart: DateString;
  periodEnd: DateString;
}

export interface BudgetDto {
  meta: BudgetMetaDto;
  filters: BudgetFiltersDto;
  balances: BudgetBalanceDto[];
  currencyRates: BudgetCurrencyRateDto[];
  structure: BudgetStructureDto;
}

export interface GetBudgetRequestDto {
  id: Id;
  date: DateString;
}

export interface CreateBudgetRequestDto {
  id: Id;
  name: string;
  excludedAccounts: Id[];
  startDate: DateTimeString | null;
  currencyId: Id;
}

export interface UpdateBudgetRequestDto {
  id: Id;
  name: string | null;
  excludedAccounts: Id[];
  currencyId: Id;
}

export interface DeleteBudgetDto {
  id: Id;
}

export interface CreateBudgetFolderRequestDto {
  budgetId: Id;
  id: Id;
  name: string;
}

export interface UpdateBudgetFolderRequestDto {
  budgetId: Id;
  id: Id;
  name: string;
}

export interface AffectedElementRequestDto {
  id: Id;
  folderId: Id | null;
  position: number;
}

export interface MoveElementRequestDto {
  budgetId: Id;
  items: AffectedElementRequestDto[];
}

export interface AffectedFolderRequestDto {
  id: Id;
  position: number;
}

export interface OrderFolderListRequestDto {
  budgetId: Id;
  items: AffectedFolderRequestDto[];
}

export interface BudgetTransactionListRequestDto {
  budgetId: Id;
  periodStart: DateString;
  categoryId: Id | null;
  tagId: Id | null;
  envelopeId: Id | null;
}

export interface CreateBudgetEnvelopeRequestDto {
  budgetId: Id;
  id: Id;
  name: string;
  icon: Icon;
  currencyId: Id;
  folderId: Id | null;
  categories: Id[];
}

export interface UpdateBudgetEnvelopeRequestDto {
  budgetId: Id;
  id: Id;
  name: string;
  icon: Icon;
  currencyId: Id;
  isArchived: number;
  categories: Id[];
}

export interface DeleteBudgetEnvelopeRequestDto {
  budgetId: Id;
  id: Id;
}

export interface ChangeCurrencyRequestDto {
  budgetId: Id;
  elementId: Id;
  currencyId: Id;
}

export interface SetElementLimitRequestDto {
  budgetId: Id;
  elementId: Id;
  amount: number | string | null;
  period: DateString;
}

export interface GrantBudgetAccessRequestDto {
  budgetId: Id;
  userId: Id;
  role: AccessRole;
}

export interface RevokeBudgetAccessRequestDto {
  budgetId: Id;
  userId: Id;
}

export interface AcceptBudgetAccessRequestDto {
  budgetId: Id;
}

export interface DeclineBudgetAccessRequestDto {
  budgetId: Id;
}


export interface GetBudgetResponseDto {
  success: boolean;
  data: {
    item: BudgetDto
  };
}

export interface CreateBudgetResponseDto {
  success: boolean;
  message: string;
  data: {
    item: BudgetDto
  };
}

export interface UpdateBudgetResponseDto {
  success: boolean;
  message: string;
  data: {
    item: BudgetMetaDto
  };
}

export interface CreateBudgetFolderResponseDto {
  success: boolean;
  message: string;
  data: {
    item: BudgetFolderDto
  };
}

export interface UpdateBudgetFolderResponseDto {
  success: boolean;
  message: string;
  data: {
    item: BudgetFolderDto
  };
}

export interface MoveElementResponseDto {
  success: boolean;
  message: string;
  data: [];
}

export interface DeleteBudgetFolderResponseDto {
  success: boolean;
  message: string;
  data: [];
}

export interface OrderFolderListResponseDto {
  success: boolean;
  message: string;
  data: [];
}

export interface BudgetListResponseDto {
  data: BudgetListDto;
}

export interface GetTransactionListResponseDto {
  success: boolean;
  data: BudgetTransactionListDto;
}

export interface CreateEnvelopeResponseDto {
  success: boolean;
  message: string;
  data: {
    item: BudgetElementDto
  };
}

export interface UpdateEnvelopeResponseDto {
  success: boolean;
  message: string;
  data: {
    item: BudgetElementDto
  };
}

export interface DeleteEnvelopeResponseDto {
  success: boolean;
  message: string;
}

export interface ChangeCurrencyResponseDto {
  success: boolean;
  message: string;
}

export interface SetLimitResponseDto {
  success: boolean;
  message: string;
}

export interface GrantAccessResponseDto {
  success: boolean;
  data: BudgetListDto;
}

export interface DeleteBudgetResponseDto {
  success: boolean;
  message: string;
}

export interface RevokeAccessResponseDto {
  success: boolean;
}

export interface AcceptAccessResponseDto {
  success: boolean;
  data: BudgetListDto;
}

export interface DeclineAccessResponseDto {
  success: boolean;
}

export interface BudgetStatsDto {
  budgeted: number;
  spent: number;
  available: number;
}
