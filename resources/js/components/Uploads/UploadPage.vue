<template>
  <div class="p-8 max-w-xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">
      Upload CSV, XLSX, or XLS File
    </h2>
    <form @submit.prevent="handleSubmit" class="space-y-6" novalidate>
      <label
        class="block cursor-pointer bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg px-4 py-6 text-center transition"
      >
        <span class="block text-sm font-medium text-gray-600 mb-2">
          Click to select a file
        </span>
        <input
          type="file"
          ref="fileInput"
          accept=".csv, .xls, .xlsx"
          @change="handleFileChange"
          class="hidden"
          aria-label="Upload file input"
        />
        <span v-if="file" class="block text-sm text-gray-800 mt-2 font-mono">
          {{ file.name }}
        </span>
      </label>

      <div
        v-if="error"
        role="alert"
        class="text-red-600 text-sm font-medium bg-red-100 border border-red-300 p-2 rounded"
      >
        {{ error }}
      </div>

      <button
        type="submit"
        :disabled="isDisabled"
        :aria-disabled="isDisabled"
        class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded transition"
      >
        <span v-if="parsing">Parsing File...</span>
        <span v-else-if="loading && progress < 100">Uploading {{ progress }}%</span>
        <span v-else-if="loading">Finishing...</span>
        <span v-else>Upload File</span>
      </button>

      <div
        v-if="parsing || loading"
        class="w-full bg-gray-200 h-4 rounded overflow-hidden shadow-inner"
        aria-live="polite"
        aria-atomic="true"
      >
        <div
          class="h-full bg-blue-500 transition-all duration-300 ease-in-out"
          :style="{ width: progress + '%' }"
        ></div>
      </div>

      <div
        v-if="successMessage"
        role="status"
        class="text-green-700 bg-green-100 border border-green-300 p-3 rounded text-sm font-medium"
      >
        {{ successMessage }}
      </div>

      <raw-data-view
        v-if="parsedNames.length || loading || parsing"
        :parsed-names="parsedNames"
        :loading="loading || parsing"
        class="mt-6"
      />
    </form>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import axios from "axios";
import RawDataView from "../Views/RawDataView.vue";

const file = ref(null);
const fileInput = ref(null);
const error = ref("");
const loading = ref(false);
const parsing = ref(false);
const progress = ref(0);
const successMessage = ref("");
const parsedNames = ref([]);
const parsedSuccessfully = ref(false);

const emit = defineEmits(["upload-success"]);

const allowedTypes = new Set([
  "text/csv",
  "application/vnd.ms-excel",
  "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
]);

function handleFileChange(event) {
  const selectedFile = event.target.files?.[0] ?? null;

  // Reset states except parsing/loading since parseFile sets parsing true
  error.value = "";
  successMessage.value = "";
  progress.value = 0;
  parsedNames.value = [];
  parsedSuccessfully.value = false;

  if (selectedFile && allowedTypes.has(selectedFile.type)) {
    file.value = selectedFile;
    // Start parsing after file is set
    parseFile();
  } else {
    file.value = null;
    error.value = "Please upload a valid .csv, .xls, or .xlsx file.";
  }
}

async function parseFile() {
  if (!file.value) return;

  parsing.value = true;
  loading.value = false;
  progress.value = 0;
  error.value = "";
  parsedNames.value = [];
  parsedSuccessfully.value = false;

  const formData = new FormData();
  formData.append("file", file.value);

  try {
    const response = await axios.post("/api/parse", formData, {
      headers: { "Content-Type": "multipart/form-data" },
      onUploadProgress: (event) => {
        if (event.lengthComputable) {
          progress.value = Math.round((event.loaded * 100) / event.total);
        }
      },
    });

    parsedNames.value = response.data.parsedNames || [];
    progress.value = 100;
    parsedSuccessfully.value = true;
  } catch (err) {
    error.value = err.response?.data?.error || "Failed to parse file.";
    parsedSuccessfully.value = false;
  } finally {
    parsing.value = false;
  }
}

const isDisabled = computed(() => !file.value || loading.value || parsing.value || !parsedSuccessfully.value);

async function handleSubmit() {
  if (!file.value) {
    error.value = "No file selected.";
    return;
  }

  loading.value = true;
  parsing.value = false;
  progress.value = 0;
  error.value = "";
  successMessage.value = "";

  const formData = new FormData();
  formData.append("file", file.value);

  try {
    const response = await axios.post("/api/upload", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
      onUploadProgress: (event) => {
        if (event.lengthComputable) {
          progress.value = Math.round((event.loaded * 100) / event.total);
        }
      },
    });

    successMessage.value =
      response.data.message || "File uploaded successfully!";
    file.value = null;
    if (fileInput.value) {
      fileInput.value.value = "";
    }
    parsedNames.value = [];
    parsedSuccessfully.value = false;
    emit("upload-success");
  } catch (err) {
    error.value =
      err.response?.data?.message || "Upload failed. Please try again.";
  } finally {
    loading.value = false;
  }
}
</script>
