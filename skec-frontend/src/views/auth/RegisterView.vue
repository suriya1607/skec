<template>
  <div>
    <!-- Loading state -->
    <div v-if="isValidating" class="text-center py-4">
      <AppLoader text="Validating invitation…" />
    </div>

    <!-- Registration form -->
    <template v-else>
      <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Create your account</h2>
        <p class="text-sm text-gray-500 mt-1">Complete your registration below</p>
      </div>

      <AppAlert v-if="error" type="error" :message="error" class="mb-5" dismissible />

      <form @submit.prevent="handleRegister" class="space-y-5">
        <AppInput
          id="reg-email"
          :model-value="invitation?.email"
          label="Email address"
          type="email"
          disabled
        />
        <AppInput
          id="reg-name"
          v-model="form.name"
          label="Full name"
          type="text"
          placeholder="Your full name"
          :error="fieldErrors.name"
          required
        />
        <AppInput
          id="reg-password"
          v-model="form.password"
          label="Password"
          type="password"
          placeholder="Min. 8 characters"
          :error="fieldErrors.password"
          required
        />
        <AppInput
          id="reg-confirm"
          v-model="form.password_confirmation"
          label="Confirm password"
          type="password"
          placeholder="Repeat password"
          :error="fieldErrors.password_confirmation"
          required
        />

        <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
          ⏱ Invitation expires {{ expiresFormatted }}
        </p>

        <AppButton type="submit" variant="primary" size="lg" :loading="loading" class="w-full">
          Create Account
        </AppButton>
      </form>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { authApi }             from '../../api/auth'
import { useAuthStore }        from '../../stores/auth'
import { useSettingsStore }    from '../../stores/settings'
import { formatDateTime }      from '../../utils/helpers'
import AppInput  from '../../components/common/AppInput.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppAlert  from '../../components/common/AppAlert.vue'
import AppLoader from '../../components/common/AppLoader.vue'

const route  = useRoute()
const router = useRouter()
const authStore     = useAuthStore()
const settingsStore = useSettingsStore()

const isValidating  = ref(true)
const loading       = ref(false)
const error         = ref('')
const invitation    = ref(null)
const fieldErrors   = reactive({})
const form = reactive({ name: '', password: '', password_confirmation: '' })

const expiresFormatted = computed(() =>
  invitation.value ? formatDateTime(invitation.value.expires_at) : ''
)

onMounted(async () => {
  const token = route.query.token
  if (!token) { router.replace('/invitation-expired'); return }
  try {
    const res = await authApi.validateInvitation(token)
    invitation.value = res.data.data
  } catch {
    router.replace('/invitation-expired')
  } finally {
    isValidating.value = false
  }
})

async function handleRegister() {
  error.value = ''
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])
  loading.value = true
  try {
    const res = await authApi.register({
      token:                 route.query.token,
      name:                  form.name,
      password:              form.password,
      password_confirmation: form.password_confirmation,
    })
    const { user, token, session_token, settings } = res.data.data
    authStore.setFromRegister(user, token, session_token)
    if (settings) settingsStore.setFromLogin(settings)
    router.replace('/notes')
  } catch (err) {
    if (err.response?.data?.errors) {
      Object.assign(fieldErrors, err.response.data.errors)
    } else {
      error.value = err.response?.data?.message || 'Registration failed.'
    }
  } finally {
    loading.value = false
  }
}
</script>
