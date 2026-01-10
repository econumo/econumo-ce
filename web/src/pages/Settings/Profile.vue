<template>
  <q-page class="settings-profile">
    <div class="settings-profile-wrapper">
      <!-- toolbar for mobile -->
      <div class="settings-toolbar-mobile">
        <div>
          <q-btn class="settings-toolbar-mobile-button" flat icon="arrow_back" @click="navigateTo('settings', true)" />
        </div>
        <div>
          <h4 class="settings-toolbar-mobile-title">{{ $t('modules.user.page.settings.profile.header') }}</h4>
        </div>
        <div class="settings-toolbar-mobile-container">
          <q-btn class="settings-toolbar-mobile-button" flat icon="power_settings_new" @click="openLogoutDialogModal" />
        </div>
      </div>

      <!-- toolbar for desktop -->
      <div class="settings-toolbar-desktop">
        <div class="settings-breadcrumbs">
          <div class="settings-breadcrumbs-item" @click="navigateTo('settings', true)">
            {{ $t('pages.settings.settings.header_desktop') }}
          </div>
        </div>
      </div>

      <!-- main block -->
      <h4 class="settings-label-header">{{ $t('modules.user.page.settings.profile.header') }}</h4>
      <div class="settings-profile-user">
        <q-avatar class="settings-profile-user-avatar">
          <img class="settings-profile-user-avatar-img" :src="avatarUrl(userAvatar, 100)" width="100" height="100">
        </q-avatar>
        <div class="settings-profile-user-group">
          <div class="settings-profile-user-group-name">
            {{ userName }}
          </div>
          <div class="settings-profile-user-group-login">
            {{ userLogin }}
          </div>
          <div class="settings-profile-user-group-logout" @click="openLogoutDialogModal">
            {{ $t('pages.settings.settings.logout') }}
          </div>
        </div>
      </div>

      <div class="settings-profile-controls">
        <div class="settings-profile-controls-group">
          <q-input
            outlined
            class="settings-profile-controls-input -name form-input full-width"
            :placeholder="$t('modules.user.form.user.name.placeholder')"
            type="text"
            :model-value="name"
            :label="$t('modules.user.form.user.name.label')"
            lazy-rules
            :rules="[
              val => isNotEmpty(val) || $t('modules.user.form.user.name.validation.required_field'),
              val => isValidName(val) || $t('modules.user.form.user.name.validation.invalid_name')
            ]"
            @change="changeName"
          />

          <q-input
            outlined
            disable
            readonly
            class="settings-profile-controls-input form-input full-width"
            :placeholder="$t('modules.user.form.user.email.placeholder')"
            type="email"
            :model-value="email"
            :label="$t('modules.user.form.user.email.label')"
            lazy-rules
          />
        </div>
      </div>

      <q-item-label class="settings-main-label">{{ $t('modules.user.page.settings.profile.groups.security') }}</q-item-label>

      <q-item class="settings-main-item cursor-pointer" clickable v-ripple :to="{ name: 'settingsChangePassword'}">
        <q-item-section>
          <q-field borderless class="settings-main-item-field cursor-pointer">
            <template v-slot:control>
              <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('modules.user.page.settings.profile.change_password.menu_item') }}</div>
            </template>
            <template v-slot:append>
              <q-icon class="settings-main-item-field-arrow" name="keyboard_arrow_right" />
            </template>
          </q-field>
        </q-item-section>
      </q-item>

      <teleport to="body">
        <confirmation-dialog-modal
                                   v-if="logoutDialogModal.isOpened"
                                   :question-title="$t('modules.user.modal.sign_out.title')"
                                   :question-label="$t('modules.user.modal.sign_out.question')"
                                   :action-label="$t('modules.user.modal.sign_out.action.logout')"
                                   :cancel-label="$t('modules.user.modal.sign_out.action.cancel')"
                                   v-on:cancel="closeModal"
                                   v-on:proceed="logout"
        />
      </teleport>
    </div>
  </q-page>
</template>

<script>
import {defineComponent} from 'vue';
import {navigationMixin} from '../../mixins/navigationMixin';
import ConfirmationDialogModal from '../../components/ConfirmationDialogModal';
import { mapState } from 'pinia'
import {useUsersStore} from 'stores/users';
import {useActiveAreaStore} from 'stores/active-area';
import { useAvatar } from '../../composables/useAvatar';
import { useValidation } from '../../composables/useValidation';

export default defineComponent({
  name: 'SettingsProfilePage',
  mixins: [navigationMixin],
  components: {ConfirmationDialogModal},
  setup() {
    const { avatarUrl } = useAvatar();
    const validation = useValidation();
    return { avatarUrl, ...validation };
  },
  data() {
    return {
      nameCopy: null,
      emailCopy: null,
      logoutDialogModal: {
        isOpened: false
      }
    }
  },
  created() {
    if (this.$router.currentRoute.value.name === 'settingsProfile') {
      useActiveAreaStore().setWorkspaceActiveArea();
    }
  },
  computed: {
    ...mapState(useUsersStore, ['userAvatar', 'userName', 'userLogin']),
    name: {
      get() {
        return this.nameCopy || this.userName
      },
      set(value) {
        this.nameCopy = value;
        useUsersStore().userUpdateName(value);
      },
    },
    email: {
      get() {
        return this.userLogin
      },
      set(value) {
        console.log(value)
      },
    },
  },
  methods: {
    changeName: function(value) {
      this.name = value;
    },
    openLogoutDialogModal: function() {
      this.logoutDialogModal.isOpened = true;
    },
    closeModal: function() {
      this.logoutDialogModal.isOpened = false;
    },
    logout: function () {
      this.$router.push({name: 'logout'})
    }
  },
})
</script>

