<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Batch</h1>

    <!-- Global alerts -->
    <AppAlert v-if="deleteSuccess" type="success" :message="deleteSuccess" class="mb-4" dismissible />
    <AppAlert v-if="deleteError"   type="error"   :message="deleteError"   class="mb-4" dismissible />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Add / Edit form -->
      <div class="card h-fit">
        <h2 class="font-semibold text-gray-800 mb-4">{{ editing ? 'Edit Batch' : 'New Batch' }}</h2>
        <form @submit.prevent="save" class="space-y-4">
          <AppInput v-model="form.name"  label="Name"  placeholder="Mathematics" required @input="autoSlug" />
          <AppInput v-model="form.slug"  label="Slug"  placeholder="mathematics" required />
          <AppInput v-model="form.color" label="Color" type="color" />
          <AppInput v-model="form.icon"  label="Icon"  placeholder="book-open" />

          <!-- Open in Browser toggle -->
          <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 bg-gray-50">
            <div>
              <p class="text-sm font-medium text-gray-700">Open in Browser PDF Viewer</p>
              <p class="text-xs text-gray-400 mt-0.5">Notes in this batch open in the default browser viewer</p>
            </div>
            <button
              type="button"
              @click="form.open_in_browser = !form.open_in_browser"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                form.open_in_browser ? 'bg-blue-600' : 'bg-gray-300'
              ]"
              :aria-checked="form.open_in_browser"
              role="switch"
            >
              <span
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  form.open_in_browser ? 'translate-x-5' : 'translate-x-0'
                ]"
              />
            </button>
          </div>

          <!-- Free Notes toggle -->
          <div class="flex items-center justify-between p-3 rounded-xl border border-green-100 bg-green-50">
            <div>
              <p class="text-sm font-medium text-green-800">Free Notes Batch</p>
              <p class="text-xs text-green-600 mt-0.5">Notes visible publicly on the landing page (no login)</p>
            </div>
            <button
              type="button"
              @click="form.is_free = !form.is_free"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                form.is_free ? 'bg-green-600' : 'bg-gray-300'
              ]"
              :aria-checked="form.is_free"
              role="switch"
            >
              <span
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  form.is_free ? 'translate-x-5' : 'translate-x-0'
                ]"
              />
            </button>
          </div>

          <AppButton type="submit" variant="primary" size="sm" :loading="saving" class="w-full">
            {{ editing ? 'Update' : 'Create' }}
          </AppButton>
          <AppButton v-if="editing" variant="ghost" size="sm" class="w-full" @click="reset">Cancel</AppButton>
        </form>
      </div>

      <!-- List -->
      <div class="lg:col-span-2 card">
        <AppLoader v-if="loading" />
        <div v-else class="space-y-2">
          <div
            v-for="cat in categories" :key="cat.id"
            class="flex items-center gap-4 px-4 py-3 rounded-2xl border border-gray-100 bg-white hover:border-gray-200 hover:shadow-sm transition-all duration-150"
          >
            <!-- Color dot + name/notes -->
            <span class="w-3 h-3 rounded-full flex-shrink-0" :style="{ background: cat.color || '#999' }" />
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-sm text-gray-800 truncate">{{ cat.name }}</p>
              <p class="text-xs text-gray-400">{{ cat.notes_count }} note{{ cat.notes_count !== 1 ? 's' : '' }}</p>
            </div>

            <!-- ── Toggle group ─────────────────────────── -->
            <div class="flex items-center gap-2 flex-shrink-0">

              <!-- Browser PDF toggle pill -->
              <button
                @click="toggleOpenInBrowser(cat)"
                :title="cat.open_in_browser ? 'Disable browser PDF viewer' : 'Enable browser PDF viewer'"
                :class="[
                  'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border transition-all duration-150 select-none',
                  cat.open_in_browser
                    ? 'bg-blue-50 border-blue-200 text-blue-700'
                    : 'bg-gray-50 border-gray-200 text-gray-400 hover:text-gray-600'
                ]"
              >
                <span :class="['w-3.5 h-3.5 rounded-full border-2 transition-colors duration-150 flex-shrink-0', cat.open_in_browser ? 'bg-blue-500 border-blue-500' : 'bg-white border-gray-300']" />
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3" />
                </svg>
                <span class="hidden sm:inline">Browser PDF</span>
              </button>

              <!-- Free toggle pill -->
              <button
                @click="toggleIsFree(cat)"
                :title="cat.is_free ? 'Remove from free notes' : 'Make free notes batch'"
                :class="[
                  'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border transition-all duration-150 select-none',
                  cat.is_free
                    ? 'bg-green-50 border-green-200 text-green-700'
                    : 'bg-gray-50 border-gray-200 text-gray-400 hover:text-gray-600'
                ]"
              >
                <span :class="['w-3.5 h-3.5 rounded-full border-2 transition-colors duration-150 flex-shrink-0', cat.is_free ? 'bg-green-500 border-green-500' : 'bg-white border-gray-300']" />
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <span class="hidden sm:inline">Free</span>
              </button>

              <!-- Active status badge -->
              <span
                :class="[
                  'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border',
                  cat.is_active
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                    : 'bg-gray-50 border-gray-200 text-gray-400'
                ]"
              >
                <span :class="['w-1.5 h-1.5 rounded-full', cat.is_active ? 'bg-emerald-500' : 'bg-gray-300']" />
                {{ cat.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>

            <!-- ── Edit / Delete icon buttons ──────────── -->
            <div class="flex items-center gap-1 flex-shrink-0">
              <button
                @click="startEdit(cat)"
                title="Edit batch"
                class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                </svg>
              </button>
              <button
                @click="openDeleteModal(cat)"
                title="Delete batch"
                class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Delete Security Modal ──────────────────────────────────────── -->
    <AppModal v-model="deleteModal.open" title="Delete Batch — Security Required">
      <div class="space-y-4">

        <!-- Danger warning banner -->
        <div class="rounded-xl bg-red-50 border border-red-200 p-4">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <div>
              <p class="font-semibold text-red-700 text-sm">⚠️ This action is irreversible!</p>
              <p class="text-red-600 text-xs mt-1 leading-relaxed">
                Deleting <strong>"{{ deleteModal.cat?.name }}"</strong> will permanently:
              </p>
              <ul class="text-red-600 text-xs mt-2 space-y-0.5 list-disc list-inside">
                <li>Delete <strong>all notes</strong> in this batch (including PDF files)</li>
                <li>Delete <strong>all student accounts</strong> enrolled in this batch</li>
                <li>Remove all associated data (sessions, logs, reviews, notifications)</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Security key input -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">
            🔐 Enter Security Key to confirm deletion
          </label>
          <input
            v-model="deleteModal.key"
            type="password"
            placeholder="Enter security key…"
            class="input-base"
            autocomplete="new-password"
            @keydown.enter="confirmDelete"
          />
          <p class="text-xs text-gray-400 mt-1">
            The key can be changed in <strong>Settings → Security → Batch Delete Key</strong>.
          </p>
          <!-- Error message -->
          <div v-if="deleteModal.error" class="mt-2 flex items-center gap-1.5 text-red-600 text-xs font-medium">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            {{ deleteModal.error }}
          </div>
        </div>

        <!-- Action buttons -->
        <div class="flex gap-2 justify-end pt-1">
          <AppButton variant="secondary" @click="deleteModal.open = false">Cancel</AppButton>
          <AppButton
            variant="danger"
            :loading="deleteModal.loading"
            :disabled="!deleteModal.key.trim()"
            @click="confirmDelete"
          >
            🗑 Delete Batch Permanently
          </AppButton>
        </div>
      </div>
    </AppModal>

  </div>
</template>


<script setup>
import { ref, reactive, onMounted } from 'vue'
import { adminCategoriesApi } from '../../api/admin/categories'
import { slugify } from '../../utils/helpers'
import AppInput  from '../../components/common/AppInput.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppBadge  from '../../components/common/AppBadge.vue'
import AppLoader from '../../components/common/AppLoader.vue'
import AppAlert  from '../../components/common/AppAlert.vue'
import AppModal  from '../../components/common/AppModal.vue'

const loading       = ref(false)
const saving        = ref(false)
const editing       = ref(null)
const categories    = ref([])
const deleteSuccess = ref('')
const deleteError   = ref('')
const form          = reactive({ name: '', slug: '', color: '#3498DB', icon: '', is_active: true, open_in_browser: false, is_free: false })

// ── Delete modal state ──────────────────────────────────────────────────
const deleteModal = reactive({
  open:    false,
  cat:     null,
  key:     '',
  loading: false,
  error:   '',
})

function openDeleteModal(cat) {
  deleteModal.cat     = cat
  deleteModal.key     = ''
  deleteModal.error   = ''
  deleteModal.loading = false
  deleteModal.open    = true
  deleteSuccess.value = ''
  deleteError.value   = ''
}

async function confirmDelete() {
  if (!deleteModal.key.trim()) return

  deleteModal.loading = true
  deleteModal.error   = ''

  try {
    const res = await adminCategoriesApi.delete(deleteModal.cat.id, deleteModal.key.trim())
    const data = res.data?.data

    deleteModal.open    = false
    deleteSuccess.value = data
      ? `✅ Batch "${data.batch}" deleted — ${data.notes_deleted} note(s) and ${data.students_deleted} student(s) removed.`
      : `Batch "${deleteModal.cat.name}" deleted successfully.`

    fetchCategories()
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to delete batch.'
    // Show inline if wrong key, global if server error
    if (err.response?.status === 403 || err.response?.status === 422) {
      deleteModal.error = msg
    } else {
      deleteModal.open  = false
      deleteError.value = msg
    }
  } finally {
    deleteModal.loading = false
  }
}

// ── Existing functions ──────────────────────────────────────────────────
function autoSlug() { form.slug = slugify(form.name) }

async function fetchCategories() {
  loading.value = true
  const res = await adminCategoriesApi.list()
  categories.value = res.data.data
  loading.value = false
}

async function save() {
  saving.value = true
  try {
    if (editing.value) await adminCategoriesApi.update(editing.value.id, form)
    else               await adminCategoriesApi.create(form)
    reset()
    fetchCategories()
  } finally { saving.value = false }
}

function startEdit(cat) {
  editing.value = cat
  Object.assign(form, {
    name: cat.name,
    slug: cat.slug,
    color: cat.color,
    icon: cat.icon,
    is_active: cat.is_active,
    open_in_browser: cat.open_in_browser ?? false,
    is_free: cat.is_free ?? false,
  })
}

function reset() {
  editing.value = null
  Object.assign(form, { name: '', slug: '', color: '#3498DB', icon: '', is_active: true, open_in_browser: false, is_free: false })
}

async function toggleOpenInBrowser(cat) {
  const newVal = !cat.open_in_browser
  cat.open_in_browser = newVal
  try {
    await adminCategoriesApi.update(cat.id, {
      name: cat.name,
      slug: cat.slug,
      open_in_browser: newVal,
    })
  } catch {
    cat.open_in_browser = !newVal
  }
}

async function toggleIsFree(cat) {
  const newVal = !cat.is_free
  cat.is_free = newVal
  try {
    await adminCategoriesApi.update(cat.id, {
      name: cat.name,
      slug: cat.slug,
      is_free: newVal,
    })
  } catch {
    cat.is_free = !newVal
  }
}

onMounted(fetchCategories)
</script>
