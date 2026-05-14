<template>
  <div class="max-w-5xl">
    <div class="flex items-center justify-between gap-4 mb-5">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <p class="text-sm text-gray-500 mt-1">Email is fixed. You can update the remaining details.</p>
      </div>
      <AppBadge :variant="authStore.user?.status" :label="authStore.user?.status || '-'" dot />
    </div>

    <AppAlert v-if="error" type="error" :message="error" class="mb-4" dismissible />
    <AppAlert v-if="success" type="success" message="Profile updated successfully." class="mb-4" />

    <form class="card space-y-6" @submit.prevent="handleSave">
      <div class="grid grid-cols-1 lg:grid-cols-[180px_1fr] gap-6">
        <div class="flex flex-col items-start gap-3">
          <img
            v-if="authStore.user?.photo_url"
            :src="authStore.user.photo_url"
            :alt="authStore.user?.name || 'Student photo'"
            class="w-28 h-28 rounded-xl object-cover border border-gray-200"
          />
          <div v-else class="w-28 h-28 rounded-xl bg-primary-100 flex items-center justify-center">
            <span class="text-primary-700 font-bold text-3xl">{{ authStore.userInitials }}</span>
          </div>

          <div class="w-full">
            <label class="block text-sm font-medium text-gray-700 mb-1.5" for="profile-photo">Profile image</label>
            <input
              id="profile-photo"
              type="file"
              accept="image/jpeg,image/png,image/webp"
              class="block w-full text-xs text-gray-700 file:mb-2 file:w-full file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
              @change="onPhotoChange"
            />
            <p v-if="fieldErrors.photo" class="mt-1.5 text-xs text-red-600">{{ fieldErrors.photo }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <AppInput
            v-model="form.name"
            label="Full name"
            :error="fieldErrors.name"
            required
          />
          <AppInput
            :model-value="authStore.user?.email"
            label="Email address"
            type="email"
            disabled
          />
          <AppInput
            v-model="form.father_name"
            label="Father's name"
            :error="fieldErrors.father_name"
            required
          />
          <AppInput
            v-model="form.reg_no"
            label="Registration number"
            :error="fieldErrors.reg_no"
            required
            disabled
          />
          <AppInput
            v-model="form.dob"
            label="Date of birth"
            type="date"
            :error="fieldErrors.dob"
            required
          />
          <AppSelect
            v-model="form.gender"
            label="Gender"
            :options="genderOptions"
            placeholder="Select gender"
            :error="fieldErrors.gender"
            required
          />
          <AppSelect
            v-model="form.community_category"
            label="Community category"
            :options="communityOptions"
            placeholder="Select category"
            :error="fieldErrors.community_category"
            required
          />
          <AppInput
            v-model="form.contact_phone"
            label="Contact phone number"
            type="tel"
            :error="fieldErrors.contact_phone"
            required
          />
          <AppInput
            v-model="form.qualification"
            label="Qualification"
            :error="fieldErrors.qualification"
            required
          />
          <AppSelect
            v-model="form.course_id"
            label="Course"
            :options="courseOptions"
            placeholder="Select course"
            :error="fieldErrors.course_id"
            required
          />
          <AppSelect
            v-model="form.medium_of_studying"
            label="Medium of studying"
            :options="mediumOptions"
            placeholder="Select medium"
            :error="fieldErrors.medium_of_studying"
            required
          />
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5" for="profile-address">
              Address <span class="text-red-500 ml-0.5">*</span>
            </label>
            <textarea
              id="profile-address"
              v-model="form.address"
              rows="2"
              class="input-base resize-none"
              required
            />
            <p v-if="fieldErrors.address" class="mt-1.5 text-xs text-red-600">{{ fieldErrors.address }}</p>
          </div>
        </div>
      </div>

      <div class="border-t border-gray-100 pt-5">
        <h3 class="font-semibold text-gray-800 mb-4">Change Password</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <AppInput
            v-model="form.password"
            label="New password"
            type="password"
            placeholder="Leave blank to keep current"
            :error="fieldErrors.password"
          />
          <AppInput
            v-model="form.password_confirmation"
            label="Confirm new password"
            type="password"
            placeholder="Repeat password"
            :error="fieldErrors.password_confirmation"
          />
        </div>
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-gray-100 pt-5">
        <p class="text-xs text-gray-400">Last login: {{ formatDate(authStore.user?.last_login_at) || '-' }}</p>
        <AppButton type="submit" variant="primary" :loading="saving" class="w-full sm:w-auto">
          Save Changes
        </AppButton>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { authApi } from '../../api/auth'
import { categoriesApi } from '../../api/categories'
import { useAuthStore } from '../../stores/auth'
import { formatDate } from '../../utils/helpers'
import AppAlert from '../../components/common/AppAlert.vue'
import AppBadge from '../../components/common/AppBadge.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppInput from '../../components/common/AppInput.vue'
import AppSelect from '../../components/common/AppSelect.vue'

const authStore = useAuthStore()
const saving = ref(false)
const error = ref('')
const success = ref(false)
const courseOptions = ref([])
const fieldErrors = reactive({})

const form = reactive({
  name: '',
  photo: null,
  reg_no: '',
  father_name: '',
  dob: '',
  gender: '',
  address: '',
  community_category: '',
  contact_phone: '',
  qualification: '',
  course_id: '',
  medium_of_studying: '',
  password: '',
  password_confirmation: '',
})

const genderOptions = [
  { value: 'male', label: 'Male' },
  { value: 'female', label: 'Female' },
  { value: 'other', label: 'Other' },
]
const communityOptions = ['MBC', 'OBC', 'SC', 'ST', 'BCM', 'EWS', 'EBC']
  .map(value => ({ value, label: value }))
const mediumOptions = [
  { value: 'english', label: 'English' },
  { value: 'tamil', label: 'Tamil' },
]

onMounted(async () => {
  try {
    await Promise.all([authStore.fetchMe(), loadCourses()])
    fillForm()
  } catch {
    error.value = 'Unable to load profile details.'
  }
})

watch(() => authStore.user, fillForm, { deep: true })

async function loadCourses() {
  const res = await categoriesApi.list()
  courseOptions.value = res.data.data.map(c => ({ value: c.id, label: c.name }))
}

function fillForm() {
  const user = authStore.user
  const profile = user?.profile
  form.name = user?.name || ''
  form.reg_no = profile?.reg_no || ''
  form.father_name = profile?.father_name || ''
  form.dob = profile?.dob ? profile.dob.slice(0, 10) : ''
  form.gender = profile?.gender || ''
  form.address = profile?.address || ''
  form.community_category = profile?.community_category || ''
  form.contact_phone = profile?.contact_phone || ''
  form.qualification = profile?.qualification || ''
  form.course_id = profile?.course_id ? String(profile.course_id) : ''
  form.medium_of_studying = profile?.medium_of_studying || ''
}

function onPhotoChange(event) {
  form.photo = event.target.files?.[0] || null
}

async function handleSave() {
  error.value = ''
  success.value = false
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])

  const data = new FormData()
  Object.entries(form).forEach(([key, value]) => {
    if (key === 'photo' && !value) return
    if ((key === 'password' || key === 'password_confirmation') && !form.password) return
    data.append(key, value ?? '')
  })

  saving.value = true
  try {
    const res = await authApi.updateProfile(data)
    authStore.setUser(res.data.data.user)
    form.photo = null
    form.password = ''
    form.password_confirmation = ''
    success.value = true
  } catch (err) {
    if (err.response?.data?.errors) Object.assign(fieldErrors, err.response.data.errors)
    else error.value = err.response?.data?.message || 'Profile update failed.'
  } finally {
    saving.value = false
  }
}
</script>
