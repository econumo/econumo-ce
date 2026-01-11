import { ref } from 'vue';
import { useCategoriesStore } from 'stores/categories';

type SortType = 'name' | 'type';
type SortDirection = 'asc' | 'desc';

export function useSortDialog() {
  const isSortDialogOpened = ref(false);

  const openSortDialog = () => {
    isSortDialogOpened.value = true;
  };

  const closeSortDialog = () => {
    isSortDialogOpened.value = false;
  };

  const submitSortDialog = async (type: SortType, direction: SortDirection): Promise<void> => {
    try {
      await useCategoriesStore().changeCategoriesSortMode({type, direction});
      closeSortDialog();
    } catch (error) {
      throw error;
    }
  };

  return {
    isSortDialogOpened,
    openSortDialog,
    closeSortDialog,
    submitSortDialog
  };
} 