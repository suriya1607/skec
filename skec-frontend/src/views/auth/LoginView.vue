<template>
  <div>
    <div class="mb-6">
      <h2 class="text-xl font-bold text-gray-900">Welcome back</h2>
      <p class="text-sm text-gray-500 mt-1">Sign in to your account</p>
    </div>

    <AppAlert v-if="error" type="error" :message="error" class="mb-5" dismissible />

    <form @submit.prevent="handleLogin" class="space-y-5">
      <AppInput
        id="email"
        v-model="form.email"
        label="Email address"
        type="email"
        placeholder="you@srikumaran.in"
        :error="errors.email"
        required
        autocomplete="email"
      />
      <AppInput
        id="password"
        v-model="form.password"
        label="Password"
        type="password"
        placeholder="••••••••"
        :error="errors.password"
        required
        autocomplete="current-password"
      />

      <AppButton type="submit" variant="primary" size="lg" :loading="loading" class="w-full">
        Sign in
      </AppButton>
    </form>

    <p class="mt-6 text-center text-xs text-gray-400">
      Access is by invitation only. Contact your administrator.
    </p>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter }         from 'vue-router'
import { useAuthStore }      from '../../stores/auth'
import { useSettingsStore }  from '../../stores/settings'
import AppInput  from '../../components/common/AppInput.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppAlert  from '../../components/common/AppAlert.vue'

const router        = useRouter()
const authStore     = useAuthStore()
const settingsStore = useSettingsStore()

const loading = ref(false)
const error   = ref('')
const errors  = reactive({ email: '', password: '' })
const form    = reactive({ email: '', password: '' })

async function handleLogin() {
  error.value    = ''
  errors.email   = ''
  errors.password = ''
  loading.value  = true
  try {
    const result = await authStore.login(form)
    // Cache settings returned from login
    if (result.data?.settings) {
      settingsStore.setFromLogin(result.data.settings)
    }
    const user = authStore.user
    router.replace(user?.role === 'admin' ? '/admin/dashboard' : '/notes')
  } catch (err) {
    const code = err.response?.data?.error
    const msg  = err.response?.data?.message || 'Login failed. Please try again.'
    if (code === 'invalid_credentials') error.value = 'Invalid email or password.'
    else if (code === 'account_inactive') error.value = 'Your account is inactive. Contact admin.'
    else error.value = msg
  } finally {
    loading.value = false
  }
}
</script>
