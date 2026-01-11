import { ref } from 'vue';
import { useCategoriesStore } from 'stores/categories';
import _ from 'lodash';

export interface EditCategoryForm {
  id: string;
  name: string;
  icon: string;
}

// Create singleton instance
const isEditModalOpened = ref(false);
const editCategory = ref({
  id: '',
  name: '',
  type: '',
  icon: ''
});

export function useEditCategory() {
  const openEditModal = (category: any) => {
    editCategory.value = _.cloneDeep(category);
    isEditModalOpened.value = true;
  };

  const closeEditModal = () => {
    editCategory.value = {
      id: '',
      name: '',
      type: '',
      icon: ''
    };
    isEditModalOpened.value = false;
  };

  const submitEditForm = async (formRef: any) => {
    const form: EditCategoryForm = {
      id: editCategory.value.id,
      name: editCategory.value.name,
      icon: editCategory.value.icon
    };

    await useCategoriesStore().updateCategory(form);
    if (formRef) {
      formRef.resetValidation();
    }
    closeEditModal();
    return null;
  };

  return {
    isEditModalOpened,
    editCategory,
    openEditModal,
    closeEditModal,
    submitEditForm
  };
} 