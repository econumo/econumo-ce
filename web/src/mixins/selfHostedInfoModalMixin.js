export const selfHostedInfoModalMixin = {
  data() {
    return {
      isSelfHostedInfoModalOpened: false,
    }
  },
  methods: {
    openSelfHostedInfoModal() {
      this.isSelfHostedInfoModalOpened = true;
    },
    closeSelfHostedInfoModal() {
      this.isSelfHostedInfoModalOpened = false;
    },
  }
}
