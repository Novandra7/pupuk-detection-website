<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Production by Shift Today</div>
                <div class="font-thin text-gray-700 text-sm">This chart shows the production of each product</div>
            </div>
        </div>
        <div ref="chartdiv" class="w-full h-80 "></div>
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
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL

onMounted(async () => {
    try {
        const response = await axios.get(`${apiBaseUrl}/read_curdate_records/${dataSource}`)
        const rawData = response.data

        // Gunakan license dan theme
        am4core.addLicense(import.meta.env.VITE_AMCHARTS_LICENSE_KEY ?? '')
        am4core.useTheme(am4themes_animated)
        am4core.useTheme(am4themes_pkt_themes)

        const chart = am4core.create(chartdiv.value, am4charts.PieChart);
        chart.logo?.dispose()

        const innerData = []
        const outerData = []

        rawData.forEach(row => {
            innerData.push({
            shift: row.shift_name,
            value: row.total_bag
            })

            outerData.push({ shift: row.shift_name, product: "Granul", value: row.total_granul })
            outerData.push({ shift: row.shift_name, product: "Subsidi", value: row.total_subsidi })
            outerData.push({ shift: row.shift_name, product: "Prill", value: row.total_prill })
        })

        // Outer Pie Series (Detail Produk per Shift)
        const outerSeries = chart.series.push(new am4charts.PieSeries())
        outerSeries.dataFields.value = "value"
        outerSeries.dataFields.category = "product"
        outerSeries.dataFields.parentCategory = "shift"
        outerSeries.data = outerData
        outerSeries.innerRadius = am4core.percent(60)

        outerSeries.slices.template.tooltipText = "[bold]{shift}[/]\n{product}: {value}"
        // outerSeries.slices.template.stroke = am4core.color("#fff")
        // outerSeries.slices.template.strokeWidth = 1
        // outerSeries.slices.template.strokeOpacity = 1

        // outerSeries.labels.template.text = "{product}"
        // outerSeries.labels.template.wrap = true
        // outerSeries.labels.template.maxWidth = 100
        // outerSeries.labels.template.fontSize = 12
        // outerSeries.labels.template.fill = am4core.color("#333")

        // outerSeries.ticks.template.disabled = false
        // outerSeries.ticks.template.strokeOpacity = 0.3
        outerSeries.labels.template.disabled = true


        // Inner Pie Series (Shift Summary)
        const innerSeries = chart.series.push(new am4charts.PieSeries())
        innerSeries.dataFields.value = "value"
        innerSeries.dataFields.category = "shift"
        innerSeries.data = innerData
        innerSeries.innerRadius = am4core.percent(30)
        innerSeries.radius = am4core.percent(50)

        innerSeries.slices.template.fillOpacity = 0.7
        innerSeries.labels.template.text = "{category}"
        innerSeries.labels.template.fontSize = 13
        innerSeries.labels.template.fill = am4core.color("#000")
        innerSeries.slices.template.tooltipText = "[bold]{category}[/]\nTotal Bag: {value}"
        // innerSeries.labels.template.disabled = true


        // Legend
        chart.legend = new am4charts.Legend()
        chart.legend.position = "bottom"
        chart.legend.maxWidth = 200

    } catch (error) {
        console.error('Gagal fetch data:', error)
    }
})

</script>