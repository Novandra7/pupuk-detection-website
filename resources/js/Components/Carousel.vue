<template>
  <div v-if="responseData.length > 0">
    <el-carousel indicator-position="outside" height="auto" autoplay>
      <el-carousel-item v-for="data in responseData" :key="data.id" style="cursor: pointer" @click="openModal(data.source_name)">
        <img
          :src="`${apiBaseUrl}/thumbnail/${data.source_name}`"
          :alt="`${data.source_name} thumbnail`"
          class="w-full h-full object-contain brightness-75"
        />
        <div class="absolute inset-0 flex items-center justify-center">
        <svg class="w-16 h-16 text-white opacity-80 hover:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 24 24">
            <path d="M8 5v14l11-7z" />
        </svg>
        </div>
      </el-carousel-item>
    </el-carousel>

    <!-- Modal -->
    <el-dialog v-model="showModal" width="720px" top="5vh" :title="selectedSource">
      <div class="flex justify-center items-center">
        <img
          v-if="selectedSource"
          :src="`${apiBaseUrl}/video_feed/${selectedSource}?t=${Date.now()}`"
          class="rounded-lg max-h-[480px] w-full object-contain"
          alt="CCTV disable"
        />
      </div>
    </el-dialog>
  </div>
  <div v-else class="w-full h-11 flex items-center justify-center text-gray-500 text-sm p-4 rounded-xl border border-gray-400 flex-1">
    No CCTV available.
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { ElMessage } from 'element-plus'

const { warehouse_id } = defineProps(['warehouse_id'])
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL

const responseData = ref([])
const showModal = ref(false)
const selectedSource = ref(null)

const fetchData = async () => {
  try {
    const response = await axios.get(`${apiBaseUrl}/read_cctv_sources/${warehouse_id}`)
    responseData.value = response.data
  } catch (error) {
    ElMessage.error('Gagal mengambil data CCTV')
  }
}

const openModal = async (sourceName) => {
  selectedSource.value = sourceName
  showModal.value = true
}
fetchData()
</script>

<style scoped>
.el-carousel__item {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 320px;
}

.brightness-75 {
  filter: brightness(0.6);
}

</style>
