<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Production By Shift</div>
                <div class="font-thin text-gray-700 text-sm">This chart shows the production of each product</div>
            </div>
            <div>
                <BsIconButton icon="cloud-arrow-up" @click="storeData"/>
            </div>
        </div>
        <div ref="chartdiv" class="h-80 w-full"></div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import * as am4core from '@amcharts/amcharts4/core'
import * as am4charts from '@amcharts/amcharts4/charts'
import am4themes_animated from '@amcharts/amcharts4/themes/animated'
import am4themes_pkt_themes from '@granule/Core/Config/am4themes_pkt_themes'
import axios from 'axios'
import BsIconButton from '@granule/Components/BsIconButton.vue';

// Props
const { label, dataSource } = defineProps(['label', 'dataSource'])
const chartdiv = ref(null)
const chartData = ref([])
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL

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

const loadChart = async () => {
  try {
    const response = await axios.get(`${apiBaseUrl}/read_formatted_records/${dataSource}`)
    chartData.value = response.data

    // amCharts license and theme
    am4core.addLicense(import.meta.env.VITE_AMCHARTS_LICENSE_KEY ?? '')
    am4core.useTheme(am4themes_animated)
    am4core.useTheme(am4themes_pkt_themes)

    var chart = am4core.create(chartdiv.value, am4charts.XYChart);
    chart.logo?.dispose();
    chart.colors.step = 2; // Semakin besar, semakin kontras warnanya


    const uniqueShifts = [...new Set(chartData.value.map(item => item.shift_name))];
  
    const result = [];
    
    uniqueShifts.forEach(shift => {
      const shiftData = chartData.value.filter(d => d.shift_name === shift);
      const row = {
        shift: shift.toUpperCase()
      };
    
      shiftData.forEach(item => {
        const prefix = `cctv_${item.cctv_id}`;
        row[`${prefix}_total_granul`] = +(item.total_granul);
        row[`${prefix}_total_prill`] = +(item.total_prill);
        row[`${prefix}_total_subsidi`] = +(item.total_subsidi);
        row[`${prefix}_total_bag`] = +(item.total_bag);
      });   
      result.push(row);
    });   
    chart.data = result;

    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "shift";
    categoryAxis.title.text = "shift";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.minGridDistance = 20;
    categoryAxis.renderer.cellStartLocation = 0.1;
    categoryAxis.renderer.cellEndLocation = 0.9;

    var  valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.min = 0;
    valueAxis.title.text = "value";

    // Create series
    function createSeries(field, name, stacked) {
      var series = chart.series.push(new am4charts.ColumnSeries());
      series.dataFields.valueY = field;
      series.dataFields.categoryX = "shift";
      series.name = name;
      series.columns.template.tooltipText = "{name}: [bold]{valueY}[/]";
      series.stacked = stacked;
      series.columns.template.width = am4core.percent(95);
    }

    // Get all unique field names from the first data object
    const allFields = Object.keys(chart.data[0]);
    console.log(allFields);

    // Filter out the "shift" field
    const dataFields = allFields.filter(field => field !== "shift");

    // Function to create readable label from dynamic field
    function generateLabel(field) {
      const parts = field.split('_');
      // Expected format: cctv_{n}_total_{jenis}
      if (parts.length >= 4 && parts[0] === 'cctv') {
        const cctvNumber = parts[1];
        const jenis = parts.slice(3).join('_'); // handle if more than 1 word after 'total'
        return `CCTV ${cctvNumber} ${capitalizeWords(jenis.replace(/_/g, ' '))}`;
      }
      return capitalizeWords(field.replace(/_/g, ' '));
    }

    // Capitalize each word
    function capitalizeWords(str) {
      return str.replace(/\b\w/g, char => char.toUpperCase());
    }

    // Create series dynamically
    dataFields.forEach(field => {
      const name = generateLabel(field);
      const isGranul = field.includes("granul");
      createSeries(field, name, !isGranul); // Hanya granul yang tidak di-stack
    });

    chart.legend = new am4charts.Legend();
    chart.legend.useDefaultMarker = true;

  } catch (error) {
    console.error('Gagal fetch data:', error)
  }

}
onMounted(async ()=>{
  loadChart()
})
</script>