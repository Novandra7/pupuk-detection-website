<template>
  <el-dropdown placement="bottom-end">
    <el-button>
      <BsIcon icon="funnel"  />
      <span class="ml-1">filter</span>
    </el-button>
    <template #dropdown>
      <el-dropdown-menu>
        <el-dropdown-item v-for="data in responseData" @click=handleSelect(data.id)>{{ data.shift_name }}</el-dropdown-item>
      </el-dropdown-menu>
    </template>
  </el-dropdown>
</template>

<script setup>
import BsIcon from '@granule/Components/BsIcon.vue';
import { ref, onMounted } from 'vue'

const emit = defineEmits(['selectShift']) 

const handleSelect = (id) => {
  emit('selectShift', id) 
}

const { warehouse_id } = defineProps(['warehouse_id'])
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

const responseData = ref([])

onMounted(async () => {
  try {
      const response = await axios.get(`${apiBaseUrl}/read_shift`)
      responseData.value = response.data
  } catch (error) {
      console.error('Gagal fetch data:', error)
  }
})
</script>

