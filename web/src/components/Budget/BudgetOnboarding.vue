<template>
  <div class="row">
    <div class="budget-empty">
      <!-- Initial setup needed -->
      <template v-if="!isReadyForBudget">
        <div>
          {{ $t('modules.budget.page.budget.empty.initial_setup') }}
        </div>
        <q-btn class="econumo-btn -small -magenta budget-toolbar-control-btn" flat v-if="!isAccountCreated"
               :label="$t('modules.budget.page.budget.empty.create_account')" @click="emit('createAccount')" />
      </template>

      <!-- Ready to create budget -->
      <template v-else>
        <div>
          {{ $t('modules.budget.page.budget.empty.no_budget') }}
        </div>
        <div>
          {{ $t('modules.budget.page.budget.empty.description') }}
        </div>
        <q-btn class="econumo-btn -small -magenta budget-toolbar-control-btn" flat
               :label="$t('modules.budget.page.budget.empty.create_budget')" @click="emit('createBudget')" />
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useAccountsStore } from 'stores/accounts';
import { useCategoriesStore } from 'stores/categories';

defineOptions({
  name: 'BudgetOnboarding'
});

const accountsStore = useAccountsStore();
const categoriesStore = useCategoriesStore();

const isReadyForBudget = computed(() => 
  accountsStore.accountsOrdered.length > 0 && 
  categoriesStore.categoriesOrdered.length > 0
);
const isAccountCreated = computed(() => accountsStore.accountsOrdered.length > 0);

const emit = defineEmits<{
  (e: 'createBudget'): void
  (e: 'createAccount'): void
}>();
</script>
