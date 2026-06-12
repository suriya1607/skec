<template>
  <div>
    <div v-if="isValidating" class="text-center py-4">
      <AppLoader text="Validating invitation…" />
    </div>

    <template v-else>
      <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-900">Create your account</h2>
        <p class="text-sm text-gray-500 mt-1">Email is from your invitation. Complete the admission details.</p>
      </div>

      <AppAlert v-if="error" type="error" :message="error" class="mb-4" dismissible />

      <form class="space-y-6" @submit.prevent="handleRegister">
        <div class="grid grid-cols-1 md:grid-cols-[170px_1fr] gap-6">
          <div class="flex flex-col items-start gap-3">
            <div class="w-28 h-28 rounded-xl bg-primary-100 flex items-center justify-center overflow-hidden border border-primary-100">
              <img
                v-if="photoPreview"
                :src="photoPreview"
                alt="Student photo preview"
                class="w-full h-full object-cover"
              />
              <span v-else class="text-primary-700 font-bold text-3xl">{{ initials }}</span>
            </div>

            <div class="w-full">
              <label class="block text-sm font-medium text-gray-700 mb-1.5" for="reg-photo">
                Student photo <span class="text-red-500 ml-0.5">*</span>
              </label>
              <input
                id="reg-photo"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                class="block w-full text-xs text-gray-700 file:mb-2 file:w-full file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                @change="onPhotoChange"
              />
              <p v-if="fieldErrors.photo" class="mt-1.5 text-xs text-red-600">{{ fieldErrors.photo }}</p>
              <p v-else class="mt-1.5 text-xs text-gray-400">JPG, PNG or WebP. Max 2MB.</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <AppInput
              id="reg-name"
              v-model="form.name"
              label="Full name"
              placeholder="Your full name"
              :error="fieldErrors.name"
              required
            />
            <AppInput
              id="reg-email"
              v-model="form.email"
              label="Email address"
              type="email"
              :disabled="!!invitation.email"
            />
            <AppInput
              id="reg-no"
              v-model="form.reg_no"
              label="Registration number"
              placeholder="Reg. No."
              :error="fieldErrors.reg_no"
              required
              
            />
            <AppInput
              id="reg-father-name"
              v-model="form.father_name"
              label="Father's name"
              placeholder="Father's name"
              :error="fieldErrors.father_name"
              required
            />
            <AppInput
              id="reg-dob"
              v-model="form.dob"
              label="Date of birth"
              type="date"
              :error="fieldErrors.dob"
              required
            />
            <AppSelect
              id="reg-gender"
              v-model="form.gender"
              label="Gender"
              :options="genderOptions"
              placeholder="Select gender"
              :error="fieldErrors.gender"
              required
            />
            <AppSelect
              id="reg-community-category"
              v-model="form.community_category"
              label="Community category"
              :options="communityOptions"
              placeholder="Select category"
              :error="fieldErrors.community_category"
              required
            />
            <AppInput
              id="reg-contact-phone"
              v-model="form.contact_phone"
              label="Contact phone number"
              type="tel"
              placeholder="Contact number"
              :error="fieldErrors.contact_phone"
              required
            />
            <AppInput
              id="reg-qualification"
              v-model="form.qualification"
              label="Qualification"
              placeholder="Highest qualification"
              :error="fieldErrors.qualification"
              required
            />
            <AppSelect
              id="reg-course"
              v-model="form.course_id"
              label="Course"
              :options="courseOptions"
              placeholder="Select course"
              :error="fieldErrors.course_id"
              required
            />
            <AppSelect
              id="reg-medium"
              v-model="form.medium_of_studying"
              label="Medium of studying"
              :options="mediumOptions"
              placeholder="Select medium"
              :error="fieldErrors.medium_of_studying"
              required
            />
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1.5" for="reg-address">
                Address <span class="text-red-500 ml-0.5">*</span>
              </label>
              <textarea
                id="reg-address"
                v-model="form.address"
                rows="2"
                class="input-base resize-none"
                placeholder="Residential address"
                required
              />
              <p v-if="fieldErrors.address" class="mt-1.5 text-xs text-red-600">{{ fieldErrors.address }}</p>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-100 pt-5">
          <h3 class="font-semibold text-gray-800 mb-4">Set Password</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
          </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-gray-100 pt-5">
          <p v-if="invitation.expires_at" class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            Invitation expires {{ expiresFormatted }}
          </p>
          <AppButton type="submit" variant="primary" :loading="loading" class="w-full sm:w-auto">
            Create Account
          </AppButton>
        </div>
      </form>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { authApi }             from '../../api/auth'
import { categoriesApi }       from '../../api/categories'
import { useAuthStore }        from '../../stores/auth'
import { useSettingsStore }    from '../../stores/settings'
import { formatDateTime, getInitials } from '../../utils/helpers'
import AppInput  from '../../components/common/AppInput.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppAlert  from '../../components/common/AppAlert.vue'
import AppLoader from '../../components/common/AppLoader.vue'
import AppSelect from '../../components/common/AppSelect.vue'

const route  = useRoute()
const router = useRouter()
const authStore     = useAuthStore()
const settingsStore = useSettingsStore()

const isValidating  = ref(true)
const loading       = ref(false)
const error         = ref('')
const invitation    = ref(null)
const fieldErrors   = reactive({})
const courseOptions = ref([])
const photoPreview  = ref('')
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
  email: '',
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

const initials = computed(() => getInitials(form.name || invitation.value?.email || 'S'))
const expiresFormatted = computed(() =>
  invitation.value ? formatDateTime(invitation.value.expires_at) : ''
)

onMounted(async () => {
  const token = route.query.token
  if (!token) { router.replace('/register'); }

  if(token)
  {
    try {
      const res = await authApi.validateInvitation(token)
      invitation.value = res.data.data
      form.email = invitation.value.email
    } catch {
      router.replace('/invitation-expired')
      return
    }
  }else{
    invitation.value = true
  }

  try {
    const categories = await categoriesApi.list()
    courseOptions.value = categories.data.data.map(c => ({ value: c.id, label: c.name }))
  } catch {
    error.value = 'Unable to load courses. Please try again later.'
  } finally {
    isValidating.value = false
  }
})

function onPhotoChange(event) {
  const file = event.target.files?.[0] || null
  form.photo = file
  photoPreview.value = file ? URL.createObjectURL(file) : ''
}

async function handleRegister() {
  error.value = ''
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])
  loading.value = true
  try {
    const data = new FormData()
    data.append('token', route.query.token)
    Object.entries(form).forEach(([key, value]) => {
      if (value !== null && value !== undefined) data.append(key, value)
    })

    let res
    if(route.query.token){
      res = await authApi.register(data)
    }else{
      res = await authApi.publicRegister(data)
    }
    
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
