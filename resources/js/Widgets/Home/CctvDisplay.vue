<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Cctv</div>
                <div class="font-thin text-gray-700 text-sm">This shows the production of {{ active }}</div>
            </div>
        </div>
        <div>
            <img v-show="source === 'Kamera1'" :src="`${apiBaseUrl}/video_feed/Kamera1`"/>
            <img v-show="source === 'Kamera2'" :src="`${apiBaseUrl}/video_feed/Kamera2`"/>
            <img v-show="source === 'video'" :src="`${apiBaseUrl}/video_feed/video`"/>
        </div>
    </div>
</template>

<script setup>

const { source } = defineProps(['source'])
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

</script>