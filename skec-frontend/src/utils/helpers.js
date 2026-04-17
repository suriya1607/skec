import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
dayjs.extend(relativeTime)

export function formatBytes(bytes, decimals = 2) {
  if (!bytes || bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(decimals))} ${sizes[i]}`
}

export function formatDate(date, format = 'DD MMM YYYY') {
  if (!date) return '—'
  return dayjs(date).format(format)
}

export function formatDateTime(date) {
  if (!date) return '—'
  return dayjs(date).format('DD MMM YYYY, h:mm A')
}

export function timeAgo(date) {
  if (!date) return '—'
  return dayjs(date).fromNow()
}

export function truncate(str, length = 80) {
  if (!str) return ''
  return str.length > length ? str.substring(0, length) + '…' : str
}

export function slugify(text) {
  return text
    .toString()
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\w-]+/g, '')
    .replace(/--+/g, '-')
    .trim()
}

export function debounce(fn, delay = 300) {
  let timer
  return (...args) => {
    clearTimeout(timer)
    timer = setTimeout(() => fn(...args), delay)
  }
}

export function copyToClipboard(text) {
  return navigator.clipboard.writeText(text)
}

export function getInitials(name) {
  if (!name) return '?'
  return name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}
