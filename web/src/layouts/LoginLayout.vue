<template>
  <q-layout class="fullscreen login">
    <q-page-container
      class="login-layout full-width column wrap justify-center items-center content-center"
    >
      <div class="login-layout-lang" @click="openLanguageModal">
        <span class="login-layout-lang-link">{{ shortLanguageLabel() }}</span>
      </div>
      <div class="login-layout-head">
        <div class="row login-layout-head-logo justify-center">
          <div>
            <img
              src="~assets/econumo.svg"
              width="194"
              height="20"
              :alt="$t('elements.econumo.label')"
            />
          </div>
        </div>
        <div class="login-layout-head-tabs flex no-wrap justify-between">
          <div class="login-layout-head-tabs-item col flex justify-center">
            <router-link
              :to="{ name: 'login' }"
              class="login-layout-head-tabs-link"
              >{{ $t('modules.user.page.sign_in.header') }}</router-link
            >
          </div>
          <div class="login-layout-head-tabs-item col flex justify-center">
            <a
              href="#"
              class="login-layout-head-tabs-link"
              disabled
              v-if="!isPaywallEnabled && !isRegistrationAllowed"
              >{{ $t('modules.user.page.sign_up.header') }}</a
            >
            <router-link
              :to="{ name: 'register' }"
              class="login-layout-head-tabs-link"
              v-else
              >{{ $t('modules.user.page.sign_up.header') }}</router-link
            >
          </div>
        </div>
      </div>
      <div class="login-layout-body">
        <router-view />
      </div>
      <div class="login-layout-social">
        <a
          target="_blank"
          href="https://github.com/econumo/"
          class="login-layout-ico -gh"
          rel="nofollow"
          aria-label="GitHub"
        ></a>
        <a
          target="_blank"
          href="https://x.com/econumo"
          class="login-layout-ico -tw"
          rel="nofollow"
          aria-label="Twitter"
        ></a>
      </div>
      <div class="login-layout-help">
        <a
          target="_blank"
          :href="$t('blocks.help.url')"
          class="login-layout-help-link"
          rel="nofollow"
          :aria-label="$t('blocks.help.label')"
          >{{ $t('blocks.help.label') }}</a
        >
      </div>

      <teleport to="body">
        <change-language-modal
          v-if="isLanguageModalOpened"
          v-on:cancel="closeLanguageModal()"
        />
      </teleport>
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent } from 'vue';
import ChangeLanguageModal from '../components/ChangeLanguageModal.vue';
import { languageModalMixin } from '../mixins/languageModalMixin';
import { econumoPackage } from '../modules/package';
import { isRegistrationAllowed } from '../modules/config';

export default defineComponent({
  name: 'LoginLayout',
  components: { ChangeLanguageModal },
  mixins: [languageModalMixin],
  data() {
    return {};
  },
  computed: {
    econumoEdition: function () {
      return econumoPackage.label;
    },
    isRegistrationAllowed: function () {
      return isRegistrationAllowed();
    },
    isPaywallEnabled: function () {
      return econumoPackage.isPaywallEnabled;
    },
  },
});
</script>
