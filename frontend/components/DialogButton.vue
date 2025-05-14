<script setup lang="ts">
import { onClickOutside } from "@vueuse/core";
import { useTemplateRef } from "vue";

const target = useTemplateRef<HTMLElement>("target");
const modalOpen = ref(false);
const loading = ref(false);
const { toast } = useToast();

const props = defineProps<{
  header: string;
  buttonText: string;
  closeText?: string;
  submitText?: string;
  class?: string;
  disabled?: boolean;
  maxWidth?: string;
  onSubmit: () => Promise<void>;
  closeOnClickOutside?: boolean;
}>();

onClickOutside(target, (event) => {
  if (props.closeOnClickOutside) {
    console.log(event)
    modalOpen.value = false;
  }
});

async function handleSubmit() {
  loading.value = true;
  try {
    await props.onSubmit();
    modalOpen.value = false;
  } catch (error: any) {
    toast(error.data.message, "error");
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <button
    :disabled="props.disabled"
    :class="props.class"
    class="btn"
    @click="modalOpen = true"
  >
    {{ buttonText }}
  </button>
  <Teleport to="body">
    <div
      v-if="modalOpen"
      class="modal w-full"
      :class="{ 'modal-open': modalOpen }"
    >
      <div
        ref="target"
        class="modal-box p-0 h-auto m-0"
        :style="{ maxWidth: props.maxWidth ? `${props.maxWidth}px` : 'none' }"
      >
        <div class="card">
          <div class="card-body">
            <h2 class="card-title text-xl mb-2">{{ header }}</h2>
            <slot />
            <div class="card-actions justify-end mt-4 gap-2">
              <button
                class="btn btn-ghost"
                @click="modalOpen = false"
                :disabled="loading"
              >
                {{ props.closeText ?? "Close" }}
              </button>
              <button
                class="btn btn-soft btn-primary"
                @click="handleSubmit"
                :disabled="loading"
              >
                {{ props.submitText ?? "Submit" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
