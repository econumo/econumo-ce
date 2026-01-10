import { ref } from 'vue';
import { useCategoriesStore } from 'stores/categories';
import _ from 'lodash';

export function useDeleteCategory() {
  const isDeleteModalOpened = ref(false);
  const deleteCategory = ref({
    id: '',
    name: ''
  });

  const openDeleteModal = (category: any) => {
    deleteCategory.value = _.cloneDeep(category);
    isDeleteModalOpened.value = true;
  };

  const closeDeleteModal = () => {
    deleteCategory.value = {
      id: '',
      name: ''
    };
    isDeleteModalOpened.value = false;
  };

  const submitDeleteCategory = async () => {
    await useCategoriesStore().deleteCategory(deleteCategory.value.id);
    closeDeleteModal();
    return null;
  };

  return {
    isDeleteModalOpened,
    deleteCategory,
    openDeleteModal,
    closeDeleteModal,
    submitDeleteCategory
  };
} 