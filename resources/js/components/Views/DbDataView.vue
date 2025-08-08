<template>
  <div>
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Persons List</h2>
    <p>This list just returns the stored schema values, nothing more.</p>
    <ul v-if="!isLoading && persons.length">
      <li v-for="person in persons" :key="person.id">
        <strong>{{ person.title }}</strong>
        {{ person.first_name }}
        <span v-if="person.initial">{{ person.initial }}</span>
        {{ person.last_name }}
      </li>
    </ul>

    <p v-else-if="isLoading">Loading persons...</p>

    <p v-else-if="error">Failed to load persons: {{ error }}</p>

    <p v-else>No persons found.</p>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { repository } from "../repository";

const props = defineProps({
  refreshKey: {
    type: Number,
    default: 0,
  },
});

const persons = ref([]);
const isLoading = ref(true);
const error = ref(null);

const personsRepo = repository();

async function fetchPersons() {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await personsRepo.getData();
    if (Array.isArray(response)) {
      persons.value = response;
    } else {
      persons.value = [];
      error.value = "Unexpected data format from server";
    }
  } catch (err) {
    console.error("Failed to fetch persons:", err);
    error.value = err.message || "Unknown error";
  } finally {
    isLoading.value = false;
  }
}

onMounted(fetchPersons);

watch(() => props.refreshKey, fetchPersons);
</script>
