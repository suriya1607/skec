import api from '../../utils/axios'

export const adminMediaApi = {
  /**
   * Upload a single image file
   * @param {File} file
   * @param {string} folder - 'logo' | 'slider' | 'gallery' | 'general'
   * @param {Function} onProgress - (percentDone) => void
   */
  upload(file, folder = 'general', onProgress = null) {
    const data = new FormData()
    data.append('file', file)
    data.append('folder', folder)
    return api.post('/admin/media/upload', data, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: onProgress
        ? (e) => onProgress(Math.round((e.loaded * 100) / e.total))
        : undefined,
    })
  },

  /**
   * Upload multiple images at once (slider / gallery)
   * @param {File[]} files
   * @param {Function} onProgress
   */
  uploadMultiple(files, onProgress = null) {
    const data = new FormData()
    files.forEach((f) => data.append('files[]', f))
    return api.post('/admin/media/upload-multiple', data, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: onProgress
        ? (e) => onProgress(Math.round((e.loaded * 100) / e.total))
        : undefined,
    })
  },

  /**
   * Delete a media file by its storage path
   * @param {string} path - e.g. 'media/slider/uuid.jpg'
   */
  delete(path) {
    return api.delete('/admin/media', { data: { path } })
  },

  /**
   * List files in a folder
   * @param {string} folder
   */
  list(folder = 'general') {
    return api.get('/admin/media', { params: { folder } })
  },
}