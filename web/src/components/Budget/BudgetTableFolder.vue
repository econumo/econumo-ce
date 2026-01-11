<template>
  <div :data-folder-id="folderId">
    <div class="row budget-table-row budget-table-row-folder">
      <div class="budget-table-column-name">
        {{ folderName }}
      </div>
      <div class="budget-table-column-subheader" v-if="!isEditMode && elements.length">
        <div class="budget-table-column-stat-title">
          {{ $t('modules.budget.page.budget.structure.tab.budgeted') }} /
          {{ $t('modules.budget.page.budget.structure.tab.spent') }} /
          {{ $t('modules.budget.page.budget.structure.tab.available') }}
        </div>
        <div class="budget-table-column-stat-values" v-if="budgetCurrency">
          <span :title="$t('modules.budget.page.budget.structure.tab.budgeted')" v-html="moneyHTML(stats.budgeted, budgetCurrencyId, true, true)" /> /
          <span :title="$t('modules.budget.page.budget.structure.tab.spent')" v-html="moneyHTML(stats.spent, budgetCurrencyId, true, true)" /> /
          <span :title="$t('modules.budget.page.budget.structure.tab.available')" v-html="moneyHTML(stats.available, budgetCurrencyId, true, true)" />
        </div>
      </div>
      <div class="budget-table-column-folder-actions" v-if="isEditMode">
        <div class="budget-table-row-folder-actions">
          <q-btn flat icon="control_point" class="budget-table-row-folder-actions-create" @click="openCreateEnvelopeModal()" />
          <q-btn flat icon="more_vert" class="budget-table-row-folder-actions-context context-menu-button-control">
            <q-menu cover auto-close>
              <q-list>
                <q-item :clickable="folderId !== null" :disable="folderId === null" class="context-menu-button-item"
                        @click="openUpdateFolderModal()">
                  <q-item-section class="context-menu-button-section">
                    {{ $t('elements.button.edit.label') }}
                  </q-item-section>
                </q-item>
                <q-item :clickable="!isFirst && folderId !== null" :disable="isFirst || !folderId"
                        class="context-menu-button-item" @click="orderFolders(-1)" v-if="folderId">
                  <q-item-section class="context-menu-button-section">
                    {{ $t('elements.button.up.label') }}
                  </q-item-section>
                </q-item>
                <q-item :clickable="!isLast && folderId !== null" :disable="isLast || !folderId" class="context-menu-button-item"
                        @click="orderFolders(1)" v-if="folderId">
                  <q-item-section class="context-menu-button-section">
                    {{ $t('elements.button.down.label') }}
                  </q-item-section>
                </q-item>
                <q-item :clickable="(!elements.length && !!folderId)" :disable="!!elements.length || !folderId"
                        class="context-menu-button-item">
                  <q-item-section class="context-menu-button-section -delete"
                                  @click="deleteFolder(folderId, !!elements.length)">
                    {{ $t('elements.button.delete.label') }}
                  </q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </div>
      </div>
    </div>

    <draggable :list="elements" group="elements" :disabled="!isEditMode" item-key="id" @end="orderElements"
               :component-data="{id: folderId}" handle=".sortable-control">
      <template #item="{element}">
        <div
          :class="'budget-table-row-element' + ((element.children.length || (element.type === 1 && $q.screen.lt.md)) && unfoldedElements[element.id] ? '' : ' budget-table-row-element-folded') + ((element.children.length || (element.type === 1 && $q.screen.lt.md)) ? ' budget-table-row-element-with-children' : '')  + (activeElementId === element.id ? ' budget-table-row-element-active' : '')"
        >
          <div
            class="row budget-table-row"
            :data-element-id="element.id"
            @touchstart="startTouch($event)"
            @touchmove="handleTouchMove"
            @touchend="handleTouchEnd"
          >
            <div
              :class="'budget-table-column-name budget-table-row-element-name' + ((element.children.length || (element.type === 1 && $q.screen.lt.md)) ? ' budget-table-row-element-name-with-children' : '')"
              @click="toggleElement(element)"
            >
              <div class="budget-table-row-element-icon -draggable draggable sortable-control" v-if="isEditMode">
                <q-avatar icon="drag_indicator" class="budget-table-row-element-icon-avatar" />
              </div>
              <div class="budget-table-row-element-icon" v-else>
                <q-avatar :icon="element.icon" class="budget-table-row-element-icon-avatar" />
                <q-avatar class="budget-table-row-element-icon-shared" v-if="$q.screen.gt.sm && element.ownerUserId && access.length > 1">
                  <img :src="avatarUrl(access[element.ownerUserId].user.avatar, 30)" width="30" height="30"
                       :title="access[element.ownerUserId].user.name" :alt="access[element.ownerUserId].user.name" />
                </q-avatar>
              </div>
              <div class="budget-table-row-item-name-wrapper" v-if="$q.screen.lt.md">
                <div class="budget-table-row-item-name-wrapper-name">{{ element.name }}</div>
                <div class="budget-table-row-item-name-wrapper-budgeted" v-html="moneyHTML(element.budgeted, element.currencyId, false, true)"></div>
              </div>
              <span class="budget-table-row-item-name-single" v-else>{{ element.name }}</span>
            </div>
            <div
              :class="'budget-table-column-budget' + (activeElementId === element.id ? ' budget-table-column-budget-active' : '') + (canUserUpdateLimits ? ' cursor-pointer' : ' -no-edit')">
              <div class="budget-table-column-budget-row" v-html="moneyHTML(element.budgeted, element.currencyId, false, true)"></div>
              <q-popup-edit :model-value="element" class="budget-table-column-budget-edit" auto-save v-slot="scope"
                            :disable="!canUserUpdateLimits || $q.screen.lt.md"
                            @before-show="openBudgetEdit(element)"
                            @before-hide="closeBudgetEdit">
                <form @submit.prevent="saveBudgetedValue(scope.cancel)">
                  <q-input
                    ref="budgetActiveElementInput"
                    :model-value="activeElementBudget.value"
                    @update:model-value="storeBudgetedValue"
                    dense
                    autofocus
                    inputmode="decimal"
                    pattern="[0-9+\-\*\.=,]*"
                    @keyup.enter.stop.prevent="saveBudgetedValue(scope.cancel)"
                    @keyup.esc.stop.prevent="scope.cancel();"
                    :rules="[
                      val => isValidNumber(val.toString()) || $t('elements.validation.invalid_number'),
                      val => isValidDecimalNumber(val.toString()) || $t('elements.validation.invalid_decimal_number'),
                      val => isValidFormula(val.toString()) || $t('elements.validation.invalid_formula')
                    ]">
                  </q-input>
                </form>
              </q-popup-edit>
            </div>
            <div class="budget-table-column-spent -cursor-help -inactive" @click="showTransactionList(element, null)">
              <div class="budget-table-column-spent-row"
                   v-html="moneyHTML(element.spent * -1, element.currencyId, false, true)"></div>
            </div>
            <div class="budget-table-column-available -inactive" @click="toggleElement(element)">
              <div
                :class="'budget-table-column-available-row ' + (element.available + element.budgeted >= 0 ? 'income-color' : 'expense-color')"
                v-html="moneyHTML(element.available + element.budgeted, element.currencyId, false, true)"></div>
            </div>
            <div :class="'budget-table-column-actions' + (!isEditMode ? '' : ' budget-table-column-actions-edit')" :id="'menu-' + element.id">
              <div class="budget-table-column-actions-currency" @click="toggleElement(element)">
                {{ getCurrencySymbol(element.currencyId) }}
              </div>
              <div class="budget-table-column-actions-menu">
                <q-btn flat icon="more_vert" class="context-menu-button-control">
                  <q-menu cover auto-close :target="'#menu-' + element.id">
                    <q-list>
                      <q-item :clickable="element.type !== 0" :disable="element.type === 0" v-if="element.type !== 0"
                              class="context-menu-button-item" @click="openChangeCurrencyModal(element)">
                        <q-item-section class="context-menu-button-section">
                          {{ $t('modules.budget.page.budget.structure.element.action.change_currency') }}
                        </q-item-section>
                      </q-item>
                      <q-item :clickable="element.type === 0" :disable="element.type !== 0"
                              class="context-menu-button-item" @click="openUpdateElementModal(element)">
                        <q-item-section class="context-menu-button-section">
                          {{ $t('elements.button.edit.label') }}
                        </q-item-section>
                      </q-item>
                      <q-item :clickable="element.type === 0" :disable="element.type !== 0" v-if="element.type === 0"
                              class="context-menu-button-item" @click="openDeleteElementModal(element)">
                        <q-item-section class="context-menu-button-section -delete">
                          {{ $t('elements.button.delete.label') }}
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </q-btn>
              </div>
            </div>
          </div>
          <div class="budget-table-row-element-items" v-if="element.type === 1 && $q.screen.lt.md">
            <div class="row budget-table-row budget-table-row-item"
                 @click="showTransactionList(element, null)">
              <div class="budget-table-column-name budget-table-row-item-name">
                <div class="budget-table-row-item-name-single">
                  <q-avatar :icon="element.icon" class="budget-table-row-item-icon" />&nbsp;<span
                  class="budget-table-row-item-name-single">{{ element.name }}</span>
                </div>
              </div>
              <div class="budget-table-column-spent -cursor-help -inactive"
                   v-html="moneyFormat(element.spent * -1, element.currencyId, false, true)">
              </div>
            </div>
          </div>
          <div class="budget-table-row-element-items" v-else-if="element.type !== 1">
            <div class="row budget-table-row budget-table-row-item draggable" v-for="subElement in element.children"
                 v-bind:key="subElement.id" @click="showTransactionList(element, subElement)">
              <div class="budget-table-column-name budget-table-row-item-name"
                   :draggable="element.type !== 2" @dragstart="startMoveElement($event, subElement)"
                   @dragend="stopMoveElement($event)">
                <div class="budget-table-row-item-name-single">
                  <q-avatar :icon="subElement.icon" class="budget-table-row-item-icon" />&nbsp;<span
                  class="budget-table-row-item-name-single">{{ subElement.name }}</span>
                </div>
              </div>
              <div class="budget-table-column-owner" v-if="subElement.ownerUserId && access.length > 1 && $q.screen.gt.sm">
                {{ access[subElement.ownerUserId].user.name }}
              </div>
              <div class="budget-table-column-available" v-if="subElement.ownerUserId && access.length > 1 && $q.screen.gt.sm">&nbsp;</div>
              <div class="budget-table-column-actions" v-if="subElement.ownerUserId && access.length > 1 && $q.screen.gt.sm">&nbsp;</div>
              <div class="budget-table-column-spent -cursor-help -inactive" v-html="moneyFormat(subElement.spent * -1, element.currencyId, false, true)">
              </div>
            </div>
          </div>
        </div>
      </template>
    </draggable>

    <div class="row budget-table-row-folder-empty" v-if="!elements.length">
      <div class="col">
        <div class="row">
          <q-icon class="budget-table-row-folder-empty-icon" name="error_outline" />
          <span class="budget-table-row-folder-empty-text">{{ $t('modules.budget.page.budget.structure.empty_folder.note') }}</span>
        </div>
        <div class="row budget-table-row-folder-empty-delete" v-if="isEditMode">
          <q-btn class="econumo-btn -small -grey" flat :label="$t('modules.budget.page.budget.structure.action.delete_folder')"
                 @click="deleteFolder(folderId, !!elements.length)" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { navigationMixin } from '../../mixins/navigationMixin';
import { Id } from '@shared/types';
import { useCurrenciesStore } from 'stores/currencies';
import { useBudgetsStore } from 'stores/budgets';
import { BudgetChildElementDto, BudgetElementDto, BudgetElementType } from '../../modules/api/v1/dto/budget.dto';
import { evaluateFormula, sanitizeInput, validateFormula } from '../../modules/helpers/calculator';
import { isValidFormula, isValidNumber, isValidDecimalNumber } from '../../modules/helpers/validation';
import { AccessRole, UserAccessDto } from '@shared/dto/access.dto';
import draggable from 'vuedraggable';
import { useQuasar } from 'quasar'
import { useLongPress } from '../../composables/useLongPress';
import { useAvatar } from '../../composables/useAvatar';
import { useMoney } from '../../composables/useMoney';
import { useDecimalNumber } from '../../composables/useDecimalNumber';

interface FolderStats {
  budgeted: number;
  spent: number;
  available: number;
}

interface AccessMap {
  [key: string]: UserAccessDto;
  length: number;
}

interface ActiveElementBudget {
  id: string;
  value: string | number;
}

defineOptions({
  name: 'BudgetTableFolder',
  mixins: [navigationMixin],
  components: { draggable }
});

const { avatarUrl } = useAvatar();
const { moneyFormat, moneyHTML } = useMoney();
const { normalizeNumber } = useDecimalNumber();

const props = defineProps<{
  editMode: boolean;
  canUpdateLimits: boolean;
  access: UserAccessDto[];
  budgetCurrencyId: Id;
  folderId: Id | null;
  folderName: string;
  elements: BudgetElementDto[];
  stats: FolderStats;
  isFirst: boolean | null;
  isLast: boolean | null;
}>();

const emit = defineEmits<{
  'update-budget': [id: string, value: number];
  'show-transaction-list': [element: BudgetElementDto | BudgetChildElementDto, tagId: Id | null, categoryId: Id | null, envelopeId: Id | null, currencyId: Id];
  'delete-folder': [folderId: Id, showConfirmation: boolean];
  'order-elements': [event: DragEvent];
  'order-folders': [folderId: Id, direction: number];
  'open-update-folder-modal': [folderId: Id];
  'open-create-envelope-modal': [folderId: Id];
  'open-update-element-modal': [element: BudgetElementDto];
  'open-delete-element-modal': [element: BudgetElementDto];
  'open-change-currency-modal': [element: BudgetElementDto];
  'open-set-limit-modal': [element: BudgetElementDto];
}>();

const $q = useQuasar();

const budgetActiveElementInput = ref();
onMounted(() => {
  if (budgetActiveElementInput.value) {
    const nativeInput = budgetActiveElementInput.value.$el.querySelector('input');
    if (nativeInput) {
      nativeInput.setAttribute('enterkeyhint', 'done');
      nativeInput.setAttribute('type', 'search');
    }
  }
});

const budgetCurrency = computed(() => {
  return useCurrenciesStore().currenciesHash[props.budgetCurrencyId];
});

const canUserUpdateLimits = ref(props.canUpdateLimits);
const isEditMode = computed(() => props.editMode);
const activeElementId = ref<Id | null>(null);
const activeElementBudget = ref<ActiveElementBudget | null>(null);
const elements = computed(() => props.elements);

const budgetStore = useBudgetsStore();
const currencyStore = useCurrenciesStore();
const currencies = computed(() => currencyStore.currenciesHash);

function getCurrencySymbol(currencyId: Id): string {
  if (currencies.value[currencyId]) {
    return currencies.value[currencyId].symbol;
  }
  return '-';
}

const unfoldedElements = computed(() => budgetStore.budgetUnfoldedElements);
const access = computed<AccessMap>(() => {
  const result: AccessMap = { length: 0 };
  let counter = 0;
  props.access.forEach((item: UserAccessDto) => {
    if (item.isAccepted && item.role !== AccessRole.READ_ONLY) {
      result[item.user.id] = item;
      counter++;
    }
  });
  result.length = counter;
  return result;
});

function toggleElement(element: BudgetElementDto): void {
  if (unfoldedElements.value[element.id]) {
    budgetStore.foldElement(element.id);
  } else {
    budgetStore.unfoldElement(element.id);
  }
}

function openBudgetEdit(element: BudgetElementDto): void {
  activeElementId.value = element.id;
  let value = moneyFormat(element.budgeted, element.currencyId, false, false, false);
  activeElementBudget.value = {
    id: element.id,
    value: value
  };
}

function closeBudgetEdit(): void {
  activeElementId.value = null;
  activeElementBudget.value = null;
}

function storeBudgetedValue(value: string | number): void {
  const stringValue = value.toString();
  const sanitizedValue = sanitizeInput(stringValue);
  let finalValue = sanitizedValue;

  if (validateFormula(sanitizedValue)) {
    finalValue = evaluateFormula(sanitizedValue).toString();
  }

  if (activeElementId.value) {
    activeElementBudget.value = {
      id: activeElementId.value,
      value: finalValue
    };
  }
}

function saveBudgetedValue(stopFunction: () => void): void {
  if (!activeElementBudget.value || !isValidDecimalNumber(activeElementBudget.value.value.toString())) {
    return;
  }

  emit('update-budget', activeElementBudget.value.id, normalizeNumber(activeElementBudget.value.value));
  activeElementId.value = null;
  activeElementBudget.value = null;
  stopFunction();
}

function deleteFolder(folderId: Id | null, showConfirmation: boolean): void {
  if (folderId) {
    emit('delete-folder', folderId, showConfirmation);
  }
}

function showTransactionList(parent: BudgetElementDto, child: BudgetChildElementDto | null): void {
  let element = null;
  let tagId: Id | null = null;
  let categoryId: Id | null = null;
  let envelopeId: Id | null = null;
  const currencyId = parent.currencyId;

  if (child !== null) {
    element = child;
    if (parent.type === BudgetElementType.TAG && child.type === BudgetElementType.CATEGORY) {
      tagId = parent.id;
      categoryId = child.id;
    } else if (parent.type === BudgetElementType.ENVELOPE && child.type === BudgetElementType.CATEGORY) {
      categoryId = child.id;
    }
  } else {
    element = parent;
    if (parent.type === BudgetElementType.TAG) {
      tagId = parent.id;
    } else if (parent.type === BudgetElementType.CATEGORY) {
      categoryId = parent.id;
    } else if (parent.type === BudgetElementType.ENVELOPE) {
      envelopeId = parent.id;
    }
  }

  if (element) {
    emit('show-transaction-list', element, tagId, categoryId, envelopeId, currencyId);
  }
}

function orderElements(event: DragEvent): void {
  emit('order-elements', event);
}

function orderFolders(direction: number): void {
  if (props.folderId) {
    emit('order-folders', props.folderId, direction);
  }
}

function openUpdateFolderModal(): void {
  if (props.folderId) {
    emit('open-update-folder-modal', props.folderId);
  }
}

function openCreateEnvelopeModal(): void {
  emit('open-create-envelope-modal', props.folderId ?? null);
}

function openUpdateElementModal(element: BudgetElementDto): void {
  emit('open-update-element-modal', element);
}

function openDeleteElementModal(element: BudgetElementDto): void {
  emit('open-delete-element-modal', element);
}

function openChangeCurrencyModal(element: BudgetElementDto): void {
  emit('open-change-currency-modal', element);
}

const movingElementId = ref<Id | null>(null);

function startMoveElement(event: DragEvent, item: BudgetElementDto): void {
  if (event.dataTransfer) {
    movingElementId.value = item.id;
    event.dataTransfer.dropEffect = 'move';
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('elementId', item.id);
  }
}

function stopMoveElement(event: DragEvent): void {
  movingElementId.value = null;
  if (event.dataTransfer) {
    event.dataTransfer.clearData('elementId');
  }
}

function moveElement(event: DragEvent, item: BudgetElementDto): void {
  if (!event.dataTransfer) return;

  const elementId = event.dataTransfer.getData('elementId');
  event.dataTransfer.clearData('elementId');
  movingElementId.value = null;

  if (!elementId || elementId === item.id) {
    return;
  }
}

function openSetLimitModal(element: BudgetElementDto): void {
  if (isEditMode.value) return;
  emit('open-set-limit-modal', element);
}

const { startTouch, handleTouchMove, handleTouchEnd } = useLongPress(
  (target: HTMLElement) => {
    const elementRow = target.closest('[data-element-id]') as HTMLElement;
    if (elementRow) {
      const elementId = elementRow.getAttribute('data-element-id');
      if (elementId) {
        const budgetElement = elements.value.find(e => e.id === elementId);
        if (budgetElement) {
          openSetLimitModal(budgetElement);
        }
      }
    }
  }
);
</script>

