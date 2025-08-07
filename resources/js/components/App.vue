<template>
  <div>
    <h1>Hello from App.vue</h1>
    <upload-page />
  </div>
</template>

<script>
import UploadPage from "./Uploads/UploadPage.vue";

export default {
  name: 'App',
  components: {UploadPage}
};
</script>
