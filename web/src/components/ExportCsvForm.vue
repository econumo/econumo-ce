<template>
  <div class="budget-form-wrapper">
    <div class="budget-form-wrapper-accounts-header-container">
      <h5 class="budget-form-wrapper-accounts-header">{{ $t('modules.export_csv.modal.export_csv_form.accounts') }}</h5>
      <a class="budget-form-wrapper-accounts-select-all" @click="toggleSelectAll">
        {{ allSelected ? $t('modules.export_csv.modal.export_csv_form.deselect_all') : $t('modules.export_csv.modal.export_csv_form.select_all') }}
      </a>
    </div>
    <div class="budget-form-wrapper-accounts">
      <div class="budget-form-wrapper-accounts-item-wrapper" v-for="account in accounts" :key="account.id">
        <q-item-section class="budget-form-wrapper-accounts-item-avatar" avatar>
          <q-icon class="budget-form-wrapper-accounts-item-avatar-icon" :name="account.icon" />
        </q-item-section>
        <div class="budget-form-wrapper-accounts-item-info">
          <q-item-section class="budget-form-wrapper-accounts-item-name">
            {{ account.name }}
          </q-item-section>
          <div class="budget-form-wrapper-accounts-item-info-container">
            <q-item-section class="budget-form-wrapper-accounts-item-balance">
              {{ moneyFormat(account.balance, account.currency.id, true, true) }}
            </q-item-section>
          </div>
          <q-item-section v-if="hasSharedAccounts" class="budget-form-wrapper-accounts-item-owner">
            {{ account.ownerName }}
          </q-item-section>
        </div>
        <q-item-section class="budget-form-wrapper-accounts-item-toggle" side>
          <q-toggle :model-value="account.selected" @click="toggleAccount(account.id, !account.selected)" />
        </q-item-section>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useAccountsStore } from 'stores/accounts';
import { useUsersStore } from 'stores/users';
import { Id } from '../modules/types';
import { includes, remove } from 'lodash-es';
import { useMoney } from '../composables/useMoney';

const { moneyFormat } = useMoney();

defineOptions({
  name: 'ExportCsvForm'
});

const props = withDefaults(defineProps<{
  selectedIds: Id[]
}>(), {
  selectedIds: () => []
});

const emit = defineEmits<{
  'update-selected': [value: Id[]]
}>();

const accountStore = useAccountsStore();
const usersStore = useUsersStore();

const selectedAccountIds = ref<Id[]>([...(props.selectedIds || [])]);

// Keep selectedAccountIds in sync with props
watch(() => props.selectedIds, (newValue) => {
  selectedAccountIds.value = [...(newValue || [])];
}, { deep: true });

interface ExportAccount {
  id: Id;
  name: string;
  icon: string;
  balance: number;
  currency: {
    id: Id;
    symbol: string;
    [key: string]: any;
  };
  ownerName: string;
  selected: boolean;
}

const hasSharedAccounts = computed(() => {
  if (!accountStore.accountsOrdered) return false;
  return accountStore.accountsOrdered.some(account => account.owner.id !== usersStore.userId);
});

const allSelected = computed(() => {
  if (!accountStore.accountsOrdered || accountStore.accountsOrdered.length === 0) return false;
  return selectedAccountIds.value.length === accountStore.accountsOrdered.length;
});

const accounts = computed(() => {
  const result: Array<ExportAccount> = [];
  if (!accountStore.accountsOrdered) return result;

  accountStore.accountsOrdered.forEach((account) => {
    const { id, name, icon, balance, currency, owner } = account;
    const selected = includes(selectedAccountIds.value, id);
    const ownerName = owner.id === usersStore.userId ? usersStore.userName : owner.name;
    const item: ExportAccount = { id, name, icon, balance, currency, ownerName, selected };
    result.push(item);
  });
  return result;
});

function toggleAccount(accountId: Id, select: boolean) {
  const isSelected = includes(selectedAccountIds.value, accountId);
  if (!isSelected && select) {
    selectedAccountIds.value.push(accountId);
  } else if (isSelected && !select) {
    remove(selectedAccountIds.value, (item) => item === accountId);
  }
  emit('update-selected', selectedAccountIds.value);
}

function toggleSelectAll() {
  if (allSelected.value) {
    // Deselect all
    selectedAccountIds.value = [];
  } else {
    // Select all
    selectedAccountIds.value = accountStore.accountsOrdered.map(account => account.id);
  }
  emit('update-selected', selectedAccountIds.value);
}
</script>

<style scoped lang="scss">
.budget-form-wrapper-accounts-header-container {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-top: 4px;
  margin-bottom: 5px;

  .budget-form-wrapper-accounts-header {
    margin-bottom: 0;
  }
}

.budget-form-wrapper-accounts-select-all {
  text-transform: uppercase;
  font-style: normal;
  font-weight: 400;
  font-size: 14px;
  line-height: 18px;
  color: #B366CC;
  cursor: pointer;
  text-decoration: none;
  white-space: nowrap;

  &:hover {
    color: #9933CC;
    text-decoration: underline;
  }
}

.budget-form-wrapper-accounts-item-owner {
  font-style: normal;
  font-weight: 500;
  font-size: 14px;
  line-height: 16px;
  color: #808080;
  font-style: italic;
  margin-left: 4px !important;
}
</style>
