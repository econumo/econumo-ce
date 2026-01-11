import { defineStore } from 'pinia';
import { v4 as uuid } from 'uuid';
import CategoriesAPIv1 from '../modules/api/v1/category';
import _ from 'lodash';
import { getChangedPositions } from '../modules/helpers';
import { METRICS, trackEvent } from '../modules/metrics';
import { date } from 'quasar';
import { useUsersStore } from './users';
import { useBudgetsStore } from './budgets';
import { useTransactionsStore } from './transactions';
import { useLocalStorage } from '@vueuse/core';
import { StorageKeys } from '../modules/storage';
import { RemovableRef } from '@vueuse/shared';
import { CategoryDto, CategoryItemDto, CategoryListDto } from '../modules/api/v1/dto/category.dto';
import { DateString, Id, BooleanType } from '../modules/types';
import { computed, ComputedRef } from 'vue';
import { DATE_TIME_FORMAT } from '../modules/constants';

interface ReplaceCategoryForm {
  oldId: Id,
  newId: Id
}

interface ChangeCategorySortModeForm {
  type: string,
  direction: 'asc' | 'desc'
}

export const useCategoriesStore = defineStore('categories', () => {
  const categories = useLocalStorage(StorageKeys.CATEGORIES, []) as RemovableRef<CategoryDto[]>;
  const categoriesLoadedAt = useLocalStorage(StorageKeys.CATEGORIES_LOADED_AT, null) as RemovableRef<DateString | null>;

  const isCategoriesLoaded: ComputedRef<boolean> = computed(() => !!categoriesLoadedAt.value);
  const ownCategories = computed(() => {
    const usersStore = useUsersStore();
    return _.orderBy(_.filter(_.cloneDeep(categories.value), { ownerUserId: usersStore.userId }), 'position', 'asc') as CategoryDto[];
  });
  const categoriesOrdered = computed(() => {
    return _.orderBy(_.filter(_.cloneDeep(categories.value), { isArchived: BooleanType.FALSE }), 'position', 'asc') as CategoryDto[];
  });
  const categoriesArchived = computed(() => {
    return _.orderBy(_.filter(_.cloneDeep(categories.value), { isArchived: BooleanType.TRUE }), 'updatedAt', 'desc') as CategoryDto[];
  });

  function fetchCategories() {
    return CategoriesAPIv1.getList((response: CategoryListDto) => {
      CATEGORIES_INIT(response.items);
    }, (error: any) => {
      console.log('[ERROR] Categories store', error);
      return error;
    });
  }

  function createCategory(params: any) {
    trackEvent(METRICS.CATEGORY_CREATE);
    params.id = uuid();
    const usersStore = useUsersStore();
    const foundCategory = _.findLast(_.filter(categories.value, { ownerUserId: usersStore.userId }), (item: any) => {
      return item.name.toLowerCase() === params.name.toLowerCase();
    });
    if (foundCategory) {
      return new Promise((resolve, reject) => {
        if (!params.name) {
          reject('Category name is empty');
          return;
        }

        if (foundCategory) {
          resolve(foundCategory);
        }
      });
    }

    const budgetsStore = useBudgetsStore();
    return CategoriesAPIv1.create(params, (response: CategoryItemDto) => {
      CATEGORY_CREATE(response.item);
      budgetsStore.resetCachedBudget();
      return response.item;
    }, (error: any) => {
      console.log('[ERROR] Categories store', error);
      return error;
    });
  }

  function updateCategory(params: any) {
    trackEvent(METRICS.CATEGORY_UPDATE);
    const usersStore = useUsersStore();
    const foundCategory = _.findLast(_.filter(categories.value, { ownerUserId: usersStore.userId }), (item: any) => {
      return item.name.toLowerCase() === params.name.toLowerCase() && item.id !== params.id;
    });
    if (foundCategory) {
      return new Promise((resolve, reject) => {
        reject('Category already exists');
      });
    }

    return CategoriesAPIv1.update(params, (response: any) => {
      CATEGORY_UPDATE(params);
      return !!response.data;
    }, (error: any) => {
      console.log('[ERROR] Categories store', error);
      return error;
    });
  }

  function orderCategoryList(categoryIds: Id[]) {
    trackEvent(METRICS.CATEGORY_ORDER_LIST);
    const changes = getChangedPositions(categories.value, categoryIds);
    if (!changes.length) {
      return new Promise((resolve, reject) => {
        reject('No changes');
      });
    }
    return CategoriesAPIv1.orderList(changes, (response: CategoryListDto) => {
      CATEGORIES_INIT(response.items);
      return response.items;
    }, (error: any) => {
      console.log('[ERROR] Categories store', error);
      return error;
    });
  }

  function deleteCategory(categoryId: Id) {
    trackEvent(METRICS.CATEGORY_DELETE);
    const budgetsStore = useBudgetsStore();
    const transactionsStore = useTransactionsStore();
    return CategoriesAPIv1.delete(categoryId, (response: any) => {
      if (!!response.data) {
        transactionsStore.TRANSACTIONS_CATEGORY_DELETE(categoryId);
        CATEGORY_DELETE(categoryId);
        budgetsStore.resetCachedBudget();
        return true;
      }
      return false;
    }, (error: any) => {
      console.log('[ERROR] Categories store', error);
      return error;
    });
  }

  function replaceCategory(params: ReplaceCategoryForm) {
    trackEvent(METRICS.CATEGORY_REPLACE);
    const budgetsStore = useBudgetsStore();
    const transactionsStore = useTransactionsStore();
    return CategoriesAPIv1.replace(params.oldId, params.newId, () => {
      transactionsStore.TRANSACTIONS_CATEGORY_REPLACE(params);
      CATEGORY_DELETE(params.oldId);
      budgetsStore.resetCachedBudget();
      return true;
    }, (error: any) => {
      console.log('[ERROR] Categories store', error);
      return error;
    });
  }

  function archiveCategory(categoryId: Id) {
    trackEvent(METRICS.CATEGORY_ARCHIVE);
    const budgetsStore = useBudgetsStore();
    return CategoriesAPIv1.archive(categoryId, (response: any) => {
      if (!!response.data) {
        CATEGORY_UPDATE_ARCHIVE({ id: categoryId, isArchived: BooleanType.TRUE });
        budgetsStore.resetCachedBudget();
        return true;
      }
      return false;
    }, (error: any) => {
      console.log('[ERROR] Categories store', error);
      return error;
    });
  }

  function unarchiveCategory(categoryId: Id) {
    trackEvent(METRICS.CATEGORY_UNARCHIVE);
    const budgetsStore = useBudgetsStore();
    return CategoriesAPIv1.unarchive(categoryId, (response: any) => {
      if (!!response.data) {
        CATEGORY_UPDATE_ARCHIVE({ id: categoryId, isArchived: BooleanType.FALSE });
        budgetsStore.resetCachedBudget();
        return true;
      }
      return false;
    }, (error: any) => {
      console.log('[ERROR] Categories store', error);
      return error;
    });
  }

  function changeCategoriesSortMode(options: ChangeCategorySortModeForm) {
    trackEvent(METRICS.CATEGORY_CHANGE_ORDER, options);
    let orderedCategories = _.cloneDeep(ownCategories.value);
    const usageCounts: { [key: string]: number } = {};
    if (options.type === 'name') {
      orderedCategories = _.orderBy(orderedCategories, ['name'], [options.direction]);
    } else if (options.type === 'count') {
      const transactionsStore = useTransactionsStore();
      orderedCategories.forEach((item) => {
        usageCounts[item.id] = _.filter(transactionsStore.transactions, { categoryId: item.id }).length;
      });
      orderedCategories = _.orderBy(orderedCategories, item => usageCounts[item.id], [options.direction]);
    }
    const categoryIds: any[] = [];
    orderedCategories.forEach((item: any) => {
      categoryIds.push(item.id);
    });
    return orderCategoryList(categoryIds);
  }

  function CATEGORIES_INIT(items: CategoryDto[]) {
    categories.value = items;
    categoriesLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
  }

  function CATEGORY_CREATE(options: CategoryDto) {
    categories.value.push(options);
  }

  function CATEGORY_UPDATE(options: any) {
    const copyCategories: any = _.cloneDeep(categories.value);
    let category = _.cloneDeep(_.find(categories.value, { id: options.id }));
    category = _.extend(category, options);
    _.remove(copyCategories, (item: any) => {
      return item.id === options.id;
    });
    copyCategories.push(category);
    categories.value = copyCategories;
  }

  function CATEGORY_DELETE(id: Id) {
    const copyCategories: any = _.cloneDeep(categories.value);
    _.remove(copyCategories, (item: any) => {
      return item.id === id;
    });
    categories.value = copyCategories;
  }

  function CATEGORY_UPDATE_ARCHIVE(options: { id: Id, isArchived: BooleanType }) {
    const category: CategoryDto | undefined = _.find(categories.value, { id: options.id });
    if (!category) {
      return;
    }
    category.isArchived = options.isArchived;
  }

  return {
    categories,
    categoriesLoadedAt,
    isCategoriesLoaded,
    ownCategories,
    categoriesOrdered,
    categoriesArchived,
    fetchCategories,
    createCategory,
    updateCategory,
    orderCategoryList,
    deleteCategory,
    replaceCategory,
    archiveCategory,
    unarchiveCategory,
    changeCategoriesSortMode
  };
});
