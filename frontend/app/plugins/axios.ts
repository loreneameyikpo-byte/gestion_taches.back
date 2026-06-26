import * as axios from 'axios'
import config from '../../config'

axios.default.defaults.baseURL = config.api_url

export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.provide('axios', axios.default)
})
