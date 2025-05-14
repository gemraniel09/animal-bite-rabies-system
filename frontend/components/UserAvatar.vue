<script setup lang="ts">
const authStore = useAuthStore();
</script>
<template>
  <div class="flex items-center justify-center" v-if="authStore.loading">
    <div class="loading" />
  </div>
  <div v-else class="flex items-center justify-center">
    <div v-if="authStore.isLoggedIn" class="dropdown dropdown-end">
      <div
        tabindex="0"
        role="button"
        class="btn btn-ghost btn-sm avatar avatar-placeholder flex items-center gap-2"
      >
        <p class="text-sm">
          {{ authStore.fullName }} ({{ authStore.roleName }})
        </p>
        <div class="bg-accent text-accent-content w-9 rounded-full">
          <span class="text-base">{{ authStore.placeHolder }}</span>
        </div>
      </div>
      <ul
        tabindex="0"
        class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm"
      >
        <NuxtLink href="/profile" class="btn btn-wide justify-start btn-ghost"
          >Profile</NuxtLink
        >
        <button
          @click="authStore.logout"
          class="btn btn-wide justify-start btn-ghost text-error"
        >
          Logout
        </button>
      </ul>
    </div>

    <div v-else class="flex items-center justify-center">
      <NuxtLink href="/login" class="btn btn-sm btn-secondary">Login</NuxtLink>
    </div>
  </div>
</template>
