<template>
  <q-page>
    <div class="settings-classification budget">
      <!-- toolbar for mobile -->
      <div class="settings-toolbar-mobile settings-classification-toolbar-mobile">
        <div>
          <q-btn class="settings-toolbar-mobile-button" flat icon="arrow_back" @click="navigateToHome(true)" />
        </div>
        <div>
          <h4 class="settings-toolbar-mobile-title">{{ $t('modules.user.pages.onboarding.header') }}</h4>
        </div>
        <div class="settings-toolbar-mobile-container">
          &nbsp;
        </div>
      </div>


      <!-- toolbar for desktop -->
      <div class="budget-toolbar-desktop">
        <div class="budget-toolbar-desktop-head">
          <h4 class="budget-toolbar-desktop-head-title">
            {{ $t('modules.user.pages.onboarding.header') }}
          </h4>
        </div>
        <div class="budget-toolbar-desktop-settings">
          &nbsp;
        </div>
      </div>


      <q-timeline color="secondary" class="onboarding">
        <q-timeline-entry heading>
            {{ $t('modules.user.pages.onboarding.title') }}
        </q-timeline-entry>

        <q-timeline-entry :color="isAccountCreated ? 'econumo-purple' : 'econumo-grey'" :icon="isAccountCreated ? 'check' : 'arrow_forward_ios'">
            <template v-slot:title>
                Add your accounts
            </template>
            <template v-slot:subtitle>
                <a href="https://econumo.com/docs/user-guide/accounts" target="_blank">{{ $t('modules.user.pages.onboarding.user_guide.accounts') }}</a>
            </template>
            <div>
                <div>
                    To start, you can add an account by clicking the button below. 
                    Alternatively, you can always navigate to the <router-link :to="{ name: 'settings' }" class="text-weight-medium">{{ $t('pages.settings.settings.menu_item') }}</router-link> -> <router-link :to="{ name: 'settingsAccounts' }" class="text-weight-medium">{{ $t('pages.settings.accounts.menu_item') }}</router-link> page to manage your accounts and arrange them into folders.
                </div>
                <q-btn class="econumo-btn -small -yellow settings-classification-toolbar-control-btn q-mt-sm" flat
                        :label="$t('modules.user.pages.onboarding.add_account')" @click="openCreateAccountModal()" />
            </div>
        </q-timeline-entry>

        <q-timeline-entry :color="isTransactionsEntered ? 'econumo-purple' : 'econumo-grey'" :icon="isTransactionsEntered ? 'check' : 'arrow_forward_ios'">
            <template v-slot:title>
                Enter your first transaction
            </template>
            <template v-slot:subtitle>
                <a href="https://econumo.com/docs/user-guide/transactions" target="_blank">{{ $t('modules.user.pages.onboarding.user_guide.transactions') }}</a>
            </template>
            <div>
                <div>
                    You can enter transactions by selecting any account in the left sidebar and clicking the <span class="text-weight-medium">Add Transaction</span> button.<br>
                    You can create categories, tags, and payees directly from the transaction modal by entering their names and pressing Enter.
                </div>
                <q-btn class="econumo-btn -small -yellow settings-classification-toolbar-control-btn q-mt-sm" flat
                        :label="$t('modules.user.pages.onboarding.import_transactions')" @click="openImportCsvModal()" />
            </div>
        </q-timeline-entry>

        <q-timeline-entry :color="isClassificationsCreated ? 'econumo-purple' : 'econumo-grey'" :icon="isClassificationsCreated ? 'check' : 'lightbulb'">
            <template v-slot:title>
                Manage categories, tags, and payees
            </template>
            <template v-slot:subtitle>
                <a href="https://econumo.com/docs/user-guide/classifications" target="_blank">{{ $t('modules.user.pages.onboarding.user_guide.classifications') }}</a>
            </template>
            <div>
                To manage categories, tags, and payees, navigate to <router-link :to="{ name: 'settings' }" class="text-weight-medium">{{ $t('pages.settings.settings.menu_item') }}</router-link> -> <router-link :to="{ name: 'settingsCategories' }" class="text-weight-medium">{{ $t('modules.classifications.categories.pages.settings.menu_item') }}</router-link>, <router-link :to="{ name: 'settingsTags' }" class="text-weight-medium">{{ $t('modules.classifications.tags.pages.settings.menu_item') }}</router-link>, or <router-link :to="{ name: 'settingsPayees' }" class="text-weight-medium">{{ $t('modules.classifications.payees.pages.settings.menu_item') }}</router-link>. You can also sort or archive them as necessary.
            </div>
        </q-timeline-entry>

        <q-timeline-entry color="econumo-grey" :avatar="userAvatar ? avatarUrl(userAvatar, 30) : ''">
            <template v-slot:title>
                Update your avatar
            </template>
            <template v-slot:subtitle>
                <a href="https://econumo.com/docs/user-guide/user-profile" target="_blank">{{ $t('modules.user.pages.onboarding.user_guide.user_profile') }}</a>
            </template>
            <div>
                  Econumo pulls your avatar from <a href="https://gravatar.com" target="_blank" rel="nofollow">Gravatar</a>, linked to your email address. To change your avatar, please visit <a href="https://gravatar.com" target="_blank" rel="nofollow">Gravatar</a>.
            </div>
        </q-timeline-entry>

        <q-timeline-entry :color="isConnectionsEstablished ? 'econumo-purple' : 'econumo-grey'" :icon="isConnectionsEstablished ? 'check' : 'arrow_forward_ios'">
            <template v-slot:title>
                Connect with your partner
            </template>
            <template v-slot:subtitle>
                <a href="https://econumo.com/docs/user-guide/shared-access" target="_blank">{{ $t('modules.user.pages.onboarding.user_guide.shared_access') }}</a>
            </template>
            <div>
                To connect with your partner and manage shared access to your budget or accounts, please visit <router-link :to="{ name: 'settings' }" class="text-weight-medium">{{ $t('pages.settings.settings.menu_item') }}</router-link> -> <router-link :to="{ name: 'settingsConnections' }" class="text-weight-medium">{{ $t('modules.connections.pages.settings.menu_item') }}</router-link>.
            </div>
        </q-timeline-entry>

        <q-timeline-entry :color="isBudgetCreated ? 'econumo-purple' : 'econumo-grey'" :icon="isBudgetCreated ? 'check' : 'arrow_forward_ios'">
            <template v-slot:title>
                Create your budget
            </template>
            <template v-slot:subtitle>
                <a href="https://econumo.com/docs/user-guide/budgets" target="_blank">{{ $t('modules.user.pages.onboarding.user_guide.budgets') }}</a>
            </template>
            <div>
                You can create your first budget on the <router-link :to="{ name: 'budget' }" class="text-weight-medium">{{ $t('blocks.main.budget') }}</router-link> page.<br>
                Additionally, you can access the <router-link :to="{ name: 'settings' }" class="text-weight-medium">{{ $t('pages.settings.settings.menu_item') }}</router-link> -> <router-link class="text-weight-medium" :to="{ name: 'settingsBudgets' }">{{ $t('modules.budget.page.settings.menu_item') }}</router-link> page to manage your budgets, shared access, and more.
            </div>
            <q-btn class="econumo-btn -small -magenta budget-toolbar-control-btn q-mt-sm" flat
                  :label="$t('modules.user.pages.onboarding.complete')" @click="completeOnboarding" />
        </q-timeline-entry>
      </q-timeline>
    </div>
  </q-page>

  <teleport to="body">
    <account-modal></account-modal>
    <ImportCsvModal v-if="showImportCsvModal" @cancel="showImportCsvModal = false" @import="handleImportComplete" />
  </teleport>
</template>

<script setup lang="ts">
import { defineOptions, onMounted, watch, ref } from 'vue'
import { computed } from 'vue';
import { useAccountsStore } from 'stores/accounts';
import { useCategoriesStore } from 'stores/categories';
import { useTagsStore } from 'stores/tags';
import { usePayeesStore } from 'stores/payees';
import { useUsersStore } from 'stores/users';
import { useTransactionsStore } from 'stores/transactions';
import { useConnectionsStore } from 'stores/connections';
import { useBudgetsStore } from 'stores/budgets';
import { useActiveAreaStore } from 'stores/active-area';
import { useRoute, useRouter } from 'vue-router';
import { RouterPage } from '../router/constants';
import type { RouteLocationNormalized } from 'vue-router';
import { useAccountModalStore } from 'stores/account-modal';
import { useAccountFoldersStore } from 'stores/account-folders';
import AccountModal from '../components/AccountModal.vue';
import ImportCsvModal from '../components/ImportCsvModal.vue';
import { useAvatar } from '../composables/useAvatar';

defineOptions({
  name: 'OnboardingPage'
})

const router = useRouter();
const route = useRoute();
const accountsStore = useAccountsStore();
const categoriesStore = useCategoriesStore();
const tagsStore = useTagsStore();
const payeesStore = usePayeesStore();
const usersStore = useUsersStore();
const transactionsStore = useTransactionsStore();
const connectionsStore = useConnectionsStore();
const budgetsStore = useBudgetsStore();
const isReadyForBudget = computed(() => 
  accountsStore.accountsOrdered.length > 0 && 
  categoriesStore.categoriesOrdered.length > 0
);
const isAccountCreated = computed(() => accountsStore.accountsOrdered.length > 0);
const isTransactionsEntered = computed(() => 
  accountsStore.accountsOrdered.length > 0 && 
  categoriesStore.categoriesOrdered.length > 0 &&
  transactionsStore.transactionsOrdered.length > 0
);
const userAvatar = computed(() => usersStore.userAvatar);
const isConnectionsEstablished = computed(() => connectionsStore.connections.length > 0);
const isBudgetCreated = computed(() => budgetsStore.budgets.length > 0);
const isClassificationsCreated = computed(() => categoriesStore.categoriesOrdered.length > 0 && (tagsStore.tagsOrdered.length > 0 || payeesStore.payeesOrdered.length > 0));

// Import CSV modal state
const showImportCsvModal = ref(false);

function openImportCsvModal() {
  showImportCsvModal.value = true;
}

function handleImportComplete() {
  showImportCsvModal.value = false;
}

function completeOnboarding() {
  usersStore.userCompleteOnboarding().then(() => {
    router.push({ name: RouterPage.BUDGET });
  });
}

const { avatarUrl } = useAvatar();

onMounted(() => {
  const currentRouteName = router.currentRoute.value.name;
  useActiveAreaStore().setWorkspaceActiveArea();
  if (currentRouteName === RouterPage.ONBOARDING) {
    useActiveAreaStore().setWorkspaceActiveArea();
  } else if (currentRouteName === RouterPage.HOME) {
    useActiveAreaStore().setSidebarActiveArea();
  }
});

watch(
  () => route,
  (to: RouteLocationNormalized) => {
    if (to.name === RouterPage.ONBOARDING) {
      useActiveAreaStore().setWorkspaceActiveArea();
    } else if (to.name === RouterPage.HOME) {
      useActiveAreaStore().setSidebarActiveArea();
    }
  },
  { immediate: true, deep: true }
);

function navigateToHome(isSidebarActive = false) {
  if (isSidebarActive) {
    useActiveAreaStore().setSidebarActiveArea();
  } else {
    useActiveAreaStore().setWorkspaceActiveArea();
  }
  router.push({ name: RouterPage.HOME });
}

function openCreateAccountModal() {
  const accountModalStore = useAccountModalStore();
  const accountFoldersStore = useAccountFoldersStore();
  const firstFolder = accountFoldersStore.accountFoldersOrdered[0];
  accountModalStore.openAccountModal({ folderId: firstFolder?.id ?? null });
}

</script>

