<template>
  <div v-if="loading" class="text-center text-gray-500 py-4">
    Loading parsed data...
  </div>
  <div v-else class="w-full">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Parsed People</h2>

    <div
      v-if="!parsedNames || parsedNames.length === 0"
      class="text-gray-500 italic"
    >
      No parsed data yet.
    </div>

    <table
      v-else
      class="min-w-full border border-gray-300 bg-white rounded-lg overflow-hidden"
    >
      <thead class="bg-gray-100 border-b border-gray-300">
        <tr>
          <th class="px-4 py-2 text-left">Title</th>
          <th class="px-4 py-2 text-left">First Name</th>
          <th class="px-4 py-2 text-left">Initial</th>
          <th class="px-4 py-2 text-left">Last Name</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(person, index) in parsedNames"
          :key="index"
          class="border-b last:border-0 hover:bg-gray-50"
        >
          <td class="px-4 py-2">{{ person.title ?? "—" }}</td>
          <td class="px-4 py-2">{{ person.first_name ?? "—" }}</td>
          <td class="px-4 py-2">{{ person.initial ?? "—" }}</td>
          <td class="px-4 py-2">{{ person.last_name ?? "—" }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  parsedNames: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
});
</script>
