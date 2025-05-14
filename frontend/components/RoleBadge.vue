<script setup lang="ts">
const roles: { [key: number]: { name: string } } = {
  0: { name: "Admin" },
  1: { name: "Nurse" },
};
const props = defineProps<{
  role: number;
  variant?: string;
}>();
</script>

<template>
  <div class="badge badge-accent">
    {{ roles[props.role]?.name ?? "-" }}
  </div>
</template>
