<template>
  <div class="demo-date-picker">
    <div class="block">
      <el-date-picker
        v-model="value1"
        type="date"
        placeholder="Filter by Date"
        :size="size"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { dayjs } from 'element-plus';
import { ref, watch } from 'vue'

const emit = defineEmits<{
  (e: 'selectDate', date: string): void
}>()

const size = ref('default')
const value1 = ref('')

watch(value1, (newDate) => {
  const formatted = newDate ? dayjs(newDate).format('YYYY-MM-DD') : ''
  emit('selectDate', formatted)
})
</script>

