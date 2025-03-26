<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Cctv</div>
                <div class="font-thin text-gray-700 text-sm">This shows the production of {{ active }}</div>
                <Dropdown class="mt-3"/>
            </div>
        </div>
        <div>
            <!-- <canvas class="w-full" ref="videoCanvas"></canvas> -->
            <img v-show="active === 'Kamera 1'" src="http://127.0.0.1:5050/video_feed/Kamera1"/>
            <img v-show="active === 'video'" src="http://127.0.0.1:5050/video_feed/video"/>
        </div>
    </div>
</template>

<script setup>
import { provide, ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';

const active = ref('Kamera 1');

const setActive = (camera) => {
    active.value = camera
}

provide('setActive', setActive)
provide('active', active)

</script>