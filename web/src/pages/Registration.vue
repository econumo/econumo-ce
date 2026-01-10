<template>
  <div class="form-container full-width column wrap justify-center items-center content-center" v-if="isPaywallEnabled">

    <div class="register-paywall-header" v-html="$t('modules.user.page.sign_up.paywall.header')">
    </div>
    <div class="register-paywall-text" v-html="$t('modules.user.page.sign_up.paywall.text')">
    </div>

    <q-btn
      size="22px"
      class="econumo-btn q-px-xl q-py-xs -yellow"
      :label="$t('modules.user.page.sign_up.paywall.action')"
      :href="paywallUrl"
      type="a"
      target="_blank"
      rel="noopener noreferrer"
      flat
    />

    <div class="register-next-steps">
      {{ $t('modules.user.page.sign_up.paywall.next_steps') }}
    </div>
  </div>
  <div class="form-container full-width column wrap justify-center items-center content-center" v-else>
    <q-form
      novalidate
      @submit="submit"
      class="full-width"
    >
      <q-input
        outlined
        class="form-input full-width"
        :placeholder="$t('modules.user.form.user.name.placeholder')"
        type="text"
        v-model="name"
        :label="$t('modules.user.form.user.name.label')"
        lazy-rules
        :rules="[
          val => validation.isNotEmpty(val) || $t('modules.user.form.user.name.validation.required_field'),
          val => validation.isValidName(val) || $t('modules.user.form.user.name.validation.invalid_name')
        ]"
      />

      <q-input
        outlined
        class="form-input full-width"
        :placeholder="$t('modules.user.form.user.email.placeholder')"
        type="email"
        v-model="email"
        :label="$t('modules.user.form.user.email.label')"
        lazy-rules
        :rules="[
          val => validation.isNotEmpty(val) || $t('modules.user.form.user.email.validation.required_field'),
          val => validation.isValidEmail(val) || $t('modules.user.form.user.email.validation.invalid_email')
        ]"
      />

      <q-input
        outlined
        class="form-input full-width"
        :placeholder="$t('modules.user.form.user.password.placeholder')"
        type="password"
        v-model="password"
        :label="$t('modules.user.form.user.password.label')"
        lazy-rules
        :rules="[
          val => validation.isNotEmpty(val) || $t('modules.user.form.user.password.validation.required_field'),
          val => validation.isValidPassword(val) || $t('modules.user.form.user.password.validation.invalid_password')
        ]"/>

      <q-input
        outlined
        class="form-input full-width"
        :placeholder="$t('modules.user.form.user.password_retry.placeholder')"
        type="password"
        v-model="passwordRetry"
        :label="$t('modules.user.form.user.password_retry.label')"
        lazy-rules
        :rules="[
          val => validation.isNotEmpty(val) || $t('modules.user.form.user.password_retry.validation.invalid_password'),
          val => val === password || $t('modules.user.form.user.password_retry.validation.not_equals')
        ]"/>

      <div v-if="isCustomApiAllowed">
        <div class="form-checkbox-group">
          <q-checkbox
            class="form-checkbox full-width"
            v-model="selfHosted"
            :label="$t('modules.user.form.user.server_host.self_hosted')"
          />
          <div 
            @click="selfHostedModal.open" 
            class="form-checkbox-hint"
            role="button"
            tabindex="0"
          ></div>
        </div>

        <q-input
          v-show="selfHosted"
          outlined
          class="form-input full-width"
          :placeholder="$t('modules.user.form.user.server_host.placeholder')"
          type="url"
          v-model="host"
          :label="$t('modules.user.form.user.server_host.label')"
          lazy-rules
          :rules="[
            val => validation.isNotEmpty(val) || $t('modules.user.form.user.server_host.validation.required_field'),
            val => validation.isValidHttpUrl(val) || $t('modules.user.form.user.server_host.validation.invalid_url'),
          ]"
        />
      </div>

      <div class="form-btn-container">
        <q-btn
          class="econumo-btn -large -yellow full-width"
          :label="$t('modules.user.form.sign_up.action.sign_up')"
          type="submit"
          flat
        />
      </div>

      <div class="register-form-privacy" v-html="$t('modules.user.page.sign_up.privacy.text')">
      </div>

      <teleport to="body">
        <loading-modal 
          v-if="loadingModal.isOpen" 
          @close="loadingModal.close" 
          :header-label="$t('modules.user.modal.sign_up.label')"
        />

        <self-hosted-info-modal 
          :model-value="selfHostedModal.isOpen.value"
          @update:model-value="(val) => selfHostedModal.isOpen.value = val"
          @cancel="selfHostedModal.close"
        />

        <q-dialog v-model="isFailRegistration" minimized @hide="closeModalFailRegistration">
          <div class="econumo-modal form-fail-modal">
            <div class="form-fail-modal-title">
              {{ $t('modules.user.modal.sign_up_failed.header') }}
            </div>
            <div class="form-fail-modal-description">
              {{ $t('modules.user.modal.sign_up_failed.information') }}
            </div>
            <div class="form-fail-modal-button">
              <q-btn
                class="econumo-btn -small -yellow full-width"
                :label="$t('elements.button.ok.label')"
                type="button"
                @click="closeModalFailRegistration"
                flat
              />
            </div>
          </div>
        </q-dialog>
      </teleport>
    </q-form>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue'
import { defineOptions } from 'vue'
import { useRouter } from 'vue-router'
import config from '../modules/config'
import LoadingModal from '../components/LoadingModal.vue'
import SelfHostedInfoModal from '../components/Login/SelfHostedInfoModal.vue'
import { useUsersStore } from 'stores/users'
import { useValidation } from '../composables/useValidation'
import { useLoadingModal } from '../composables/useLoadingModal'
import { useSelfHostedInfoModal } from '../composables/useSelfHostedInfoModal'
import { econumoPackage } from '../modules/package';

defineOptions({
  name: 'RegistrationPage'
})

// State
const isFailRegistration = ref(false)
const selfHosted = ref(config.selfHosted() as boolean)
const host = ref(String(config.backendHost() || ''))
const name = ref('')
const email = ref('')
const password = ref('')
const passwordRetry = ref('')

// Composables
const router = useRouter()
const usersStore = useUsersStore()
const validation = useValidation()
const loadingModal = useLoadingModal()
const selfHostedModal = useSelfHostedInfoModal()

// Computed
const isCustomApiAllowed = computed(() => config.isCustomApiAllowed())
const isPaywallEnabled = computed(() => econumoPackage.isPaywallEnabled)
const paywallUrl = computed(() => econumoPackage.paywallUrl)
// Methods
const openModalFailRegistration = () => {
  isFailRegistration.value = true
}

const closeModalFailRegistration = () => {
  isFailRegistration.value = false
}

const submit = async () => {
  loadingModal.open()
  try {
    const success = await usersStore.register({
      email: email.value,
      password: password.value.toString(),
      name: name.value.toString()
    })
    
    if (!success) {
      openModalFailRegistration()
    } else {
      router.push({ name: 'login' })
    }
  } catch {
    openModalFailRegistration()
  } finally {
    loadingModal.close()
  }
}

// Watchers
watch(host, (value) => {
  config.backendHost(value)
})

watch(selfHosted, (value: boolean) => {
  config.selfHosted(value)
})

// Lifecycle
onMounted(() => {
  if (usersStore.isUserAuthenticated) {
    router.push({ name: 'home' })
  }
  selfHosted.value = config.selfHosted() as boolean
  host.value = String(config.backendHost() || '')
})
</script>
