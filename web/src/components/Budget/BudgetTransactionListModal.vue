<template>
  <q-dialog class="responsive-modal" :model-value="true" @hide="cancel"
            :position="$q.screen.gt.sm ? 'standard' : 'bottom'" no-backdrop-dismiss>
    <q-card class="budget-transaction-list">
      <q-card-section class="budget-transaction-list-header">
        <div class="budget-transaction-list-header-name">{{ selectedElement.name }}</div>
        <div class="budget-transaction-list-header-amount"
             v-html="moneyHTML(selectedElement.spent, currencyId, true, true)">
        </div>
        <div class="budget-transaction-list-header-icon">
          <q-avatar :icon="selectedElement.icon" class="budget-transaction-list-header-icon-avatar" />
        </div>
      </q-card-section>

      <q-card-section v-if="isTransactionListLoading" class="budget-transaction-list-body">
        <div class="budget-transaction-list-body-loading">Loading...</div>
      </q-card-section>

      <q-card-section v-if="!isTransactionListLoading" class="budget-transaction-list-body">
        <div v-if="transactionList.length === 0" class="budget-transaction-list-body-empty">No transactions</div>
        <div v-if="transactionList.length > 0" class="budget-transaction-list-body-transactions">
          <div v-for="item in transactionMixin.methods.transactionsDailyList(transactionList, 'spentAt')" v-bind:key="item.id">
            <q-item-label class="budget-transaction-list-body-transactions-date" header v-if="item.isSeparator"
                          :key="item.id">
              <span class="budget-transaction-list-body-transactions-date-content" v-if="item.alias !== 'none'">
                {{ $t('pages.account.transaction_list.' + (item.alias)) }}
              </span>
              <span class="budget-transaction-list-body-transactions-date-content" v-else>{{ item.date }}</span>
            </q-item-label>

            <q-item class="budget-transaction-list-body-transactions-item" v-if="!item.isSeparator" :key="item.id"
                    :clickable="$q.screen.lt.lg">
              <div class="budget-transaction-list-body-transactions-item-category">
                <div class="budget-transaction-list-body-transactions-item-category-name">
                  {{ item.category?.name || '' }}
                </div>
                <div class="budget-transaction-list-body-transactions-item-category-description">
                  {{ item.description }}
                </div>
                <div class="budget-transaction-list-body-transactions-item-category-author" v-if="item.author?.id && access.length > 1">
                  <q-avatar class="budget-transaction-list-body-transactions-item-category-author-avatar">
                    <img :src="avatarUrl(item.author.avatar, 30)" :title="item.author.name" :alt="item.author.name" width="30" height="30" />
                  </q-avatar>
                  {{ item.author.name }}
                </div>
              </div>
              <div class="budget-transaction-list-body-transactions-item-payee">
                <div class="budget-transaction-list-body-transactions-item-payee-name" v-if="item.payee">
                  {{ item.payee.name || '' }}
                </div>
              </div>
              <div class="budget-transaction-list-body-transactions-item-amount">
                <div class="budget-transaction-list-body-transactions-item-amount-value" v-html="moneyHTML(item.amount * -1, item.currencyId, true, true)">
                </div>
              </div>
            </q-item>
          </div>
        </div>
      </q-card-section>
      <q-card-actions class="budget-transaction-list-footer">
        <q-btn class="econumo-btn -wide -large -grey preview-modal-account-info-access-actions-btn"
               :title="$t('elements.button.cancel.label')" flat icon="expand_more" v-close-popup @click="cancel" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { transactionMixin } from '../../mixins/transactionMixin';
import { DateString, Id } from '../../modules/types';
import { BudgetBaseElementDto } from 'modules/api/v1/dto/budget.dto';
import { useBudgetsStore } from 'stores/budgets';
import { UserAccessDto } from '@shared/dto/access.dto';
import { useQuasar } from 'quasar';
import { useAvatar } from '../../composables/useAvatar';
import { useMoney } from '../../composables/useMoney';

const props = defineProps<{
  budgetId: Id,
  periodStart: DateString,
  categoryId: Id | null,
  tagId: Id | null,
  envelopeId: Id | null,
  selectedElement: BudgetBaseElementDto,
  currencyId: Id,
  currentUserId: Id,
  access: UserAccessDto[]
}>();

const emit = defineEmits([
  'close'
]);

defineOptions({
  name: 'BudgetTransactionListModal'
});

const { moneyHTML } = useMoney();

const $q = useQuasar();

function cancel() {
  emit('close');
}


const budgetStore = useBudgetsStore();
const isTransactionListLoading = computed(() => budgetStore.isBudgetTransactionListLoading);
const transactionList = computed(() => budgetStore.budgetTransactionList);
budgetStore.fetchTransactionList({
  budgetId: props.budgetId,
  periodStart: props.periodStart,
  categoryId: props.categoryId ? props.categoryId : '',
  envelopeId: props.envelopeId ? props.envelopeId : '',
  tagId: props.tagId ? props.tagId : ''
});

const { avatarUrl } = useAvatar();

</script>

