<script setup lang="ts">
const { formatCategory } = useFormatter();
const props = defineProps<{
  category: number;
  class?: string;
}>();
</script>

<template>
  <div
    :class="[
      'badge w-10 text-base',
      props.class,
      {
        'badge-success': props.category === 1,
        'badge-warning': props.category === 2,
        'badge-error': props.category === 3,
      },
    ]"
  >
    <span class="text-md">{{ formatCategory(props.category) }}</span>
  </div>
</template>
