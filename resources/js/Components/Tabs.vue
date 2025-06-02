<template>
    <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick">
      <el-tab-pane v-for = "warehouse in warehouseData" :key="warehouse.id" :label=" warehouse.warehouse_name " :name=" String(warehouse.id) ">
        <PieChart class="mb-3" :dataSource="warehouse.id" />
        <Carousel v-if="!loading" class="mb-3" :warehouse_id = "warehouse.id" />
        <ProductionColumnChart :label="warehouse.source_name" :warehouseId="warehouse.id"/>
      </el-tab-pane>
    </el-tabs>
</template>
  
<script setup>
import { ref, onMounted } from 'vue'
import PieChart from '@/Widgets/Home/PieChart.vue';
import Carousel from '@/Components/Carousel.vue';
import axios from 'axios'
import ProductionColumnChart from '@/Widgets/Home/ProductionColumnChart.vue';
import { ElMessage } from 'element-plus';

const warehouseData = ref([])
const activeName = ref('1')
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

onMounted(async () => {
  try {
    const response = await axios.get(`${apiBaseUrl}/read_warehouse`)
    warehouseData.value = response.data
  } catch (error) {
    ElMessage({
      message: 'Failed fetching data',
      type: 'error',
    })
  }
})

</script>