import { defineStore } from 'pinia';
import { ref, Ref } from 'vue';

enum ActiveArea {
  SIDEBAR = 'sidebar',
  WORKSPACE = 'workspace'
}

export const useActiveAreaStore = defineStore('active-area', () => {
  const activeArea: Ref<ActiveArea> = ref(ActiveArea.SIDEBAR);

  function setSidebarActiveArea() {
    activeArea.value = ActiveArea.SIDEBAR;
  }

  function setWorkspaceActiveArea() {
    activeArea.value = ActiveArea.WORKSPACE;
  }

  return {
    activeArea,
    setSidebarActiveArea,
    setWorkspaceActiveArea
  };
});
