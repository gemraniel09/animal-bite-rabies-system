<script setup lang="ts">

const { getBrands, createBrand, updateBrand, deleteBrand } = useBrandService();
const { data: brands, refresh } = getBrands();
const { toast, errorToast } = useToast();
const { formatDate } = useDateFormatter();

const brandName = ref("");
const loading = ref(false);
const submitLoading = ref(false);
const createModalOpen = ref(false);

// Function to close modal and reset input field
function closeCreateModal() {
  createModalOpen.value = false;
  brandName.value = ""; // Reset input when modal is closed
}

async function handleCreateBrand() {
  submitLoading.value = true;
  try {
    const data = await createBrand({ name: brandName.value });
    await refresh(); // Ensure list updates properly
    closeCreateModal(); // Close modal and reset input
    toast(data.message, "success");
  } catch (e) {
    errorToast(e);
  }
  submitLoading.value = false;
}
</script>

<template>
  <div class="mx-5 mt-5 overflow-x-auto">
    <button class="btn btn-primary mb-3 btn-sm" @click="createModalOpen = true">
      Add Brand
    </button>

    <!-- Create Brand Modal -->
    <Modal
      max-width="500"
      :modal-open="createModalOpen"
      header="Add Brand"
      :onClickOutside="closeCreateModal"
    >
      <form @submit.prevent="handleCreateBrand">
        <input
          v-model="brandName"
          required
          type="text"
          placeholder="Brand name"
          class="input w-full"
        />
        <div class="card-actions justify-end mt-6 gap-2">
          <button
            type="button"
            :disabled="submitLoading"
            @click="closeCreateModal"
            class="btn btn-ghost"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitLoading"
            class="btn btn-success"
          >
            Confirm
          </button>
        </div>
      </form>
    </Modal>

    <div>
      <table class="table table-zebra">
        <thead>
          <tr>
            <th>Brand Name</th>
            <th>Transaction Count</th>
            <th>Date Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(brand, i) in brands">
            <td>{{ brand.name }}</td>
            <td>{{ brand.transaction_count! }}</td>
            <td>{{ formatDate(brand.created_at) }}</td>
            <td class="flex gap-2">
              <EditBrandModal :brand="brand" />
              <DialogButton
                :disabled="loading"
                class="btn-error btn-sm"
                :header="`Delete brand: '${brand.name}'?`"
                buttonText="Delete"
                closeText="Close"
                submitText="Delete"
                maxWidth="500"
                :close-on-click-outside="!submitLoading"
                :onSubmit="async () => {
                    await deleteBrand(brand.id!)
                    refresh()
                }"
              >
                <p class="text-base">
                  Are you sure you want to delete this brand?
                </p>
                <span class="text-xs">
                  Note: If the brand has associated transactions, it cannot be
                  deleted anymore.
                </span>
              </DialogButton>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
