<template>
  <div class="budget-expense-widget" v-if="props.currencyId && budget">
    <div class="row budget-expense-widget-header">
      {{ $t('modules.budget.modal.expense_widget.header', { period: budgetDate }) }}
    </div>
    <div class="row budget-expense-widget-numbers">
      <div class="col budget-expense-widget-numbers-progress expense-color"
           v-html="moneyHTML(spent, props.currencyId)"></div>
      <div class="col budget-expense-widget-numbers-total"
           v-html="moneyHTML(total, props.currencyId)"></div>
    </div>
    <div class="row budget-expense-widget-progress">
      <q-linear-progress :value="progress"
                        :class="'budget-expense-widget-progress-bar ' + (isOverSpent ? 'budget-expense-widget-progress-bar-overbudget' : '')" />
    </div>
             <div class="row budget-expense-widget-hint"
                   v-if="props.currencyId !== budgetCurrencyId">
               {{ $t('modules.budget.modal.expense_widget.conversion_rate', {
                period: averageCurrencyRate[0],
                defaultCurrency: budgetCurrency?.symbol || '',
                rate: moneyFormat(averageCurrencyRate[1], props.currencyId)
              }) }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, Ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useMoney } from '../../composables/useMoney';
import { useCurrency } from '../../composables/useCurrency';
import { useBudgetsStore } from '../../stores/budgets';
import { useCurrenciesStore } from '../../stores/currencies';
import { Id, DateString } from '@shared/types';
import { BudgetCurrencyRateDto, BudgetBalanceDto } from '../../modules/api/v1/dto/budget.dto';

defineOptions({
  name: 'BudgetExpenseWidget'
});

const props = withDefaults(defineProps<{
  currencyId?: Id | null;
}>(), {
  currencyId: null
});

const {moneyFormat, moneyHTML} = useMoney();
const { exchange } = useCurrency();
const budgetStore = useBudgetsStore();
const currenciesStore = useCurrenciesStore();
const { t } = useI18n();
const getCurrency = (currencyId: Id) => {
    return currenciesStore.currenciesHash[currencyId];
};

const budget = computed(() => budgetStore.budget);
const budgetCurrencyId = computed(() => budget.value?.meta.currencyId);
const budgetCurrency = computed(() => {
    if (!budget.value) return null;
    return getCurrency(budget.value.meta.currencyId);
});
const budgetDate = computed(() => {
    const rawDate = budgetStore.budgetDate;
    if (!rawDate) return '';

    return getPeriod(rawDate);
});

function getPeriod(dateStr: DateString) {
    const [year, month] = dateStr.split('-');
    const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
    const monthName = t(`elements.date.month_short.${months[parseInt(month) - 1]}`);
    return `${monthName} ${year}`;
}

const total: Ref<number> = computed(() => {
  if (!budget.value) return 0;

  let budgetBalance: BudgetBalanceDto | null = null;
  (budget.value.balances as BudgetBalanceDto[]).forEach(balance => {
    if (balance.currencyId === props.currencyId) {
        budgetBalance = balance;
    }
  });
  if (!budgetBalance) return 0;

  let result = Math.abs(Number((budgetBalance as BudgetBalanceDto).startBalance) + Number((budgetBalance as BudgetBalanceDto).income));
  if (Number((budgetBalance as BudgetBalanceDto).exchanges) > 0) {
    result += Number((budgetBalance as BudgetBalanceDto).exchanges);
  }
  if (Number((budgetBalance as BudgetBalanceDto).holdings) > 0) {
    result += Number((budgetBalance as BudgetBalanceDto).holdings);
  }
  return result;
});

const spent: Ref<number> = computed(() => {
    if (!budget.value) return 0;

  let budgetBalance: BudgetBalanceDto | null = null;
  (budget.value.balances as BudgetBalanceDto[]).forEach(balance => {
    if (balance.currencyId === props.currencyId) {
        budgetBalance = balance;
    }
  });
  if (!budgetBalance) return 0;

  let result = Math.abs(Number((budgetBalance as BudgetBalanceDto).expenses));
  if (Number((budgetBalance as BudgetBalanceDto).exchanges) < 0) {
    result += Math.abs(Number((budgetBalance as BudgetBalanceDto).exchanges));
  }
  if (Number((budgetBalance as BudgetBalanceDto).holdings) < 0) {
    result += Math.abs(Number((budgetBalance as BudgetBalanceDto).holdings));
  }
  return result;
});

const averageCurrencyRate: Ref<[string | null, number]> = computed(() => {
    if (!budget.value) return [null, 0];

    let budgetCurrencyRate: BudgetCurrencyRateDto | null = null;
    budget.value.currencyRates.forEach(currencyRate => {
        if (currencyRate.currencyId === props.currencyId) {
            budgetCurrencyRate = currencyRate;
        }
    });
    if (!budgetCurrencyRate) return [null, 0];

    return [
        getPeriod((budgetCurrencyRate as BudgetCurrencyRateDto).periodStart),
        budget.value?.meta.currencyId && props.currencyId ? exchange(budget.value.meta.currencyId, props.currencyId, 1, budget.value.currencyRates) : 0
    ];
});

const progress = computed(() => {
  if (total.value <= 0) return 0;
  return Math.max(0, Math.min(spent.value / total.value, 1));
});

const isOverSpent = computed(() => {
  return spent.value > total.value;
});
</script>
