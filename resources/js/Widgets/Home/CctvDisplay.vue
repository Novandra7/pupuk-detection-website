<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Cctv</div>
                <div class="font-thin text-gray-700 text-sm">This shows the production</div>
            </div>
        </div>
        <div>
            <!-- <canvas class="w-full" ref="videoCanvas"></canvas> -->
            <img src="http://127.0.0.1:5050/video_feed"/>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const videoCanvas = ref(null);
onMounted(() =>{
    const url = 'ws://127.0.0.1:8082/';
    const player = new JSMpeg.Player(url, {canvas: videoCanvas.value, loop:true});
})
   
</script>