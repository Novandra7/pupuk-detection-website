<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Production By {{ label }}</div>
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
const { label, dataSource } = defineProps(['label', 'dataSource'])

const chartdiv = ref(null);
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

onMounted(() => {
    am4core.addLicense(import.meta.env.VITE_AMCHARTS_LICENSE_KEY ?? "");
    am4core.useTheme(am4themes_animated);
    am4core.useTheme(am4themes_pkt_themes);

    // Create chart instance for pie chart
    var chart = am4core.create(chartdiv.value, am4charts.XYChart);
    chart.logo?.dispose();

    // Add data
    const fetchData = async (callback) => {
    try {
        const response = await axios.get(`${apiBaseUrl}/read_data/${dataSource}`);
        const data = response.data;
        callback(data);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    fetchData((data) => {
        const formattedData = data.map((item, index) => ({
            id: index + 1,
            bag: item[2],
            granul: item[3],
            subsidi: item[4],
            prill: item[5],
            date: new Date(item[6]),
        }));

        chart.data = formattedData;

        // Buat X Axis (Tanggal)
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.grid.template.location = 0;
        dateAxis.title.text = "Datetime";

        // Buat Y Axis (Granul, Subsidi, Prill)
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = "Values";
        

        // Fungsi untuk menambahkan line series
        function createSeries(field, name) {
            var series = chart.series.push(new am4charts.LineSeries());
            series.dataFields.valueY = field;
            series.dataFields.dateX = "date";
            series.name = name;
            series.strokeWidth = 2;
            series.tooltipText = "{name}: [bold]{valueY}[/]"
            

            var bullet = series.bullets.push(new am4charts.CircleBullet());
            bullet.circle.strokeWidth = 2;
            bullet.circle.radius = 4;
        }

        // Tambahkan tiga garis
        createSeries("granul", "Granul");
        createSeries("subsidi", "Subsidi");
        createSeries("prill", "Prill");
        createSeries("bag", "Bag");

        // Tambahkan Legend
        chart.legend = new am4charts.Legend();

        // Hitung total data
        function calculateTotal(field) {
            return chart.data.reduce((sum, item) => sum + item[field], 0);
        }

        // Tambahkan total ke legend
        chart.legend.itemContainers.template.adapter.add("tooltipText", function(text, target) {
            if (target.dataItem && target.dataItem.dataContext) {
                var series = target.dataItem.dataContext;
                var total = calculateTotal(series.dataFields.valueY);
                return `${series.name}: ${total}`;
            }
            return text;
        });

        chart.cursor = new am4charts.XYCursor();

    });
    
});

</script>
