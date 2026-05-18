<template>
  <div
    id="pdf-viewer-container"
    class="pdf-container flex flex-col bg-gray-900 select-none"
    style="height: 100dvh;"
    @contextmenu.prevent
  >
    <!-- Controls bar -->
    <PdfControls
      :current-page="currentPage"
      :total-pages="totalPages"
      :zoom="zoom"
      :loading="isLoading"
      :note-id="noteId"
      @prev="prevPage"
      @next="nextPage"
      @zoom-in="zoomIn"
      @zoom-out="zoomOut"
      @fit-width="fitWidth"
      @page-change="goToPage"
    />

    <!-- PDF canvas area -->
    <div
      ref="viewerContainer"
      class="flex-1 overflow-y-auto overflow-x-auto flex flex-col items-center gap-4 py-4 sm:py-6 px-2 sm:px-4"
      style="overscroll-behavior: contain; -webkit-overflow-scrolling: touch;"
    >
      <!-- Loading state -->
      <div v-if="isLoading" class="flex items-center justify-center w-full h-full">
        <div class="text-center text-white">
          <div class="w-12 h-12 border-4 border-white/20 border-t-white rounded-full animate-spin mx-auto mb-4"/>
          <p class="text-white/70 text-sm">Loading document…</p>
        </div>
      </div>

      <!-- Error -->
      <div v-else-if="loadError" class="flex items-center justify-center w-full h-full text-white text-center">
        <div class="px-4">
          <ExclamationCircleIcon class="w-12 h-12 text-red-400 mx-auto mb-3" />
          <p class="text-lg font-semibold">Failed to load document</p>
          <p class="text-white/50 text-sm mt-1">{{ loadError }}</p>
        </div>
      </div>

      <!-- Canvases -->
      <template v-else>
        <div
          v-for="pageNum in renderedPages"
          :key="pageNum"
          class="relative shadow-2xl max-w-full"
        >
          <canvas :ref="el => setCanvasRef(el, pageNum)" class="block max-w-full h-auto" />
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import * as pdfjsLib from 'pdfjs-dist'
import dayjs from 'dayjs'
import PdfControls from './PdfControls.vue'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import { notesApi } from '../../api/student/notes'

// pdfjsLib.GlobalWorkerOptions.workerSrc = new URL(
//   'pdfjs-dist/build/pdf.worker.min.mjs',
//   import.meta.url
// ).href
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@4.10.38/build/pdf.worker.min.mjs'

const props = defineProps({
  noteId:            { type: Number, required: true },
  streamUrl:         { type: String, required: true },
  studentEmail:      { type: String, required: true },
  studentRegNo:      { type: String, default: '' },
  studentName:       { type: String, default: '' },
  watermarkOpacity:  { type: Number, default: 0.15 },
  watermarkTemplate: { type: String, default: '{name} | {email} | {reg_no} | {date}' },
})

const emit = defineEmits(['loaded', 'error'])

const isLoading       = ref(true)
const loadError       = ref('')
const currentPage     = ref(1)
const totalPages      = ref(0)
const zoom            = ref(1.0)
const viewerContainer = ref(null)
const canvasRefs      = {}
const openedAt        = ref(Date.now())

let pdfDoc = null

const renderedPages = computed(() => {
  if (!totalPages.value) return []
  return Array.from({ length: totalPages.value }, (_, i) => i + 1)
})

function setCanvasRef(el, pageNum) {
  if (el) canvasRefs[pageNum] = el
}

onMounted(async () => {
  // Auto fit-width on mobile
  if (window.innerWidth < 640) zoom.value = 0.65
  await loadPdf()
  addSecurityListeners()
})

async function loadPdf() {
  isLoading.value = true
  loadError.value = ''
  try {
    const response = await fetch(props.streamUrl, {
      headers: {
        Authorization:    `Bearer ${localStorage.getItem('skec_token')}`,
        'X-Session-Token': localStorage.getItem('skec_session_token'),
      },
    })
    if (!response.ok) throw new Error(`HTTP ${response.status}`)
    const arrayBuffer = await response.arrayBuffer()
    pdfDoc = await pdfjsLib.getDocument({ data: arrayBuffer }).promise
    totalPages.value = pdfDoc.numPages
    emit('loaded', pdfDoc.numPages)
    isLoading.value = false
    await new Promise(r => setTimeout(r, 100))
    for (let i = 1; i <= totalPages.value; i++) {
      await renderPage(i)
    }
  } catch (err) {
    loadError.value = err.message || 'Unknown error'
    isLoading.value = false
    emit('error', err)
  }
}

async function renderPage(pageNum) {
  if (!pdfDoc) return
  const canvas = canvasRefs[pageNum]
  if (!canvas) return
  const page     = await pdfDoc.getPage(pageNum)
  const viewport = page.getViewport({ scale: zoom.value })
  canvas.width  = viewport.width
  canvas.height = viewport.height
  const ctx = canvas.getContext('2d')
  await page.render({ canvasContext: ctx, viewport, renderTextLayer: false }).promise
  drawWatermark(ctx, canvas.width, canvas.height)
}

function drawWatermark(ctx, w, h) {
  const text = props.watermarkTemplate
    .replaceAll('{name}', props.studentName || '')
    .replaceAll('{email}', props.studentEmail || '')
    .replaceAll('{reg_no}', props.studentRegNo || '')
    .replaceAll('{course}', props.studentCourse || '')
    .replaceAll('{date}', dayjs().format('DD/MM/YYYY'))
    .replaceAll('{time}', dayjs().format('hh:mm A'))

  ctx.save()

  ctx.globalAlpha = props.watermarkOpacity || 0.10

  // slightly smaller font
  ctx.font = 'bold 15px Arial'

  ctx.fillStyle = '#1A3C6E'
  ctx.textAlign = 'center'

  // better angle
  ctx.rotate(-Math.PI / 6)

  const textWidth = ctx.measureText(text).width

  // tighter spacing without overlap
  const gapX = textWidth + 70
  const gapY = 130

  for (let y = -h; y < h * 2; y += gapY) {
    for (let x = -w; x < w * 2; x += gapX) {
      ctx.fillText(text, x, y)
    }
  }

  ctx.restore()
}

function prevPage() { if (currentPage.value > 1) { currentPage.value--; scrollToPage(currentPage.value) } }
function nextPage() { if (currentPage.value < totalPages.value) { currentPage.value++; scrollToPage(currentPage.value) } }
function goToPage(n) {
  const p = parseInt(n)
  if (p >= 1 && p <= totalPages.value) { currentPage.value = p; scrollToPage(p) }
}
function scrollToPage(pageNum) {
  const canvas = canvasRefs[pageNum]
  if (canvas) canvas.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
async function zoomIn()   { zoom.value = Math.min(zoom.value + 0.25, 3.0); await rerenderAll() }
async function zoomOut()  { zoom.value = Math.max(zoom.value - 0.25, 0.3); await rerenderAll() }
async function fitWidth() {
  if (viewerContainer.value) {
    const containerW = viewerContainer.value.clientWidth - 32
    zoom.value = containerW / 600
  }
  await rerenderAll()
}
async function rerenderAll() {
  for (let i = 1; i <= totalPages.value; i++) await renderPage(i)
}

function onKeydown(e) {
  const blocked = (
    (e.ctrlKey || e.metaKey) && ['s', 'p', 'u'].includes(e.key.toLowerCase()) ||
    (e.ctrlKey && e.shiftKey && e.key === 'I') || e.key === 'F12'
  )
  if (blocked) { e.preventDefault(); e.stopPropagation() }
}
function addSecurityListeners() { window.addEventListener('keydown', onKeydown, true) }

onUnmounted(async () => {
  window.removeEventListener('keydown', onKeydown, true)
  const duration = Math.floor((Date.now() - openedAt.value) / 1000)
  try { await notesApi.logAccess(props.noteId, { action: 'closed', duration_seconds: duration }) } catch {}
  if (pdfDoc) { pdfDoc.destroy(); pdfDoc = null }
})
</script>

<style>
@media print { #pdf-viewer-container { display: none !important; } }
</style>