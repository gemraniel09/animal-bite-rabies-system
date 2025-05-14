<script setup lang="ts">
import type { Animal } from "~/lib/types";
const { updateAnimal } = useAnimalService();

const modalOpen = ref(false);
const submitLoading = ref(false);

const props = defineProps<{
  animal: Animal;
}>();

const editAnimal = ref<Animal>(JSON.parse(JSON.stringify(props.animal)));

const { toast } = useToast();

async function handleUpdateAnimal() {
  submitLoading.value = true;

  try {
    //  Fix: Check if only case changed
    if (props.animal.name.toLowerCase() === editAnimal.value.name.toLowerCase()) {
      props.animal.name = editAnimal.value.name; // Update UI
      modalOpen.value = false;
      toast("Updated successfully!", "success");
      submitLoading.value = false;
      return;
    }

    // Only update backend if name change is not just case
    const data = await updateAnimal(editAnimal.value);
    toast(data.message, "success");
    modalOpen.value = false;

    // Optimistic update
    props.animal.name = editAnimal.value.name;
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
    header="Edit Animal"
    :onClickOutside="() => (modalOpen = submitLoading)"
  >
    <form @submit.prevent="handleUpdateAnimal">
      <input
        v-model="editAnimal.name"
        required
        type="text"
        placeholder="Animal name"
        class="input w-full"
      />
      <div class="card-actions justify-end mt-6 gap-2">
        <button
          :disabled="submitLoading"
          @click="modalOpen = submitLoading"
          class="btn btn-ghost"
        >
          Cancel
        </button>
        <button
          :disabled="submitLoading || props.animal.name === editAnimal.name"
          class="btn btn-success"
        >
          Confirm Edit
        </button>
      </div>
    </form>
  </Modal>
</template>
