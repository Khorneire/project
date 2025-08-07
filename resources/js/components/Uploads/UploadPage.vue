<template>
  <div class="p-8 max-w-xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Upload CSV, XLSX, or XLS File</h2>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- File Input -->
      <label
        class="block cursor-pointer bg-gray-50 hover:bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg px-4 py-6 text-center transition"
      >
        <span class="block text-sm font-medium text-gray-600 mb-2">Click to select a file</span>
        <input
          type="file"
          accept=".csv, .xls, .xlsx"
          @change="handleFileChange"
          class="hidden"
        />
        <span v-if="file" class="block text-sm text-gray-800 mt-2 font-mono">{{ file.name }}</span>
      </label>

      <!-- Error Message -->
      <div v-if="error" class="text-red-600 text-sm font-medium bg-red-100 border border-red-300 p-2 rounded">
        {{ error }}
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        :disabled="!file || loading"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded disabled:opacity-50 transition"
      >
        <span v-if="loading && progress < 100">Uploading {{ progress }}%</span>
        <span v-else-if="loading">Finishing...</span>
        <span v-else>Upload File</span>
      </button>

      <!-- Progress Bar -->
      <div v-if="loading" class="w-full bg-gray-200 h-4 rounded overflow-hidden shadow-inner">
        <div
          class="h-full bg-blue-500 transition-all duration-300 ease-in-out"
          :style="{ width: progress + '%' }"
        ></div>
      </div>

      <!-- Success Message -->
      <div
        v-if="successMessage"
        class="text-green-700 bg-green-100 border border-green-300 p-3 rounded text-sm font-medium"
      >
        {{ successMessage }}
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const file = ref(null)
const error = ref('')
const loading = ref(false)
const progress = ref(0)
const successMessage = ref('')

function handleFileChange(event) {
  error.value = ''
  successMessage.value = ''
  progress.value = 0

  const selected = event.target.files[0]

  const allowedTypes = [
    'text/csv',
    'application/vnd.ms-excel', // .xls
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // .xlsx
  ]

  if (selected && allowedTypes.includes(selected.type)) {
    file.value = selected
  } else {
    file.value = null
    error.value = 'Please upload a valid .csv, .xls, or .xlsx file.'
  }
}

async function handleSubmit() {
  if (!file.value) {
    error.value = 'No file selected.'
    return
  }

  loading.value = true
  progress.value = 0

  const formData = new FormData()
  formData.append('file', file.value)

  try {
    const response = await axios.post('/api/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (event) => {
        if (event.lengthComputable) {
          progress.value = Math.round((event.loaded * 100) / event.total)
        }
      }
    })

    successMessage.value = response.data.message || 'File uploaded successfully!'
    file.value = null
  } catch (err) {
    error.value = err.response?.data?.message || 'Upload failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
