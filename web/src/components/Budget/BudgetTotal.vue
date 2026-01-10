<template>
  <div class="budget-total">
    <hr class="budget-total-separator" />
    <div :class="'budget-table-row-element' + ($q.screen.lt.md ? ' budget-table-row-element-with-children' : '')">
      <div class="row budget-table-row">
        <div :class="'budget-table-column-name budget-table-row-element-name' + ($q.screen.lt.md ? ' budget-table-row-element-name-with-children' : '')">
          <div class="budget-table-row-item-name-wrapper" v-if="$q.screen.lt.md">
            <div class="budget-table-row-item-name-wrapper-name">{{ $t('modules.budget.page.budget.structure.total.name') }}</div>
            <div class="budget-table-row-item-name-wrapper-budgeted" v-html="moneyHTML(budgetStats.budgeted, budgetCurrencyId, false, true)"></div>
          </div>
          <span class="budget-table-row-item-name-single" v-else>{{ $t('modules.budget.page.budget.structure.total.name') }}</span>
        </div>
        <div class="budget-table-column-budget">
          <div class="budget-table-column-budget-row" v-html="moneyHTML(budgetStats.budgeted, budgetCurrencyId, false, true)"></div>
        </div>
        <div class="budget-table-column-spent">
          <div class="budget-table-column-spent-row" v-html="moneyHTML(budgetStats.spent * -1, budgetCurrencyId, false, true)"></div>
        </div>
        <div class="budget-table-column-available">
          <div
            :class="'budget-table-column-available-row ' + (budgetStats.available >= 0 ? 'income-color' : 'expense-color')"
            v-html="moneyHTML(budgetStats.available, budgetCurrencyId, false, true)"></div>
        </div>
        <div class="budget-table-column-actions">
          <div class="budget-table-column-actions-currency">
            {{ budgetCurrency.symbol }}
          </div>
        </div>
      </div>
      <div class="budget-table-row-element-items" v-if="$q.screen.lt.md">
        <div class="row budget-table-row budget-table-row-item">
          <div class="budget-table-column-name budget-table-row-item-name">
            <div class="budget-table-row-item-name-single">
              <span class="budget-table-row-item-name-single">{{ $t('modules.budget.page.budget.structure.total.expenses') }}</span>
            </div>
          </div>
          <div class="budget-table-column-spent -cursor-help -inactive"
                v-html="moneyFormat(budgetStats.spent * -1, budgetCurrencyId, false, true)">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Id } from '@shared/types';
import { BudgetStatsDto } from '../../modules/api/v1/dto/budget.dto';
import { useCurrenciesStore } from '../../stores/currencies';
import { useMoney } from '../../composables/useMoney';
import { useQuasar } from 'quasar'

defineOptions({
  name: 'BudgetTotal'
});

const props = defineProps<{
  budgetStats: BudgetStatsDto,
  budgetCurrencyId: Id,
}>();

const $q = useQuasar()
const { moneyFormat, moneyHTML } = useMoney();

const currenciesStore = useCurrenciesStore();
const budgetCurrency = computed(() => currenciesStore.currenciesHash[props.budgetCurrencyId]);
</script>
