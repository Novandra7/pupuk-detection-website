<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Production by Shift Today</div>
                <div class="font-thin text-gray-700 text-sm">This chart shows the production of each product</div>
            </div>
        </div>
        <div v-show ="rawData.length > 0" ref="chartdiv" class="w-full h-80 "></div>
        <div v-if="rawData.length === 0" class="w-full h-11 flex items-center justify-center text-gray-500 text-sm">
          No data available for today.
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import * as am4core from '@amcharts/amcharts4/core'
import * as am4charts from '@amcharts/amcharts4/charts'
import am4themes_animated from '@amcharts/amcharts4/themes/animated'
import am4themes_pkt_themes from '@granule/Core/Config/am4themes_pkt_themes'
import axios from 'axios'

const { label, dataSource } = defineProps(['label', 'dataSource'])
const chartdiv = ref(null)
const rawData = ref([])
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL

onMounted(async () => {
    try {
        const response = await axios.get(`${apiBaseUrl}/read_formatted_records/${dataSource}`,{
            params: {now: true}
        })
        rawData.value = response.data        

        // Gunakan license dan theme
        am4core.addLicense(import.meta.env.VITE_AMCHARTS_LICENSE_KEY ?? '')
        am4core.useTheme(am4themes_animated)
        am4core.useTheme(am4themes_pkt_themes)

        var container = am4core.create(chartdiv.value, am4core.Container);
        container.width = am4core.percent(100);
        container.height = am4core.percent(100);
        container.layout = "horizontal";

        const chart = container.createChild(am4charts.PieChart);
        container.logo?.dispose()

        const result = [];

        rawData.value.forEach(entry => {
            const { shift_name, source_name, bag_type, total_quantity } = entry;
            
            if (bag_type.toLowerCase() === 'bag') return;
            // Cari entri shift yang sesuai
            let shiftEntry = result.find(s => s.shift === shift_name);
            if (!shiftEntry) {
                shiftEntry = {
                shift: shift_name,
                "total bag": 0,
                subData: []
                };
                result.push(shiftEntry);
            }

            // Tambahkan jumlah ke total bag
            shiftEntry["total bag"] += total_quantity;

            // Cari entri CCTV di dalam shift
            let cctvEntry = shiftEntry.subData.find(s => s.name === source_name);
            if (!cctvEntry) {
                cctvEntry = { name: source_name, value: {} };
                shiftEntry.subData.push(cctvEntry);
            }

            // Tambahkan atau update jumlah tipe pupuk
            if (!cctvEntry.value[bag_type.toLowerCase()]) {
                cctvEntry.value[bag_type.toLowerCase()] = 0;
            }
            cctvEntry.value[bag_type.toLowerCase()] += total_quantity;
        });

        chart.data = result
        
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "total bag";
        pieSeries.dataFields.category = "shift";
        pieSeries.slices.template.states.getKey("active").properties.shiftRadius = 0;
        //pieSeries.labels.template.text = "{category}\n{value.percent.formatNumber('#.#')}%";

        pieSeries.slices.template.events.on("hit", function(event) {
        selectSlice(event.target.dataItem);
        })

        var chart2 = container.createChild(am4charts.PieChart);
        chart2.width = am4core.percent(80);
        chart2.radius = am4core.percent(80);

        // Add and configure Series
        var pieSeries2 = chart2.series.push(new am4charts.PieSeries());
        pieSeries2.dataFields.value = "value";
        pieSeries2.dataFields.category = "name";
        pieSeries2.slices.template.states.getKey("active").properties.shiftRadius = 0;
        //pieSeries2.labels.template.radius = am4core.percent(50);
        //pieSeries2.labels.template.inside = true;
        //pieSeries2.labels.template.fill = am4core.color("#ffffff");
        pieSeries2.labels.template.disabled = true;
        pieSeries2.ticks.template.disabled = true;
        pieSeries2.alignLabels = false;
        pieSeries2.events.on("positionchanged", updateLines);

        var interfaceColors = new am4core.InterfaceColorSet();

        var line1 = container.createChild(am4core.Line);
        line1.strokeDasharray = "2,2";
        line1.strokeOpacity = 0.5;
        line1.stroke = interfaceColors.getFor("alternativeBackground");
        line1.isMeasured = false;

        var line2 = container.createChild(am4core.Line);
        line2.strokeDasharray = "2,2";
        line2.strokeOpacity = 0.5;
        line2.stroke = interfaceColors.getFor("alternativeBackground");
        line2.isMeasured = false;

        var selectedSlice;

        function selectSlice(dataItem) {
            selectedSlice = dataItem.slice;
            var fill = selectedSlice.fill;

            var rawSubData = dataItem.dataContext.subData;
            var transformedSubData = [];

            rawSubData.forEach(function(entry) {
                if (typeof entry.value === "object" && entry.value !== null) {
                // value adalah objek, misalnya {granul: 90, subsidi: 90, prill: 90}
                for (var key in entry.value) {
                    if (entry.value.hasOwnProperty(key)) {
                    transformedSubData.push({
                        name: entry.name + " - " + key,
                        value: entry.value[key]
                    });
                    }
                }
                } else {
                // value adalah angka biasa
                transformedSubData.push(entry);
                }
            });

            var count = transformedSubData.length;
            pieSeries2.colors.list = [];
            for (var i = 0; i < count; i++) {
                pieSeries2.colors.list.push(fill.brighten(i * 2 / count));
            }

            chart2.data = transformedSubData;
            pieSeries2.appear();

            var middleAngle = selectedSlice.middleAngle;
            var firstAngle = pieSeries.slices.getIndex(0).startAngle;
            var animation = pieSeries.animate([
                { property: "startAngle", to: firstAngle - middleAngle },
                { property: "endAngle", to: firstAngle - middleAngle + 360 }
            ], 600, am4core.ease.sinOut);
            animation.events.on("animationprogress", updateLines);

            selectedSlice.events.on("transformed", updateLines);
        }



        function updateLines() {
        if (selectedSlice) {
            var p11 = { x: selectedSlice.radius * am4core.math.cos(selectedSlice.startAngle), y: selectedSlice.radius * am4core.math.sin(selectedSlice.startAngle) };
            var p12 = { x: selectedSlice.radius * am4core.math.cos(selectedSlice.startAngle + selectedSlice.arc), y: selectedSlice.radius * am4core.math.sin(selectedSlice.startAngle + selectedSlice.arc) };

            p11 = am4core.utils.spritePointToSvg(p11, selectedSlice);
            p12 = am4core.utils.spritePointToSvg(p12, selectedSlice);

            var p21 = { x: 0, y: -pieSeries2.pixelRadius };
            var p22 = { x: 0, y: pieSeries2.pixelRadius };

            p21 = am4core.utils.spritePointToSvg(p21, pieSeries2);
            p22 = am4core.utils.spritePointToSvg(p22, pieSeries2);

            line1.x1 = p11.x;
            line1.x2 = p21.x;
            line1.y1 = p11.y;
            line1.y2 = p21.y;

            line2.x1 = p12.x;
            line2.x2 = p22.x;
            line2.y1 = p12.y;
            line2.y2 = p22.y;
        }
        }

        chart.events.on("datavalidated", function() {
        setTimeout(function() {
            selectSlice(pieSeries.dataItems.getIndex(0));
        }, 1000);
        });
    } catch (error) {
        console.error('Gagal fetch data:', error)
    }
})

</script>