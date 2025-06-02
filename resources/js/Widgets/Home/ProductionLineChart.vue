<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Production By {{ cctvName }} in shift {{ shift }}</div>
                <div class="font-thin text-gray-700 text-sm">This chart shows the production of each product</div>
            </div>
        </div>
        <div ref="chartdiv" class="h-80 w-full"></div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";
import am4themes_pkt_themes from "@granule/Core/Config/am4themes_pkt_themes";
import axios from 'axios';

const { cctvName, shift, warehouseId } = defineProps(['cctvName', 'shift', 'warehouseId'])

const chartdiv = ref(null);
const chartDataRef = ref(null);
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

function pivotBagTypeByDate(data) {
  const bagTypes = new Set();
  const grouped = new Map();

  // Langkah 1: Kumpulkan semua jenis bag_type selain "Bag"
  data.forEach(item => {
    if (item.bag_type !== "Bag") {
      bagTypes.add(item.bag_type);
    }
  });

  // Langkah 2: Kelompokkan data berdasarkan tanggal
  data.forEach(item => {
    if (item.bag_type === "Bag") return;

    if (!grouped.has(item.record_date)) {
      // Inisialisasi dengan semua bag_type = 0
      const entry = { record_date: item.record_date };
      bagTypes.forEach(type => entry[type] = 0);
      grouped.set(item.record_date, entry);
    }

    const entry = grouped.get(item.record_date);
    entry[item.bag_type] += item.total_quantity;
  });

  // Langkah 3: Pastikan semua tanggal punya semua bag_type
  for (const entry of grouped.values()) {
    bagTypes.forEach(type => {
      if (!(type in entry)) {
        entry[type] = 0;
      }
    });
  }

  return Array.from(grouped.values());
}

onMounted(async () => {
    function createAxisAndSeries(field, name, opposite, bullet) {
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        if(chart.yAxes.indexOf(valueAxis) != 0){
            valueAxis.syncWithAxis = chart.yAxes.getIndex(0);
        }
        
        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueY = field;
        series.dataFields.dateX = "record_date";
        series.strokeWidth = 2;
        series.yAxis = valueAxis;
        series.name = name;
        series.tooltipText = "{name}: [bold]{valueY}[/]";
        series.tensionX = 0.8;
        series.showOnInit = true;
        
        var interfaceColors = new am4core.InterfaceColorSet();
        
        switch(bullet) {
            case "triangle":
            var bullet = series.bullets.push(new am4charts.Bullet());
            bullet.width = 12;
            bullet.height = 12;
            bullet.horizontalCenter = "middle";
            bullet.verticalCenter = "middle";
            
            var triangle = bullet.createChild(am4core.Triangle);
            triangle.stroke = interfaceColors.getFor("background");
            triangle.strokeWidth = 2;
            triangle.direction = "top";
            triangle.width = 12;
            triangle.height = 12;
            break;
            case "rectangle":
            var bullet = series.bullets.push(new am4charts.Bullet());
            bullet.width = 10;
            bullet.height = 10;
            bullet.horizontalCenter = "middle";
            bullet.verticalCenter = "middle";
            
            var rectangle = bullet.createChild(am4core.Rectangle);
            rectangle.stroke = interfaceColors.getFor("background");
            rectangle.strokeWidth = 2;
            rectangle.width = 10;
            rectangle.height = 10;
            break;
            default:
            var bullet = series.bullets.push(new am4charts.CircleBullet());
            bullet.circle.stroke = interfaceColors.getFor("background");
            bullet.circle.strokeWidth = 2;
            break;
        }
        
        valueAxis.renderer.line.strokeOpacity = 1;
        valueAxis.renderer.line.strokeWidth = 2;
        valueAxis.renderer.line.stroke = series.stroke;
        valueAxis.renderer.labels.template.fill = series.stroke;
        valueAxis.renderer.opposite = opposite;
    }

    am4core.addLicense(import.meta.env.VITE_AMCHARTS_LICENSE_KEY ?? "");
    am4core.useTheme(am4themes_animated);
    am4core.useTheme(am4themes_pkt_themes);

    // Create chart instance for pie chart
    var chart = am4core.create(chartdiv.value, am4charts.XYChart);
    chart.logo?.dispose();

    let url = `${apiBaseUrl}/read_formatted_records/${warehouseId}`;
    const params = [];
    if (cctvName) params.push(`name=${encodeURIComponent(cctvName)}`);
    if (shift) params.push(`shift=${encodeURIComponent(shift)}`);
    if (params.length > 0) {
        url += `?${params.join("&")}`;
    }    

    try {        
        const response = await axios.get(url)
        chartDataRef.value = pivotBagTypeByDate(response.data)      

        chart.data = chartDataRef.value
        
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.minGridDistance = 50;

        const bagTypes = Object.keys(chartDataRef.value[0]).filter(key => key !== "record_date");        

        bagTypes.forEach((type, index) => {
            createAxisAndSeries(type, type, index % 2 === 1, index % 3 === 0 ? "triangle" : index % 3 === 1 ? "rectangle" : "circle");
        });

        chart.legend = new am4charts.Legend();
        chart.cursor = new am4charts.XYCursor();
        
    } catch (error){
        console.error('Gagal fetch data:', error)
    }
});

</script>
