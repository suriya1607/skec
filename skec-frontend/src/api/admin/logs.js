import api from '../../utils/axios'

export const adminDashboardApi = {
  get: () => api.get('/admin/dashboard'),
}

export const adminLogsApi = {
  list: (params) => api.get('/admin/logs', { params }),
}
