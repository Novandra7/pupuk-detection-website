<template>
	<el-dropdown placement="bottom-start">
     	 <el-button> Channel </el-button>
			<template #dropdown>
				<el-dropdown-menu>
					<el-dropdown-item @click="emitSelect('Kamera 1')">Kamera 1</el-dropdown-item>
					<el-dropdown-item @click="emitSelect('video')">Video</el-dropdown-item>
				</el-dropdown-menu>
			</template>
    </el-dropdown>
</template>

<script setup>
import { inject } from 'vue'

const emit = defineEmits(['update:modelValue']) 

function emitSelect(camera) {
  emit('update:modelValue', camera) 
}

</script>