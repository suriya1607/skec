<template>
  <div>
    <div class="flex items-center justify-between gap-4 mb-6">
      <div class="flex items-center gap-4">
        <RouterLink to="/admin/students" class="text-gray-400 hover:text-gray-600">
          <ArrowLeftIcon class="w-5 h-5" />
        </RouterLink>
        <h1 class="text-2xl font-bold text-gray-900">Student Detail</h1>
      </div>
      <AppButton v-if="!editMode" variant="primary" size="sm" @click="editMode = true">
        Edit Profile
      </AppButton>
      <div v-else class="flex gap-2">
        <AppButton variant="secondary" size="sm" @click="cancelEdit">Cancel</AppButton>
        <AppButton variant="primary" size="sm" :loading="saving" @click="saveEdit">Save Changes</AppButton>
      </div>
    </div>

    <AppAlert v-if="error" type="error" :message="error" class="mb-4" dismissible />
    <AppAlert v-if="success" type="success" message="Student profile updated successfully." class="mb-4" dismissible />

    <AppLoader v-if="loading" />

    <div v-else-if="student" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Profile card -->
      <div class="card text-center">
        <img
          v-if="student.photo_url"
          :src="student.photo_url"
          :alt="student.name"
          class="w-20 h-20 rounded-full object-cover border border-gray-200 mx-auto mb-3"
        />
        <div v-else class="w-20 h-20 rounded-full bg-primary-100 flex items-center justify-center mx-auto mb-3">
          <span class="text-primary-700 font-bold text-xl">{{ getInitials(student.name) }}</span>
        </div>
        <h2 class="font-bold text-gray-900 text-lg">{{ student.name }}</h2>
        <p class="text-gray-400 text-sm">{{ student.email }}</p>
        <AppBadge :variant="student.status" :label="student.status" dot class="mt-3" />

        <div class="mt-5 space-y-2">
          <AppButton
            variant="secondary"
            size="sm"
            class="w-full"
            :disabled="!student.photo_url"
            @click="downloadPhoto"
          >
            Download Photo
          </AppButton>
          <AppButton
            :variant="student.status === 'active' ? 'danger' : 'success'"
            size="sm"
            class="w-full"
            @click="toggleStatus"
          >
            {{ student.status === 'active' ? 'Deactivate' : 'Activate' }}
          </AppButton>
          <AppButton variant="secondary" size="sm" class="w-full" @click="forceLogout">
            Force Logout
          </AppButton>
        </div>
      </div>

      <!-- Stats + logs -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Admission Details - Edit Mode -->
        <div v-if="editMode" class="card">
          <h3 class="font-semibold text-gray-800 mb-4">Edit Admission Details</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <AppInput
              v-model="form.name"
              label="Student Name"
              :error="fieldErrors.name"
              required
            />
            <AppInput
              :model-value="student.email"
              label="Email Address"
              type="email"
              disabled
            />
            <AppInput
              v-model="form.father_name"
              label="Father's Name"
              :error="fieldErrors.father_name"
              required
            />
            <AppInput
              v-model="form.reg_no"
              label="Registration No."
              :error="fieldErrors.reg_no"
              required
            />
            <AppInput
              v-model="form.dob"
              label="Date of Birth"
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
              label="Community Category"
              :options="communityOptions"
              placeholder="Select category"
              :error="fieldErrors.community_category"
              required
            />
            <AppInput
              v-model="form.contact_phone"
              label="Contact Phone"
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
              label="Medium of Studying"
              :options="mediumOptions"
              placeholder="Select medium"
              :error="fieldErrors.medium_of_studying"
              required
            />
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
              <textarea
                v-model="form.address"
                rows="2"
                class="input-base resize-none"
                required
              />
              <p v-if="fieldErrors.address" class="mt-1.5 text-xs text-red-600">{{ fieldErrors.address }}</p>
            </div>
          </div>

          <div class="border-t border-gray-100 pt-4">
            <h4 class="font-semibold text-gray-800 mb-3">Change Password (Optional)</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <AppInput
                v-model="form.password"
                label="New Password"
                type="password"
                placeholder="Leave blank to keep current"
                :error="fieldErrors.password"
              />
              <AppInput
                v-model="form.password_confirmation"
                label="Confirm Password"
                type="password"
                placeholder="Repeat password"
                :error="fieldErrors.password_confirmation"
              />
            </div>
          </div>
        </div>

        <!-- Admission Details - View Mode -->
        <div v-else class="card">
          <h3 class="font-semibold text-gray-800 mb-4">Admission Details</h3>
          <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-400">Registration No.</dt><dd class="font-medium mt-0.5">{{ profile?.reg_no || '—' }}</dd></div>
            <div><dt class="text-gray-400">Father's Name</dt><dd class="font-medium mt-0.5">{{ profile?.father_name || '—' }}</dd></div>
            <div><dt class="text-gray-400">Date of Birth</dt><dd class="font-medium mt-0.5">{{ formatDate(profile?.dob) }}</dd></div>
            <div><dt class="text-gray-400">Gender</dt><dd class="font-medium mt-0.5 capitalize">{{ profile?.gender || '—' }}</dd></div>
            <div><dt class="text-gray-400">Community Category</dt><dd class="font-medium mt-0.5">{{ profile?.community_category || '—' }}</dd></div>
            <div><dt class="text-gray-400">Contact Phone</dt><dd class="font-medium mt-0.5">{{ profile?.contact_phone || '—' }}</dd></div>
            <div><dt class="text-gray-400">Qualification</dt><dd class="font-medium mt-0.5">{{ profile?.qualification || '—' }}</dd></div>
            <div><dt class="text-gray-400">Course</dt><dd class="font-medium mt-0.5">{{ profile?.course?.name || '—' }}</dd></div>
            <div><dt class="text-gray-400">Medium</dt><dd class="font-medium mt-0.5 capitalize">{{ profile?.medium_of_studying || '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-400">Address</dt><dd class="font-medium mt-0.5">{{ profile?.address || '—' }}</dd></div>
          </dl>
        </div>

        <div class="card">
          <h3 class="font-semibold text-gray-800 mb-4">Activity Info</h3>
          <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-400">Joined</dt><dd class="font-medium mt-0.5">{{ formatDate(student.created_at) }}</dd></div>
            <div><dt class="text-gray-400">Last Login</dt><dd class="font-medium mt-0.5">{{ formatDate(student.last_login_at) }}</dd></div>
            <div><dt class="text-gray-400">Last IP</dt><dd class="font-medium mt-0.5">{{ student.last_login_ip || '—' }}</dd></div>
          </dl>
        </div>

        <div class="card">
          <h3 class="font-semibold text-gray-800 mb-4">Recent Activity</h3>
          <div v-if="!student.access_logs?.length" class="text-center py-6 text-gray-400 text-sm">No activity yet</div>
          <div v-else class="space-y-2">
            <div v-for="log in student.access_logs" :key="log.id" class="flex justify-between text-sm py-1.5 border-b border-gray-50 last:border-0">
              <span class="text-gray-600">Note #{{ log.note_id }} — <span class="capitalize">{{ log.action }}</span></span>
              <span class="text-gray-400">{{ formatDate(log.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { adminStudentsApi } from '../../api/admin/students'
import { categoriesApi } from '../../api/categories'
import { formatDate, getInitials } from '../../utils/helpers'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import AppLoader from '../../components/common/AppLoader.vue'
import AppAlert from '../../components/common/AppAlert.vue'
import AppBadge  from '../../components/common/AppBadge.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppInput  from '../../components/common/AppInput.vue'
import AppSelect from '../../components/common/AppSelect.vue'

const route   = useRoute()
const loading = ref(true)
const saving  = ref(false)
const editMode = ref(false)
const error = ref('')
const success = ref(false)
const student = ref(null)
const profile = computed(() => student.value?.profile || null)
const courseOptions = ref([])
const fieldErrors = reactive({})

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

const form = reactive({
  name: '',
  father_name: '',
  reg_no:'',
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

onMounted(async () => {
  try {
    const res = await adminStudentsApi.get(route.params.id)
    student.value = res.data.data.student
    fillForm()
    await loadCourses()
  } finally {
    loading.value = false
  }
})

async function loadCourses() {
  const res = await categoriesApi.list()
  courseOptions.value = res.data.data.map(c => ({ value: c.id, label: c.name }))
}

function fillForm() {
  const p = student.value?.profile
  form.name = student.value?.name || ''
  form.father_name = p?.father_name || ''
  form.reg_no = p?.reg_no || ''
  form.dob = p?.dob ? p.dob.slice(0, 10) : ''
  form.gender = p?.gender || ''
  form.address = p?.address || ''
  form.community_category = p?.community_category || ''
  form.contact_phone = p?.contact_phone || ''
  form.qualification = p?.qualification || ''
  form.course_id = p?.course_id ? String(p.course_id) : ''
  form.medium_of_studying = p?.medium_of_studying || ''
}

function cancelEdit() {
  editMode.value = false
  error.value = ''
  success.value = false
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])
  fillForm()
}

async function saveEdit() {
  error.value = ''
  success.value = false
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])

  const data = new FormData()
  const editableFields = ['name', 'father_name','reg_no', 'dob', 'gender', 'address', 'community_category', 'contact_phone', 'qualification', 'course_id', 'medium_of_studying', 'password', 'password_confirmation']
  
  Object.entries(form).forEach(([key, value]) => {
    if (!editableFields.includes(key)) return
    if ((key === 'password' || key === 'password_confirmation') && !form.password) return
    data.append(key, value ?? '')
  })

  saving.value = true
try {
  await adminStudentsApi.profileUpdate(student.value.id, data)
  const updated = await adminStudentsApi.get(route.params.id)
  student.value = updated.data.data.student
  fillForm()
  form.password = ''
  form.password_confirmation = ''
  editMode.value = false
  success.value = true
  setTimeout(() => {
    success.value = false
  }, 3000)

}  catch (err) {
    if (err.response?.data?.errors) Object.assign(fieldErrors, err.response.data.errors)
    else error.value = err.response?.data?.message || 'Update failed.'
  } finally {
    saving.value = false
  }
}

async function toggleStatus() {
  const newStatus = student.value.status === 'active' ? 'inactive' : 'active'
  await adminStudentsApi.update(student.value.id, { status: newStatus })
  student.value.status = newStatus
}

async function downloadPhoto() {
  if (!student.value?.photo_url) return
  const res = await adminStudentsApi.downloadPhoto(student.value.id)
  const url = URL.createObjectURL(res.data)
  const link = document.createElement('a')
  link.href = url
  link.download = `${student.value.name || 'student'}-photo`
  document.body.appendChild(link)
  link.click()
  link.remove()
  URL.revokeObjectURL(url)
}

async function forceLogout() {
  await adminStudentsApi.forceLogout(student.value.id)
}
</script>
