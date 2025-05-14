<script setup lang="ts">
const { toast } = useToast();
const createModalOpen = ref(false);
const animalName = ref("");
const submitLoading = ref(false);

const animalService = useAnimalService();
const { formatDate } = useDateFormatter();

const { data: animals, loading, refresh } = animalService.getAnimals();

function closeModal() {
  createModalOpen.value = false;
  animalName.value = ""; // Reset input when modal is closed
}

async function createAnimal() {
  submitLoading.value = true;
  try {
    const data = await animalService.createAnimal(animalName.value);
    await refresh(); // Ensure list updates properly
    closeModal(); // Close modal and reset input
    toast(data.message, "success");
  } catch (error: any) {
    toast(error.data.message, "error");
  }
  submitLoading.value = false;
}

async function deleteAnimal(id: number) {
  const data = await animalService.deleteAnimal(id);
  toast(data.message, "success");
  await refresh();
}
</script>

<template>
  <div class="mx-5 mt-5 overflow-x-auto">
    <button class="btn btn-primary mb-3 btn-sm" @click="createModalOpen = true">
      Add Animal
    </button>

    <!-- Create Animal Modal -->
    <Modal
      max-width="500"
      :modal-open="createModalOpen"
      header="Add Animal"
      :onClickOutside="closeModal"
    >
      <form @submit.prevent="createAnimal">
        <input
          v-model="animalName"
          required
          type="text"
          placeholder="Animal name"
          class="input w-full"
        />
        <div class="card-actions justify-end mt-6 gap-2">
          <button
            :disabled="submitLoading"
            @click="closeModal"
            class="btn btn-ghost"
          >
            Cancel
          </button>
          <button :disabled="submitLoading || !animalName" class="btn btn-success">
            Confirm
          </button>
        </div>
      </form>
    </Modal>

    <div>
      <table class="table table-zebra">
        <thead>
          <tr>
            <th>Animal Name</th>
            <th>Patient Bitten</th>
            <th>Date Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="animal in animals" :key="animal.id">
            <td>{{ animal.name }}</td>
            <td>{{ animal?.transaction_count ?? 0 }}</td>
            <td>{{ formatDate(animal.created_at) }}</td>
            <td class="flex gap-2">
              <EditAnimalModal :animal="animal" />
              <DialogButton
                :disabled="loading"
                class="btn-error btn-sm"
                :header="`Delete animal: '${animal.name}'?`"
                buttonText="Delete"
                closeText="Close"
                submitText="Delete"
                maxWidth="500"
                :onSubmit="async () => await deleteAnimal(animal.id!)"
              >
                <p class="text-base">
                  Are you sure you want to delete this animal?
                </p>
                <span class="text-xs">
                  Note: If the animal has associated transactions, it cannot be deleted.
                </span>
              </DialogButton>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
