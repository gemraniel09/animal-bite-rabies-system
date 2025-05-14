<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  title: string;
  number: number;
  color: string;
}>();

const formattedNumber = computed(() => {
  return props.number.toLocaleString('en-US', );
});
</script>

<template>
  <div class="rounded-2xl h-[200px] w-[200px] shadow-sm p-6 border border-gray-200">
    <div class="font-bold text-center">{{ title }}</div>
    <div class="text-center text-[50px] mt-[17px]" :style="{ color: color }">
      {{ formattedNumber }}
    </div>
  </div>
</template>
