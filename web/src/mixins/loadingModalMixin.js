export const loadingModalMixin = {
  data() {
    return {
      isLoadingModalOpened: false,
    }
  },
  methods: {
    openLoadingModal() {
      this.isLoadingModalOpened = true;
    },
    closeLoadingModal() {
      this.isLoadingModalOpened = false;
    },
  }
}
