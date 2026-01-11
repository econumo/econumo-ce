import _ from 'lodash';
import config from '../modules/config';

export const languageModalMixin = {
  data() {
    return {
      isLanguageModalOpened: false,
    }
  },
  methods: {
    openLanguageModal() {
      this.isLanguageModalOpened = true;
    },
    closeLanguageModal() {
      this.isLanguageModalOpened = false;
    },
    fullLanguageLabel() {
      return _.find(config.getLocaleOptions(), {value: config.locale()}).label
    },
    shortLanguageLabel() {
      return _.find(config.getLocaleOptions(), {value: config.locale()}).short
    },
  }
}
