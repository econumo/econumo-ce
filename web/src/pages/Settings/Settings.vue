<template>
  <q-page class="settings">
    <!-- toolbar for mobile -->
    <div class="settings-toolbar-mobile">
      <div>
        <q-btn class="settings-toolbar-mobile-button" flat icon="arrow_back" @click="navigateTo('home', true)" />
      </div>
      <div>
        <h4 class="settings-toolbar-mobile-title">{{ $t('pages.settings.settings.header') }}</h4>
      </div>
      <div class="settings-toolbar-mobile-container"></div>
    </div>

    <q-item-label class="settings-label-header">{{ $t('pages.settings.settings.header_desktop') }}</q-item-label>

    <!-- main block -->
    <div>
      <q-list class="settings-main">
        <q-item class="settings-main-user cursor-pointer" clickable :to="{ name: 'settingsProfile'}">
          <div class="settings-main-user-avatar">
            <img class="settings-main-user-avatar-img" :src="avatarUrl(userAvatar, 50)" width="50" height="50">
          </div>
          <div class="settings-main-user-block">
            <div class="settings-main-user-name">{{ userName }}</div>
            <div class="settings-main-user-login">{{ userLogin }}</div>
          </div>
        </q-item>

        <q-item-label class="settings-main-label">{{ $t('pages.settings.settings.groups.service') }}</q-item-label>

        <q-item class="settings-main-item cursor-pointer" clickable @click="sync">
          <q-item-section>
            <q-field borderless class="settings-main-item-field cursor-pointer" bottom-slots>
              <template v-slot:control>
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('pages.settings.sync.menu_item') }}</div>
              </template>
              <template v-slot:append>
                <q-icon class="settings-main-item-field-arrow" name="sync" />
              </template>
              <template v-slot:hint>
                {{ lastSyncAt }}
              </template>
            </q-field>
          </q-item-section>
        </q-item>

        <q-item class="settings-main-item cursor-pointer" clickable :to="{ name: 'settingsConnections'}">
          <q-item-section>
            <q-field borderless class="settings-main-item-field cursor-pointer">
              <template v-slot:control>
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('modules.connections.pages.settings.menu_item') }}</div>
              </template>
              <template v-slot:append>
                <q-icon class="settings-main-item-field-arrow" name="keyboard_arrow_right" />
              </template>
            </q-field>
          </q-item-section>
        </q-item>

        <q-item class="settings-main-item cursor-pointer" clickable :to="{ name: 'settingsBudgets'}">
          <q-item-section>
            <q-field borderless class="settings-main-item-field cursor-pointer">
              <template v-slot:control>
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('modules.budget.page.settings.menu_item') }}</div>
              </template>
              <template v-slot:append>
                <q-icon class="settings-main-item-field-arrow" name="keyboard_arrow_right" />
              </template>
            </q-field>
          </q-item-section>
        </q-item>

        <q-item class="settings-main-item cursor-pointer" clickable :to="{ name: 'settingsAccounts'}">
          <q-item-section>
            <q-field borderless class="settings-main-item-field cursor-pointer">
              <template v-slot:control>
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('pages.settings.accounts.menu_item') }}</div>
              </template>
              <template v-slot:append>
                <q-icon class="settings-main-item-field-arrow" name="keyboard_arrow_right" />
              </template>
            </q-field>
          </q-item-section>
        </q-item>

        <q-item class="settings-main-item cursor-pointer" clickable :to="{ name: 'settingsCategories'}">
          <q-item-section>
            <q-field borderless class="settings-main-item-field cursor-pointer">
              <template v-slot:control>
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('modules.classifications.categories.pages.settings.menu_item') }}</div>
              </template>
              <template v-slot:append>
                <q-icon class="settings-main-item-field-arrow" name="keyboard_arrow_right" />
              </template>
            </q-field>
          </q-item-section>
        </q-item>

        <q-item class="settings-main-item cursor-pointer" clickable :to="{ name: 'settingsPayees'}">
          <q-item-section>
            <q-field borderless class="settings-main-item-field cursor-pointer">
              <template v-slot:control>
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('modules.classifications.payees.pages.settings.menu_item') }}</div>
              </template>
              <template v-slot:append>
                <q-icon class="settings-main-item-field-arrow" name="keyboard_arrow_right" />
              </template>
            </q-field>
          </q-item-section>
        </q-item>

        <q-item class="settings-main-item cursor-pointer" clickable :to="{ name: 'settingsTags'}">
          <q-item-section>
            <q-field borderless class="settings-main-item-field cursor-pointer">
              <template v-slot:control>
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('modules.classifications.tags.pages.settings.menu_item') }}</div>
              </template>
              <template v-slot:append>
                <q-icon class="settings-main-item-field-arrow" name="keyboard_arrow_right" />
              </template>
            </q-field>
          </q-item-section>
        </q-item>

        <q-item class="settings-main-item cursor-pointer" clickable @click="openImportCsvModal">
          <q-item-section>
            <q-field borderless class="settings-main-item-field cursor-pointer">
              <template v-slot:control>
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('pages.settings.import_csv.menu_item') }}</div>
              </template>
              <template v-slot:append>
                <q-icon class="settings-main-item-field-arrow" name="upload_file" />
              </template>
            </q-field>
          </q-item-section>
        </q-item>

        <q-item class="settings-main-item cursor-pointer" clickable @click="exportTransactions">
          <q-item-section>
            <q-field borderless class="settings-main-item-field cursor-pointer">
              <template v-slot:control>
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ $t('pages.settings.export_csv.menu_item') }}</div>
              </template>
              <template v-slot:append>
                <q-icon class="settings-main-item-field-arrow" name="download" />
              </template>
            </q-field>
          </q-item-section>
        </q-item>

        <q-item class="settings-main-item cursor-pointer" clickable>
          <q-item-section>
            <currency-select
              v-model="currency"
              :label="$t('pages.settings.currency.menu_item')"
              custom-class="settings-main-item-field-currency settings-main-item-field cursor-pointer settings-main-item-field-control"
              :borderless="true"
              :option-disable="(item) => !item"
              dropdown-icon="keyboard_arrow_down"
              popup-content-class="modal-popup"
              options-selected-class="modal-popup-selected"
            >
              <template v-slot:selected-item="scope">
                <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ scope.opt.label }}</div>
              </template>
            </currency-select>
          </q-item-section>
        </q-item>

        <template v-if="localeOptions.length > 1">
          <q-item-label class="settings-main-label">{{ $t('pages.settings.settings.groups.user_interface') }}</q-item-label>

          <q-item class="settings-main-item cursor-pointer" clickable @click="openLanguageModal">
            <q-item-section>
              <q-field borderless class="settings-main-item-field cursor-pointer" :label="$t('pages.settings.language.menu_item')" stack-label>
                <template v-slot:control>
                  <div class="settings-main-item-field-control self-center full-width no-outline" tabindex="0">{{ fullLanguageLabel() }}</div>
                </template>
                <template v-slot:append>
                  <q-icon class="settings-main-item-field-arrow" name="keyboard_arrow_down" />
                </template>
              </q-field>
            </q-item-section>
          </q-item>
        </template>
      </q-list>
    </div>

    <teleport to="body">
      <change-language-modal v-if="isLanguageModalOpened" v-on:cancel="closeLanguageModal()" />
      <import-csv-modal v-if="isImportCsvModalOpened" v-on:cancel="closeImportCsvModal()" v-on:import="handleImport" />
      <export-csv-modal v-if="isExportCsvModalOpened" :selected-ids="selectedAccountIds" v-on:update-selected="value => selectedAccountIds = value" v-on:close="closeExportCsvModal()" v-on:submit="handleExport" />
    </teleport>
  </q-page>
</template>

<script>
import {defineComponent} from 'vue';
import {date} from 'quasar';
import {navigationMixin} from '../../mixins/navigationMixin';
import {languageModalMixin} from '../../mixins/languageModalMixin';
import ChangeLanguageModal from '../../components/ChangeLanguageModal.vue';
import ImportCsvModal from '../../components/ImportCsvModal.vue';
import ExportCsvModal from '../../components/ExportCsvModal.vue';
import { mapState } from 'pinia'
import {useUsersStore} from 'stores/users';
import {useActiveAreaStore} from 'stores/active-area';
import {useAccountsStore} from 'stores/accounts';
import {useAccountFoldersStore} from 'stores/account-folders';
import {useCategoriesStore} from 'stores/categories';
import {usePayeesStore} from 'stores/payees';
import {useTagsStore} from 'stores/tags';
import {useTransactionsStore} from 'stores/transactions';
import {useCurrenciesStore} from 'stores/currencies';
import {useSyncStore} from 'stores/sync';
import { econumoPackage } from '../../modules/package';
import config from '../../modules/config';
import { useAvatar } from '../../composables/useAvatar';
import CurrencySelect from '../../components/CurrencySelect.vue';
import transactionApi from '../../modules/api/v1/transaction';

export default defineComponent({
  name: 'SettingsSettingsPage',
  mixins: [navigationMixin, languageModalMixin],
  components: {
    ChangeLanguageModal,
    ImportCsvModal,
    ExportCsvModal,
    CurrencySelect
  },
  setup() {
    const { avatarUrl } = useAvatar();
    return { avatarUrl };
  },
  data() {
    return {
      isImportCsvModalOpened: false,
      isExportCsvModalOpened: false,
      selectedAccountIds: []
    };
  },
  created() {
    if (this.$router.currentRoute.value.name === 'settings') {
      useActiveAreaStore().setWorkspaceActiveArea();
    }
  },
  computed: {
    ...mapState(useUsersStore, ['userAvatar', 'userName', 'userLogin', 'userCurrency']),
    ...mapState(useAccountsStore, ['accountsLoadedAt']),
    ...mapState(useAccountFoldersStore, ['accountFoldersLoadedAt']),
    ...mapState(useCategoriesStore, ['categoriesLoadedAt']),
    ...mapState(usePayeesStore, ['payeesLoadedAt']),
    ...mapState(useTagsStore, ['tagsLoadedAt']),
    ...mapState(useTransactionsStore, ['transactionsLoadedAt']),
    ...mapState(useCurrenciesStore, ['currenciesLoadedAt', 'currencies']),
    lastSyncAt: function () {
      let lastLoadedAt = null;
      [this.accountsLoadedAt, this.accountFoldersLoadedAt, this.categoriesLoadedAt, this.payeesLoadedAt, this.tagsLoadedAt, this.transactionsLoadedAt, this.currenciesLoadedAt].forEach(item => {
        if (lastLoadedAt === null && item) {
          lastLoadedAt = item;
        }
        if (item && date.extractDate(item, 'YYYY-MM-DD HH:mm:ss') < date.extractDate(lastLoadedAt, 'YYYY-MM-DD HH:mm:ss')) {
          lastLoadedAt = item;
        }
      });
      return lastLoadedAt || '-';
    },
    currency: {
      get() {
        const currency = this.userCurrency;
        if (!currency) {
          return null;
        }
        return {
          label: currency.code,
          value: currency.id
        };
      },
      set(item) {
        if (item) {
          const currency = this.currencies.find(c => c.id === item.value);
          if (currency) {
            useUsersStore().userUpdateCurrency({currency: currency.code});
          }
        }
      }
    },
    localeOptions: function () {
      return config.getLocaleOptions()
    },
    econumoPackage: function() {
      return econumoPackage;
    }
  },
  methods: {
    sync: function () {
      useSyncStore().fetchAll();
    },
    fullLanguageLabel: function () {
      const locale = config.getLocaleOptions().find(item => item.value === config.locale());
      return locale ? locale.label : '';
    },
    openImportCsvModal: function () {
      this.isImportCsvModalOpened = true;
    },
    closeImportCsvModal: function () {
      this.isImportCsvModalOpened = false;
    },
    handleImport: function (config) {
      console.log('Import CSV:', config);
      this.closeImportCsvModal();
    },
    openExportCsvModal: function () {
      // Initialize with only own account IDs selected by default
      const accountsStore = useAccountsStore();
      const usersStore = useUsersStore();
      this.selectedAccountIds = accountsStore.accountsOrdered
        .filter(account => account.owner.id === usersStore.userId)
        .map(account => account.id);
      this.isExportCsvModalOpened = true;
    },
    closeExportCsvModal: function () {
      this.isExportCsvModalOpened = false;
    },
    handleExport: function (config) {
      transactionApi.exportTransactionList(
        config.accountIds,
        (response) => {
          // Create a download link
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;

          // Generate filename with current date
          const now = new Date();
          const dateStr = date.formatDate(now, 'YYYY-MM-DD');
          link.setAttribute('download', `transactions-${dateStr}.csv`);

          // Trigger download
          document.body.appendChild(link);
          link.click();

          // Cleanup
          link.remove();
          window.URL.revokeObjectURL(url);

          this.closeExportCsvModal();
        },
        (error) => {
          console.error('Error exporting transactions:', error);
          this.$q.notify({
            type: 'negative',
            message: this.$t('pages.settings.export_csv.error') || 'Failed to export transactions'
          });
        }
      );
    },
    exportTransactions: function () {
      this.openExportCsvModal();
    }
  }
})
</script>

