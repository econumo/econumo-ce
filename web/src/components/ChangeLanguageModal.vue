<template>
  <q-dialog :model-value="true" minimized @hide="$emit('cancel')">
    <div class="econumo-modal lang-modal">
      <div class="lang-modal-text">
        {{ $t('modules.app.modal.change_language.header') }}
      </div>
      <div class="lang-modal-button">
        <q-btn
          :class="item.value === locale ? 'econumo-btn -medium -yellow full-width' : 'econumo-btn -medium -transparent full-width'"
          :label="item.label"
          type="button"
          v-for="item in localeOptions"
          v-bind:key="item.value"
          @click="selectLocale(item)"
          flat
        />
      </div>
    </div>
  </q-dialog>
</template>

<script>
import {defineComponent} from 'vue'
import config from '../modules/config';
import { useI18n } from 'vue-i18n';

export default defineComponent({

  setup() {
    const { locale } = useI18n({ useScope: 'global' })
    return { locale }
  },
  computed: {
    localeOptions: function () {
      return config.getLocaleOptions()
    }
  },
  methods: {
    selectLocale: function(value) {
      if (this.locale !== value.value) {
        this.locale = value;
        config.locale(value.value);
        window.location.reload();
      }
      this.$emit('cancel')
    },
  }
})
</script>
