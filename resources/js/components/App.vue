<template>
  <div>
    <upload-page
      @upload-success="refreshDbView"
      @parsed-names="setParsedNames"
    />

    <div class="flex space-x-6 p-6">
      <db-data-view
        :refresh-key="refreshKey"
        class="flex-1 border border-gray-300 rounded-lg p-6 bg-gray-50 shadow-inner"
      />
    </div>
  </div>
</template>

<script>
import { ref } from "vue";
import UploadPage from "./Uploads/UploadPage.vue";
import DbDataView from "./Views/DbDataView.vue";

export default {
  name: "App",
  components: { DbDataView, UploadPage },
  setup() {
    const refreshKey = ref(0);
    const parsedNames = ref([]);

    function refreshDbView() {
      refreshKey.value++;
    }

    function setParsedNames(names) {
      parsedNames.value = names;
    }

    return { refreshKey, parsedNames, refreshDbView, setParsedNames };
  },
};
</script>
