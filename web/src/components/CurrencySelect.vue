<template>
  <q-select ref="selectRef"
            :class="customClass"
            :outlined="outlined"
            v-model="selectedCurrency"
            :input-value="inputValue"
            use-input
            lazy-rules
            :options="currenciesOptions"
            :option-disable="optionDisable"
            :label="label"
            :rules="rules"
            :borderless="borderless"
            :readonly="readonly"
            :dropdown-icon="dropdownIcon"
            :popup-content-class="popupContentClass"
            :options-selected-class="optionsSelectedClass"
            @update:input-value="onInputValueUpdate"
            @popup-show="openCurrencySelect"
            @popup-hide="closeCurrencySelect"
            @filter="filterCurrencies"
            @filter-abort="filterCurrenciesAbort">
    <template v-slot:selected-item="scope">
      <slot name="selected-item" v-bind="scope">
        <div class="self-center full-width no-outline">{{ scope.opt.value ? currenciesStore.currenciesHash[scope.opt.value].code : '' }}</div>
      </slot>
    </template>
    <template v-slot:option="scope">
      <q-item v-bind="scope.itemProps">
        <q-item-section>
          <q-item-label>{{ getFullCurrencyLabel(currenciesStore.currenciesHash[scope.opt.value]) }}</q-item-label>
        </q-item-section>
      </q-item>
    </template>
  </q-select>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Id } from '../modules/types';
import { useCurrenciesStore } from 'stores/currencies';
import { CurrencyDto } from '@shared/dto/currency.dto';

interface Props {
  modelValue: CurrencyOptionDto | null;
  label?: string;
  rules?: Array<(val: any) => boolean | string>;
  optionDisable?: (item: any) => boolean;
  borderless?: boolean;
  readonly?: boolean;
  dropdownIcon?: string;
  popupContentClass?: string;
  optionsSelectedClass?: string;
  outlined?: boolean;
  customClass?: string;
}

interface CurrencyOptionDto {
  label: string;
  value: Id;
}

interface EmitEvents {
  (event: 'update:modelValue', value: CurrencyOptionDto | null): void;
}

const props = withDefaults(defineProps<Props>(), {
  label: '',
  rules: () => [],
  optionDisable: () => false,
  borderless: false,
  readonly: false,
  dropdownIcon: undefined,
  popupContentClass: undefined,
  optionsSelectedClass: undefined,
  outlined: false,
  customClass: ''
});

const emit = defineEmits<EmitEvents>();
const selectRef = ref<any>(null);

const currenciesStore = useCurrenciesStore();
const currencySearchFilter = ref('');
const tmpCurrencyId = ref('');
const inputValue = ref('');

const selectedCurrency = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value);
    if (value) {
      inputValue.value = value.label;
      selectRef.value?.blur();
    } else {
      inputValue.value = '';
    }
  }
});

function fuzzyMatch(str: string, pattern: string): boolean {
  pattern = pattern.toLowerCase();
  str = str.toLowerCase();
  
  let patternIdx = 0;
  let strIdx = 0;
  
  while (patternIdx < pattern.length && strIdx < str.length) {
    if (pattern[patternIdx] === str[strIdx]) {
      patternIdx++;
    }
    strIdx++;
  }
  
  return patternIdx === pattern.length;
}

const currenciesOptions = computed(() => {
  const filter = currencySearchFilter.value.toLowerCase();
  return currenciesStore.currencies
    .filter((item: CurrencyDto) => {
      if (!filter) return true;
      return (
        fuzzyMatch(item.name, filter) ||
        fuzzyMatch(item.symbol, filter) ||
        fuzzyMatch(item.code, filter)
      );
    })
    .map(assembleCurrency);
});

function getFullCurrencyLabel(currency: CurrencyDto): string {
  const uniqueValues = new Set([currency.code, currency.symbol, currency.name]);
  const values = Array.from(uniqueValues);
  return values.join(', ');
}

function assembleCurrency(item: CurrencyDto): CurrencyOptionDto {
  return {
    label: item.code,
    value: item.id
  };
}

function openCurrencySelect(): void {
  tmpCurrencyId.value = selectedCurrency.value?.value ?? '';
  emit('update:modelValue', null);
  inputValue.value = '';
  currencySearchFilter.value = '';
}

function closeCurrencySelect(): void {
  if (!selectedCurrency.value && tmpCurrencyId.value) {
    const currency = assembleCurrency(currenciesStore.currenciesHash[tmpCurrencyId.value]);
    emit('update:modelValue', currency);
    inputValue.value = currency.label;
  } else if (selectedCurrency.value) {
    inputValue.value = selectedCurrency.value.label;
  } else {
    inputValue.value = '';
  }
}

function onInputValueUpdate(val: string): void {
  if (selectRef.value?.hasPopupOpen) {
    currencySearchFilter.value = val;
  }
  inputValue.value = val;
}

function filterCurrencies(val: string, update: (fn: () => void) => void): void {
  update(() => {
    currencySearchFilter.value = val;
  });
}

function filterCurrenciesAbort(): void {
  currencySearchFilter.value = '';
  if (selectedCurrency.value) {
    inputValue.value = selectedCurrency.value.label;
  }
}
</script> 