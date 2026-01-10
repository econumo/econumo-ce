import { reactive, toRefs } from 'vue';

export function useAcceptInviteModalState() {
  const state = reactive({
    isOpened: false,
    data: null as string | null
  });

  const open = () => {
    state.isOpened = true;
  };

  const close = () => {
    state.isOpened = false;
    state.data = null;
  };

  return {
    ...toRefs(state),
    open,
    close
  };
} 