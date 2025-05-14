<script setup lang="ts">
import type { Brand } from "~/lib/types";
const { updateBrand } = useBrandService();

const modalOpen = ref(false);
const submitLoading = ref(false);

const props = defineProps<{
  brand: Brand;
}>();

const editBrand = ref<Brand>(structuredClone(toRaw(props.brand)));

const { toast } = useToast();

async function handleUpdateBrand() {
  submitLoading.value = true;
  try {
    // Force update by sending temporary name if case-only change is detected
    if (props.brand.name.toLowerCase() === editBrand.value.name.toLowerCase() && 
        props.brand.name !== editBrand.value.name) {
      const tempName = editBrand.value.name + "_temp";
      await updateBrand({ ...editBrand.value, name: tempName }); // Temporary update
    }

    const data = await updateBrand(editBrand.value);
    toast(data.message, "success");
    modalOpen.value = false;
    props.brand.name = editBrand.value.name;
  } catch (error: any) {
    toast(error.data.message, "error");
  }
  submitLoading.value = false;
}
</script>

<template>
  <button class="btn btn-success btn-sm" @click="modalOpen = true">Edit</button>
  <Modal
    max-width="500"
    :modal-open="modalOpen"
    header="Edit Brand"
    :onClickOutside="() => (modalOpen = false)"
  >
    <form @submit.prevent="handleUpdateBrand">
      <input
        v-model="editBrand.name"
        required
        type="text"
        placeholder="Brand name"
        class="input w-full"
      />
      <div class="card-actions justify-end mt-6 gap-2">
        <button
          :disabled="submitLoading"
          @click="modalOpen = false"
          class="btn btn-ghost"
        >
          Cancel
        </button>
        <button
          :disabled="submitLoading"
          class="btn btn-success"
        >
          Confirm Edit
        </button>
      </div>
    </form>
  </Modal>
</template>
