import { ref } from 'vue';
import { useCategoriesStore } from 'stores/categories';
import { defaultCategoryIcon } from '../../modules/icons';

export interface CreateCategoryForm {
  id: null;
  name: string;
  type: string;
  icon: string;
}

// Create singleton instance
const isCreateModalOpened = ref(false);
const createCategory = ref({
  type: 'expense',
  name: '',
  icon: ''
});

export function useCreateCategory() {
  const openCreateModal = () => {
    createCategory.value.type = 'expense';
    createCategory.value.name = '';
    createCategory.value.icon = defaultCategoryIcon;
    isCreateModalOpened.value = true;
  };

  const closeCreateModal = () => {
    createCategory.value.type = 'expense';
    createCategory.value.name = '';
    createCategory.value.icon = '';
    isCreateModalOpened.value = false;
  };

  const submitCategory = async (formRef: any) => {
    const form: CreateCategoryForm = {
      id: null,
      name: createCategory.value.name,
      type: createCategory.value.type,
      icon: createCategory.value.icon
    };

    await useCategoriesStore().createCategory(form);
    if (formRef) {
      formRef.resetValidation();
    }
    closeCreateModal();
    return null;
  };

  return {
    isCreateModalOpened,
    createCategory,
    openCreateModal,
    closeCreateModal,
    submitCategory
  };
} 