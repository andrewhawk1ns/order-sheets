import axios from 'axios';

const axiosInstance = axios.create({baseURL:process.env.MIX_APP_URL})

axiosInstance.interceptors.response.use(({data}) => {
    return data;
}, (error) => {
    return Promise.reject(error.message);
});

export default {
  createSheets (data) {
    return axiosInstance.post('/api/print-sheets', data)
  },
  getSheet(id) {
    return axiosInstance.get(`/api/print-sheets/${id}`);
  },
  getSheets() {
    return axiosInstance.get(`/api/print-sheets`);
  }
};
