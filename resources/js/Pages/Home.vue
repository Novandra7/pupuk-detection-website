<template>
    <Head title="Dashboard" />
    <MainLayout title="Dashboard">
        <Welcome class="mb-3" />
        <div class = "mb-3">
            <Tabs :loading="loading" :failedCctv="failedCctv" :refreshed="refreshed"/>
        </div>
    </MainLayout>
</template>
<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import Welcome from '@/Widgets/Home/Welcome.vue';
import Tabs from '@/Components/Tabs.vue';

const { cctv } = defineProps(['cctv']);
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const loading = ref(true);
const failedCctv = ref([]);


onMounted(async () => {
  const promises = cctv.map(async (element) => {
    try {
      await axios.get(`${apiBaseUrl}/start_predict/${element.source_name}`);
    } catch (error) {
      console.error('Gagal memulai prediksi pada :', element.source_name);
      failedCctv.value.push(element.source_name);
    }
  });

  await Promise.all(promises);
  loading.value = false;

});
</script>
