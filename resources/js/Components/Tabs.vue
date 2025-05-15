<template>
    <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick">
      <el-tab-pane v-for = "data in responseData" :key="data.id" :label=" data.warehouse_name " :name=" String(data.id) ">
        <div class="grid grid-cols-2 gap-3 mb-3">
          <PieChart :dataSource="data.id" />
        </div>
        <Carousel class="mb-3" :warehouse_id = "data.id" />
        <!-- <ProductionLineChart :label="data.source_name" :dataSource="data.id"/> -->
        <ProductionColumnChart :label="data.source_name" :dataSource="data.id"/>
      </el-tab-pane>
    </el-tabs>
</template>
  
<script setup>
import { ref, onMounted } from 'vue'
import PieChart from '@/Widgets/Home/PieChart.vue';
import Carousel from '@/Components/Carousel.vue';
import axios from 'axios'
import ProductionColumnChart from '@/Widgets/Home/ProductionColumnChart.vue';

const responseData = ref([])
const activeName = ref('1')
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;


onMounted(async () => {
  try {
      const response = await axios.get(`${apiBaseUrl}/read_warehouse`)
      responseData.value = response.data
  } catch (error) {
      console.error('Gagal fetch data:', error)
  }
})

</script>