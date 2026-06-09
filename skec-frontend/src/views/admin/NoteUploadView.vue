<template>
  <div class="max-w-2xl">
    <div class="flex items-center gap-4 mb-6">
      <RouterLink to="/admin/notes"><ArrowLeftIcon class="w-5 h-5 text-gray-400 hover:text-gray-600" /></RouterLink>
      <h1 class="text-2xl font-bold text-gray-900">Upload Note</h1>
    </div>

    <AppAlert v-if="error" type="error" :message="error" class="mb-5" dismissible />
    <AppAlert v-if="success" type="success" :message="successMsg" class="mb-5" />

    <div class="card">
      <form @submit.prevent="handleUpload" class="space-y-6">
        <AppFileUpload
          v-model="form.file"
          label="PDF File"
          :max-size-mb="maxFileSizeMb"
          :error="fieldErrors.file"
          :progress="uploadProgress"
          @update:model-value="onFileSelected"
        />

        <AppInput
          v-model="form.title"
          label="Title"
          placeholder="Note title"
          :error="fieldErrors.title"
          required
        />

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="input-base resize-none"
            placeholder="Optional description…"
          />
        </div>

        <AppMultiSelect
          v-model="form.category_ids"
          label="Batch"
          :options="categoryOptions"
          placeholder="Select batch"
          :error="fieldErrors.category_ids"
        />

        <AppSelect
          v-model="form.subject_id"
          label="Subject"
          :options="subjectOptions"
          placeholder="Select subject"
        />

        <AppSelect
          v-model="form.status"
          label="Status"
          :options="[{value:'draft',label:'Draft'},{value:'published',label:'Published'}]"
        />

        <div class="flex gap-3 pt-2">
          <AppButton type="submit" variant="primary" :loading="uploading">
            Upload Note
          </AppButton>
          <RouterLink to="/admin/notes">
            <AppButton variant="secondary">Cancel</AppButton>
          </RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { adminNotesApi }      from '../../api/admin/notes'
import { adminCategoriesApi } from '../../api/admin/categories'
import { adminSubjectsApi }   from '../../api/admin/subjects'
import { useSettingsStore }   from '../../stores/settings'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import AppAlert       from '../../components/common/AppAlert.vue'
import AppButton      from '../../components/common/AppButton.vue'
import AppInput       from '../../components/common/AppInput.vue'
import AppSelect      from '../../components/common/AppSelect.vue'
import AppMultiSelect from '../../components/common/AppMultiSelect.vue'
import AppFileUpload  from '../../components/common/AppFileUpload.vue'

const router        = useRouter()
const settingsStore = useSettingsStore()
const maxFileSizeMb = settingsStore.get('max_file_size_mb', 50)

const uploading       = ref(false)
const uploadProgress  = ref(0)
const error           = ref('')
const success         = ref(false)
const successMsg      = ref('')
const fieldErrors     = reactive({})
const categoryOptions = ref([])
const subjectOptions  = ref([{ value: '', label: 'No Subject' }])

const form = reactive({ file: null, title: '', description: '', category_ids: [], subject_id: '', status: 'draft' })

function onFileSelected(file) {
  if (file && !form.title) {
    form.title = file.name.replace(/\.pdf$/i, '').replace(/[-_]/g, ' ')
  }
}

async function handleUpload() {
  error.value   = ''
  success.value = false
  Object.keys(fieldErrors).forEach(k => delete fieldErrors[k])

  if (!form.file) { fieldErrors.file = 'Please select a PDF file.'; return }

  const data = new FormData()
  data.append('file', form.file)
  data.append('title', form.title)
  data.append('description', form.description)
  data.append('status', form.status)
  if (form.subject_id)  data.append('subject_id', form.subject_id)

  // Append multiple category_ids
  if (form.category_ids.length) {
    form.category_ids.forEach(id => data.append('category_ids[]', id))
  }

  uploading.value = true
  try {
    const res = await adminNotesApi.upload(data, e => {
      uploadProgress.value = Math.round((e.loaded * 100) / e.total)
    })
    success.value = true
    successMsg.value = res.data?.message || 'Note uploaded successfully!'
    setTimeout(() => router.push('/admin/notes'), 1500)
  } catch (err) {
    if (err.response?.data?.errors) Object.assign(fieldErrors, err.response.data.errors)
    else error.value = err.response?.data?.message || 'Upload failed.'
    uploadProgress.value = 0
  } finally {
    uploading.value = false
  }
}

onMounted(async () => {
  const [catRes, subRes] = await Promise.all([
    adminCategoriesApi.list(),
    adminSubjectsApi.list(),
  ])
  categoryOptions.value = catRes.data.data.map(c => ({ value: c.id, label: c.name, color: c.color }))
  subjectOptions.value = [
    { value: '', label: 'No Subject' },
    ...subRes.data.data.map(s => ({ value: s.id, label: s.name }))
  ]
})
</script>
