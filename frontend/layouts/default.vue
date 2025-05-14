<script setup lang="ts">
import SideBar from "~/components/navigation/SideBar.vue";
import { Menu } from "lucide-vue-next";

const authStore = useAuthStore();
const drawerOpen = ref(false);

// load initial data
onMounted(() => {
  authStore.getCurrentUser();
});
watch(
  () => authStore.isLoggedIn,
  (isLoggedIn) => {
    if (!isLoggedIn) {
      drawerOpen.value = false;
    } else {
      drawerOpen.value = true;
    }
  },
  { immediate: true }
);
</script>

<template>
  <!-- Top Bar -->
  <Toast />
  <div
    class="card card-border bg-base-200 w-screen flex-1 fixed top-0 left-0 z-40"
  >
    <div class="card-body flex flex-row justify-between p-4 flex-wrap">
      <div class="flex items-center gap-5">
        <button
          @click="drawerOpen = !drawerOpen"
          v-if="authStore.isLoggedIn"
          class="btn btn-circle btn-sm"
        >
          <Menu class="primary" />
        </button>
        <NuxtLink :href="authStore.isLoggedIn ? '/admin/dashboard' : '/login'">
          <h2 class="card-title">Animal Bite Management System</h2>
        </NuxtLink>
      </div>
      <div class="flex">
        <ThemeOption />
        <UserAvatar />
      </div>
    </div>
  </div>
  <SideBar :drawer-open="drawerOpen" :close="() => (drawerOpen = false)">
    <slot />
  </SideBar>
</template>
