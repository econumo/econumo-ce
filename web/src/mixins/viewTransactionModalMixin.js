import _ from 'lodash';

export const viewTransactionModalMixin = {
  data() {
    return {
      previewTransactionModal: {
        isOpened: false,
        transaction: null,
        account: null,
        accountRecipient: null
      },
    }
  },
  methods: {
    openPreviewTransactionModal: function (transactionId) {
      const transaction = _.cloneDeep(_.find(this.transactions, {id: transactionId}));
      this.previewTransactionModal.transaction = transaction;
      this.previewTransactionModal.isOpened = true;
      this.previewTransactionModal.account = _.find(this.accounts, {id: transaction.accountId});
      if (transaction.accountRecipientId) {
        this.previewTransactionModal.accountRecipient = _.find(this.accounts, {id: transaction.accountRecipientId});
      }
    },
  }
}
