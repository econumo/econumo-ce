<template>
  <component :is="currentComponent" />
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { useUsersStore } from '../stores/users';
import Budget from './Budget/Budget.vue';
import Onboarding from './Onboarding.vue';
import { defineOptions } from 'vue'

defineOptions({
  name: 'HomePage'
})

const userStore = useUsersStore();
const currentComponent = computed(() => 
  userStore.userOnboardingCompleted ? Budget : Onboarding
);
</script> 