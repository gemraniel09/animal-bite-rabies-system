<script setup lang="ts">
import { onClickOutside } from "@vueuse/core";
import { useTemplateRef } from "vue";

const target = useTemplateRef<HTMLElement>("target");

const props = defineProps<{
  modalOpen: boolean;
  header: string;
  class?: string;
  disabled?: boolean;
  maxWidth?: string;
  close?: () => void;
  onClickOutside?: () => void;
}>();
onClickOutside(target, (event) => {
  if (props.onClickOutside) {
    props.onClickOutside();
  }
});
</script>

<template>
  <Teleport to="body">
    <div
      v-if="modalOpen"
      class="modal w-full"
      :class="{ 'modal-open': modalOpen }"
    >
      <div
        ref="target"
        class="modal-box p-0 h-auto m-0 mb-24"
        :style="{ maxWidth: props.maxWidth ? `${props.maxWidth}px` : 'none' }"
      >
        <div class="card">
          <div class="card-body">
            <h2 class="card-title text-xl mb-2">{{ header }}</h2>
            <slot />
            <!-- <div class="card-actions justify-end mt-4 gap-2"></div> -->
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
