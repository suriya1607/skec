import api from '../utils/axios'

export const categoriesApi = {
  list: () => api.get('/categories'),
}
