<template>
    <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick">
      <el-tab-pane v-for = "data in responseSumber" :key="data[0]" :label=" data[1] " :name=" String(data[0]) ">
          <CctvDisplay class="mb-3" :source = "data[3]"/>
          <ProductionLineChart :label="data[1]" :dataSource="data[0]"/>
      </el-tab-pane>
    </el-tabs>
</template>
  
<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import type { TabsPaneContext } from 'element-plus'
import ProductionLineChart from '@/Widgets/Home/ProductionLineChart.vue';
import CctvDisplay from '@/Widgets/Home/CctvDisplay.vue';
import axios from 'axios'

const responseSumber = ref([])
const activeName = ref('3')

const handleClick = (tab: TabsPaneContext, event: Event) => {
  activeName.value = String(tab.props.name)
}

onMounted(async () => {
  try {
      const response2 = await axios.get('http://127.0.0.1:5050/read_sumber')
      responseSumber.value = response2.data
  } catch (error) {
      console.error('Gagal fetch data:', error)
  }
})

</script>