import { ref } from 'vue';
import _ from 'lodash';

interface ContextMenuCategory {
  id: string;
  name: string;
}

export function useContextMenu() {
  const isContextMenuOpened = ref(false);
  const contextMenuCategory = ref<ContextMenuCategory>({
    id: '',
    name: ''
  });

  const openContextMenu = (category: any) => {
    contextMenuCategory.value = _.cloneDeep(category);
    isContextMenuOpened.value = true;
  };

  const closeContextMenu = () => {
    contextMenuCategory.value = {
      id: '',
      name: ''
    };
    isContextMenuOpened.value = false;
  };

  return {
    isContextMenuOpened,
    contextMenuCategory,
    openContextMenu,
    closeContextMenu
  };
} 