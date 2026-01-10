import { reactive, toRefs } from 'vue';

export interface GenerateInviteData {
  code: string;
  expiredAt: string;
}

export function useGenerateInviteModalState() {
  const state = reactive({
    isOpened: false,
    data: null as GenerateInviteData | null
  });

  const open = (data: GenerateInviteData) => {
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