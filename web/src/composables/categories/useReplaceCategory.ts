import { ref } from 'vue';
import { useCategoriesStore } from 'stores/categories';
import _ from 'lodash';

interface Category {
  id: string;
  name: string;
  type: string;
  icon: string;
}

// Create singleton instance
const isReplaceModalOpened = ref(false);
const replaceCategory = ref({
  categoryId: '',
  selectedCategoryId: null as string | null
});

export function useReplaceCategory() {
  const openReplaceModal = (categoryId: string) => {
    replaceCategory.value.categoryId = categoryId;
    replaceCategory.value.selectedCategoryId = null;
    isReplaceModalOpened.value = true;
  };

  const closeReplaceModal = () => {
    replaceCategory.value.categoryId = '';
    replaceCategory.value.selectedCategoryId = null;
    isReplaceModalOpened.value = false;
  };

  const submitReplaceCategory = async () => {
    if (!replaceCategory.value.selectedCategoryId) return;
    
    await useCategoriesStore().replaceCategory({
      oldId: replaceCategory.value.categoryId, 
      newId: replaceCategory.value.selectedCategoryId
    });
    closeReplaceModal();
    return null;
  };

  const categoriesExcept = (categories: Category[], categoryId: string) => {
    const category = _.find(categories, {id: categoryId});
    if (!category) {
      return [];
    }

    return _.filter(categories, (item) => {
      return item.id !== category.id && item.type === category.type;
    });
  };

  return {
    isReplaceModalOpened,
    replaceCategory,
    openReplaceModal,
    closeReplaceModal,
    submitReplaceCategory,
    categoriesExcept
  };
} 