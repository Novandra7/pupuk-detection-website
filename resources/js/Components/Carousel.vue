<template>
    <el-carousel v-if="responseData.length > 0" indicator-position="outside" height="auto" autoplay>
        <el-carousel-item v-for="data in responseData" :key="data.id" style="height: auto;">
            <img 
            :key="data.source_name"
            :src="`${apiBaseUrl}/video_feed/${data.source_name}`" 
            :alt="`${data.source_name} is unavailable`" 
            class="w-full h-full object-contain"
            />
            <h3 text="2xl" class="text-center opacity-50">{{ data.source_name }}</h3>
        </el-carousel-item>
    </el-carousel>
</template>


<script setup>
import { ref, onMounted } from 'vue'

const { warehouse_id } = defineProps(['warehouse_id'])
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const responseData = ref([])

onMounted(async () => {
  try {
      const response = await axios.get(`${apiBaseUrl}/read_cctv_sources/${warehouse_id}`)
      responseData.value = response.data
  } catch (error) {
      console.error('Gagal fetch data:', error)
  }
})
</script>