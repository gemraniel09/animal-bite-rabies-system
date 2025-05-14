<script setup lang="ts">
import { Sun, Moon } from "lucide-vue-next";

const theme = ref("dark");
const setTheme = (newTheme: string) => {
  theme.value = newTheme;
  localStorage.setItem("theme", newTheme);
};
useHead({
  htmlAttrs: {
    "data-theme": theme,
  },
});
onMounted(() => {
  const savedTheme = localStorage.getItem("theme") || "dark";
  setTheme(savedTheme);
});
</script>

<template>
  <div class="dropdown dropdown-end">
    <button
      @click="setTheme(theme === 'dark' ? (theme = 'light') : (theme = 'dark'))"
      class="btn btn-ghost flex items-center gap-1 btn-sm btn btn-circle"
    >
      <Sun v-if="theme === 'light'" />
      <Moon v-if="theme === 'dark'" />
    </button>
  </div>
</template>
