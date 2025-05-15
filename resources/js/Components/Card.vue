<template>
    <div>
        <div v-for="data in responseData"  class="w-64 rounded-2xl shadow-md overflow-hidden bg-white">
            <!-- Placeholder untuk gambar CCTV -->
            <div class="bg-gray-300 h-40 w-full relative">
                <template v-if="responseData.length > 0">
                    <img 
                    :key="data.source_name"
                    :src="`${apiBaseUrl}/video_feed/${data.source_name}`" 
                    :alt="`${data.source_name} is unavailable`" 
                    class="absolute top-0 left-0 w-full h-full object-cover"
                    />
                </template>
            </div>
            <!-- Bagian bawah kartu -->
            <div class="flex justify-between items-center p-4">
                    <div>
                        <h3 class="font-semibold text-lg">{{ data.source_name }}</h3>
                        <p class="text-gray-600 text-sm">Gudang 1</p>
                    </div>
                <button class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 focus:outline-none">
                    Show
                </button>
            </div>
      
          <!-- Garis bawah -->
          <hr class="mx-4 border-gray-300" />
        </div>
    </div>
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
