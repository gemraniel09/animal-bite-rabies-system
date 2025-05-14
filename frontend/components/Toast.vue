<script setup lang="ts">
import { useToast } from "@/composables/useToast";

const { toasts } = useToast();
</script>
<template>
  <TransitionGroup
    name="toast"
    tag="div"
    class="toast toast-top toast-end z-100"
  >
    <div
      v-for="toast in toasts"
      :key="toast.id"
      :class="[
        'alert',
        toast.type === 'success'
          ? 'alert-success'
          : toast.type === 'error'
          ? 'alert-error'
          : toast.type === 'warning'
          ? 'alert-warning'
          : 'alert-info',
      ]"
    >
      <span class="text-base"
        ><strong>{{ toast.message }}</strong></span
      >
    </div>
  </TransitionGroup>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
