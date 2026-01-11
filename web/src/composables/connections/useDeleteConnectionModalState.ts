import { reactive, toRefs } from 'vue';
import { type Connection } from '../../stores/connections';

export function useDeleteConnectionModalState() {
  const state = reactive({
    isOpened: false,
    data: null as Connection | null
  });

  const open = (data: Connection) => {
    state.isOpened = true;
    state.data = data;
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