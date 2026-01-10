<template>
  <q-page class="settings-change-password">
    <div class="settings-change-password-wrapper">
      <!-- toolbar for mobile -->
      <div class="settings-toolbar-mobile">
        <div>
          <q-btn class="settings-toolbar-mobile-button" flat icon="arrow_back" @click="navigateTo('settingsProfile', true)" />
        </div>
        <div>
          <h4 class="settings-toolbar-mobile-title">{{ $t('modules.user.page.settings.profile.change_password.header') }}</h4>
        </div>
        <div class="settings-toolbar-mobile-container"></div>
      </div>

      <!-- toolbar for desktop -->
      <div class="settings-toolbar-desktop">
        <div class="settings-breadcrumbs">
          <div class="settings-breadcrumbs-item" @click="navigateTo('settings', true)">
            {{ $t('pages.settings.settings.header_desktop') }}
          </div>
          <div class="settings-breadcrumbs-arrow">
            <img src="~assets/breadcrumbs.svg" />
          </div>
          <div class="settings-breadcrumbs-item" @click="navigateTo('settingsProfile', true)">
            {{ $t('modules.user.page.settings.profile.menu_item') }}
          </div>
        </div>
      </div>

      <!-- main block -->
      <h4 class="settings-label-header">{{ $t('modules.user.page.settings.profile.change_password.header') }}</h4>

      <div class="settings-change-password-form">
        <q-form
          ref="changePasswordForm"
          novalidate
          @submit="submit"
        >
          <q-input
            outlined
            class="form-input full-width"
            :placeholder="$t('modules.user.form.change_password.password.placeholder')"
            type="password"
            v-model="oldPassword"
            :label="$t('modules.user.form.change_password.password.label')"
            lazy-rules
            :rules="[
              val => isNotEmpty(val) || $t('modules.user.form.change_password.password.validation.invalid_password')
            ]"
          />

          <q-input
            outlined
            class="form-input full-width"
            :placeholder="$t('modules.user.form.change_password.new_password.placeholder')"
            type="password"
            v-model="newPassword"
            :label="$t('modules.user.form.change_password.new_password.label')"
            lazy-rules
            :rules="[
              val => isValidPassword(val) || $t('modules.user.form.user.password.validation.invalid_password'),
              val => val !== oldPassword || $t('modules.user.form.change_password.new_password.validation.not_equals')
            ]"
          />

          <q-input
            outlined
            class="form-input full-width"
            :placeholder="$t('modules.user.form.change_password.new_password_retry.placeholder')"
            type="password"
            v-model="newPasswordRetry"
            :label="$t('modules.user.form.change_password.new_password_retry.label')"
            lazy-rules
            :rules="[
              val => isNotEmpty(val) || $t('modules.user.form.user.password_retry.validation.required_field'),
              val => isValidPassword(val) || $t('modules.user.form.user.password_retry.validation.invalid_password'),
              val => val === newPassword || $t('modules.user.form.change_password.new_password_retry.validation.not_equals')
            ]"
          />

          <q-btn
            class="econumo-btn -medium -magenta full-width"
            :label="$t('modules.user.form.change_password.submit.label')"
            type="submit"
            flat
          />
        </q-form>
      </div>

      <teleport to="body">
        <loading-modal v-if="isLoadingModalOpened" v-on:hide="closeLoadingModal()" :header-label="$t('modules.user.modal.change_password_loading.label')" />

        <q-dialog v-model="isSuccess" minimized @hide="isSuccess = false">
          <div class="econumo-modal form-hint-modal">
            <div class="form-hint-modal-text">
              {{ $t('modules.user.modal.change_password_success.text') }}
            </div>
            <div class="form-hint-modal-button">
              <q-btn
                class="econumo-btn -small -yellow full-width"
                :label="$t('elements.button.close.label')"
                type="button"
                @click="isSuccess = false"
                flat
              />
            </div>
          </div>
        </q-dialog>

        <q-dialog v-model="isError" minimized @hide="isError = false">
          <div class="econumo-modal form-fail-modal">
            <div class="form-fail-modal-title">
              {{ $t('modules.user.modal.change_password_error.header') }}
            </div>
            <div class="form-fail-modal-description">
              {{ errorMessage }}
            </div>
            <div class="form-fail-modal-button">
              <q-btn
                class="econumo-btn -small -yellow full-width"
                :label="$t('elements.button.close.label')"
                type="button"
                @click="isError = false"
                flat
              />
            </div>
          </div>
        </q-dialog>
      </teleport>
    </div>
  </q-page>
</template>

<script>
import {defineComponent} from 'vue';
import {navigationMixin} from '../../mixins/navigationMixin';
import {loadingModalMixin} from '../../mixins/loadingModalMixin';
import LoadingModal from '../../components/LoadingModal.vue';
import {useActiveAreaStore} from 'stores/active-area';
import {useUsersStore} from 'stores/users';
import { useValidation } from '../../composables/useValidation';

export default defineComponent({
  name: 'SettingsChangePasswordPage',
  mixins: [navigationMixin, loadingModalMixin],
  components: {LoadingModal},
  setup() {
    const validation = useValidation();
    return { ...validation };
  },
  data() {
    return {
      oldPassword: '',
      newPassword: '',
      newPasswordRetry: '',
      isError: false,
      errorMessage: '',
      isSuccess: false,
    }
  },
  created() {
    if (this.$router.currentRoute.value.name === 'settingsChangePassword') {
      useActiveAreaStore().setWorkspaceActiveArea();
    }
  },
  computed: {
  },
  methods: {
    submit: function () {
      this.openLoadingModal();
      return useUsersStore().userUpdatePassword({oldPassword: this.oldPassword.toString(), newPassword: this.newPassword.toString()})
        .then((response) => {
          if (!!response.data) {
            this.newPassword = '';
            this.oldPassword = '';
            this.newPasswordRetry = '';
            this.$refs.changePasswordForm.reset();
            this.isSuccess = true;
          } else {
            this.isError = true;
            this.errorMessage = response.message || this.$t('modules.user.modal.change_password_error.text');
          }
        }).catch((response) => {
          this.isError = true;
          this.errorMessage = response.message || this.$t('modules.user.modal.change_password_error.text');
        }).finally(() => {
          this.closeLoadingModal();
        })
    }
  }
})
</script>

