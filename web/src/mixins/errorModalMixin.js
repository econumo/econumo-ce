export const errorModalMixin = {
  data() {
    return {
      errorModal: {
        isOpened: false,
        header: '',
        description: ''
      }
    }
  },
  methods: {
    openErrorModal(header, description) {
      this.errorModal.header = header;
      this.errorModal.description = description;
      this.errorModal.isOpened = true;
    },
    closeErrorModal() {
      this.errorModal.header = '';
      this.errorModal.description = '';
      this.errorModal.isOpened = false;
    },
  }
}
