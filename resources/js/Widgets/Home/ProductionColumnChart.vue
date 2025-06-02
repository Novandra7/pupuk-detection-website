<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Production By Shift <span v-if="selectedDate">in {{ selectedDate }}</span></div>
                <div class="font-thin text-gray-700 text-sm">This chart shows the production of each product</div>
            </div>
            <div class="flex flex-row items-center">  
                <DatePicker @selectDate="handleDate" />
                <!-- <BsIconButton class="ml-2" :icon="isRunning ? 'pause' : 'clock'" :title="isRunning ? 'Stop Scheduler' : 'Start Scheduler'" @click="toggleScheduler" /> -->
                <BsIconButton class="ml-2" icon="cloud-arrow-up" @click="storeData" title ="Store Data"/>
                <BsIconButton class="ml-2" icon="arrow-path" @click="loadChart(null)"/>
            </div>
        </div>
        <div ref="chartdiv" class="h-80 w-full" v-show="chartDataRef.length > 0"></div>
        <div v-if="chartDataRef.length === 0" class="w-full h-11 flex items-center justify-center text-gray-500 text-sm">
          No data available for today.
        </div>
    </div>

    <el-dialog v-model="dialogVisible" :modal="true" :width="1200" modal-class="overide-animation">
      <ProductionLineChart :key="`${modalCctv}-${modalShift}`" :cctvName="modalCctv" :shift="modalShift" :warehouseId="warehouseId"/>
    </el-dialog>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import * as am4core from '@amcharts/amcharts4/core'
import * as am4charts from '@amcharts/amcharts4/charts'
import am4themes_animated from '@amcharts/amcharts4/themes/animated'
import am4themes_pkt_themes from '@granule/Core/Config/am4themes_pkt_themes'
import axios from 'axios'
import DatePicker from '@/Components/DatePicker.vue'
import BsIconButton from '@granule/Components/BsIconButton.vue';
import ProductionLineChart from '@/Widgets/Home/ProductionLineChart.vue';
import { ElMessage } from 'element-plus'


// Props
const { label, warehouseId } = defineProps(['label', 'warehouseId'])
const chartdiv = ref(null)
const chartDataRef = ref([])
const selectedDate = ref(null)
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL

const modalCctv = ref(null)
const modalShift = ref(null)
const dialogVisible = ref(false)
const isRunning = ref(false)

function handleDate(date) {
  selectedDate.value = date
  loadChart(date)
}

async function toggleScheduler() {
  const endpoint = isRunning.value ? '/stop_scheduler' : '/start_scheduler'
  const response = await axios.get(`${apiBaseUrl}${endpoint}`)
  ElMessage({
    message: response.data.message,
    type: response.data.status
  })
  isRunning.value = !isRunning.value
  
}

async function checkSchedulerStatus() {
  try {
    const response = await axios.get(`${apiBaseUrl}/scheduler_status`)
    isRunning.value = response.data.running
  } catch (error) {
    console.error('Gagal mengambil status scheduler:', error)
  }
}

const storeData = async () => {
  try {
    const response = await axios.get(`${apiBaseUrl}/write_records`)

    if (response.status === 200) {
      await loadChart()
      ElMessage({
        message: 'Data berhasil disimpan',
        type: 'success',
      })
    } else {
      ElMessage({
        message: `Gagal menyimpan data: status ${response.status}`,
        type: 'error',
      })
    }
  } catch (error) {
    ElMessage({
      message: `Error saat menyimpan data: ${error.message}`,
      type: 'error',
    })
  }
}

const loadChart = async (date) => {
  try {
    let url = `${apiBaseUrl}/read_formatted_records/${warehouseId}`;
    if (date) {
      url += `?date=${date}`;
    }

    const response = await axios.get(url);
    chartDataRef.value = response.data;    

    // amCharts license and theme
    am4core.addLicense(import.meta.env.VITE_AMCHARTS_LICENSE_KEY ?? '')
    am4core.useTheme(am4themes_animated)
    am4core.useTheme(am4themes_pkt_themes)

    var chart = am4core.create(chartdiv.value, am4charts.XYChart);
    chart.logo?.dispose();
    chart.colors.step = 1; // Semakin besar, semakin kontras warnanya


    const chartDataMap = {};

    // Merubah Format Data
    chartDataRef.value.forEach((item) => {
      const shift = item.shift_name;
      const source = item.source_name.toLowerCase().replace(/\W+/g, "_"); // CCTV-1 → cctv_1
      const bagType = item.bag_type.toLowerCase(); // Bag → bag

      if (bagType === "bag") return; // Skip jenis "Bag" (sesuai logika kamu)

      const key = shift;
      if (!chartDataMap[key]) {
        chartDataMap[key] = { shift: shift };
      }

      const columnName = `${source}_total_${bagType}`;

      if (!chartDataMap[key][columnName]) {
        chartDataMap[key][columnName] = 0;
      }

      chartDataMap[key][columnName] += item.total_quantity;
    });

    // Ubah object jadi array
    const chartDataFix = Object.values(chartDataMap);    

    chart.data = chartDataFix;

    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "shift";
    categoryAxis.title.text = "Shift";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.minGridDistance = 20;
    categoryAxis.renderer.cellStartLocation = 0.1;
    categoryAxis.renderer.cellEndLocation = 0.9;

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.min = 0;
    valueAxis.title.text = "Value";

    // Create series
    function createSeries(field, name, stacked) {
      var series = chart.series.push(new am4charts.ColumnSeries());
      series.dataFields.valueY = field;
      series.dataFields.categoryX = "shift";
      series.name = name;
      series.columns.template.tooltipText = "{name}: [bold]{valueY}[/]";
      series.stacked = stacked;
      series.columns.template.width = am4core.percent(95);

      series.columns.template.events.on("hit", function(ev) {
        const label = ev.target.dataItem.component.name;
        const name = label.split(" - ")[0].replace(" ", "-").toUpperCase();
        const xAxisLabel = ev.target.dataItem.categoryX;
        modalCctv.value = name
        modalShift.value = xAxisLabel           
        dialogVisible.value = true  
    })
    }

    // Generate fields from chart.data
    const fieldSet = new Set();
    chart.data.forEach(row => {
      Object.keys(row).forEach(key => {
        if (key !== "shift") fieldSet.add(key);
      });
    });

    const fields = Array.from(fieldSet);

    // Kelompokkan berdasarkan sumber (misal: cctv_1, cctv_2)
    const groupedBySource = {};
    fields.forEach(field => {
      const source = field.split("_").slice(0, 2).join(" ").toUpperCase(); // cctv_1
      if (!groupedBySource[source]) groupedBySource[source] = [];
      groupedBySource[source].push(field);
    });

    // Buat series berdasarkan urutan kelompok sumber
    Object.entries(groupedBySource).forEach(([source, fieldList]) => {
      fieldList.forEach((field, index) => {
        const type = field.split("_").slice(3).join(" "); // bag, granul, subsidi
        const label = `${source} - ${type.charAt(0).toUpperCase()}${type.slice(1)}`;
        const isStacked = index > 0; // hanya yang pertama tidak di-stack
        createSeries(field, label, isStacked);
      });
    });

    chart.legend = new am4charts.Legend();

  } catch (error) {
    console.error('Gagal fetch data:', error)
  }

}
onMounted(async ()=>{
  checkSchedulerStatus()
  loadChart()
})
</script>