<script setup lang="ts">
import type { Transaction, TransactionSchedule } from "~/lib/types";
import { VACCINATION_STATUS } from "~/lib/enums";

const { generateSchedule } = usePatientSchedule();
const { getAnimals } = useAnimalService();
const { getBarangays } = useBarangayService();
const { toast, errorToast } = useToast();
const { updateTransaction } = useTransactionService();
const { data: barangays } = getBarangays();
const { data: animals } = getAnimals();
const isSelectStatus = ref(true);

const loading = ref(false);
const modalOpen = ref(false);
const props = defineProps<{
  transaction: Transaction;
  newTransaction: (t: Transaction) => void;
}>();
props.transaction.wash_bite = Boolean(props.transaction.wash_bite);
props.transaction.rig_given = Boolean(props.transaction.rig_given);
const transaction = ref<Transaction>(structuredClone(toRaw(props.transaction)));

const transaction_schedules = ref<TransactionSchedule[]>();
const includeDays = ref({
  day14: (transaction.value.schedules?.length || 0) > 3,
  day27: (transaction.value.schedules?.length || 0) > 4,
});

const onCancel = () => {
  props.transaction.wash_bite = Boolean(props.transaction.wash_bite);
  props.transaction.rig_given = Boolean(props.transaction.rig_given);
  transaction.value = structuredClone(toRaw(props.transaction));
  includeDays.value = structuredClone(
    toRaw({
      day14: (props.transaction.schedules?.length || 0) > 3,
      day27: (props.transaction.schedules?.length || 0) > 4,
    })
  );
  modalOpen.value = false;
};
onMounted(() => {
  transaction_schedules.value = generateSchedule(
    new Date(props.transaction.schedules?.[0]?.schedule || ""),
    []
  );
  if (
    !["Alive", "Lost", "Dead", "Re-Exposed"].includes(
      transaction.value.animal_status
    )
  ) {
    isSelectStatus.value = false;
  }
});
watch(
  () => transaction.value.animal_status,
  () => {
    if (transaction.value.animal_status === "Other") {
      isSelectStatus.value = false;
      transaction.value.animal_status = "";
    }
  }
);
watch(
  includeDays,
  () => {
    const days = [0, 3, 7];
    if (includeDays.value.day14) {
      days.push(14);
      if (includeDays.value.day27) {
        days.push(27);
      }
    }
    transaction.value.schedules = generateSchedule(
      new Date(props.transaction.schedules?.[0]?.schedule || ""),
      days
    );
  },
  { deep: true }
);

const confirmChanges = async () => {
  delete transaction.value.patient;
  delete transaction.value.updated_at;
  delete transaction.value.created_at;
  loading.value = true;
  try {
    const data = await updateTransaction(transaction.value);
    modalOpen.value = false;
    console.log(data.transaction);
    props.newTransaction(data.transaction);
    toast(data.message, "success");
  } catch (e) {
    errorToast(e);
  }
  loading.value = false;
};
</script>

<template>
  <button @click="modalOpen = true" class="btn btn-success btn-sm flex">
    Update
  </button>
  <Modal
    max-width="700"
    :modal-open="modalOpen"
    header="Update Transaction Details"
    @click-outside="onCancel()"
  >
    <!-- Transaction Details -->
    <form @submit.prevent="confirmChanges">
      <div class="flex flex-wrap gap-4 w-full">
        <!-- Left column -->
        <div class="flex-1 min-w-[280px]">
          <!-- Category and Animal -->
          <div class="flex gap-4 mb-2">
            <!-- Category -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2">Category</span>
              </label>
              <select
                required
                v-model="transaction.category"
                class="select w-full"
              >
                <option selected disabled value="">Choose category</option>
                <option :value="1">I</option>
                <option :value="2">II</option>
                <option :value="3">III</option>
              </select>
            </div>
            <!-- Animals -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Animal</span>
              </label>
              <select
                required
                v-model="transaction.animal_id"
                class="select w-full"
              >
                <option selected disabled value="">Choose animal</option>
                <option
                  v-for="animal in animals"
                  :key="animal.id"
                  :value="animal.id"
                >
                  {{ animal.name }}
                </option>
              </select>
            </div>
          </div>
          <!-- Body Part and Place -->
          <div class="flex gap-4 mb-2">
            <!-- Body Part -->
            <input
  type="text"
  v-model="transaction.body_part"
  placeholder="Enter body part"
  class="input input-bordered w-full"
  required
  @input="transaction.body_part = transaction.body_part.toUpperCase()"
/>

            <!-- Place -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Animal Status</span>
              </label>
              <select
                v-if="isSelectStatus"
                v-model="transaction.animal_status"
                class="select w-full"
              >
                <option disabled selected value="">Choose animal status</option>
                <option value="Alive">Alive</option>
                <option value="Lost">Lost</option>
                <option value="Dead">Dead</option>
                <option value="Re-Exposed">Re-Exposed</option>
                <option value="Other">Other</option>
              </select>
              <div v-else class="flex">
                <input
                  type="text"
                  v-model="transaction.animal_status"
                  placeholder="Enter animal status"
                  class="input input-bordered w-full"
                  required
                />
                <button
                  type="button"
                  @click="
                    {
                      isSelectStatus = true;
                      transaction.animal_status = '';
                    }
                  "
                  class="btn btn-neutral join-item"
                >
                  Re-Select
                </button>
              </div>
            </div>
          </div>
          <!-- Barangay and Place -->
          <div class="flex gap-4 mb-2">
            <!-- Place -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Place</span>
              </label>
              <input
                type="text"
                v-model="transaction.place"
                placeholder="Enter place"
                class="input input-bordered w-full"
                required
              />
            </div>
            <!-- Barangay  -->
            <div class="form-control w-full mb-2">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Barangay</span>
              </label>
              <select
                v-model="transaction.barangay_id"
                required
                class="select w-full"
              >
                <option selected disabled value="">Choose barangay</option>
                <option v-for="barangay in barangays" :value="barangay.id">
                  {{ barangay.name }}
                </option>
              </select>
            </div>
          </div>
          <!-- Checkboxes -->
          <div class="flex items-center gap-6 mt-5">
            <label class="flex items-center gap-2">
              <input
                :disabled="props.transaction.wash_bite"
                v-model="transaction.wash_bite"
                type="checkbox"
                class="checkbox checkbox-accent"
              />
              <span>Washed the bitten area</span>
            </label>
            <label class="flex items-center gap-2">
              <input
                :disabled="props.transaction.rig_given"
                v-model="transaction.rig_given"
                type="checkbox"
                class="checkbox checkbox-accent"
              />
              <span>RIG given</span>
            </label>
          </div>
          <!-- Included days checkbox -->
          <div class="flex items-center gap-6 mt-5 mb-2">
            <label class="flex items-center gap-2">
              <input
                :disabled="includeDays.day27"
                v-model="includeDays.day14"
                type="checkbox"
                class="checkbox checkbox-accent"
              />
              <span>Include Day 14</span>
            </label>

            <label class="flex items-center gap-2">
              <input
                :disabled="!includeDays.day14"
                v-model="includeDays.day27"
                type="checkbox"
                class="checkbox checkbox-accent"
              />
              <span>Include Day 27</span>
            </label>
          </div>
        </div>
      </div>
      <div class="card-actions justify-end mt-6 gap-2">
        <button @click="onCancel" type="button" class="btn btn-ghost">
          Cancel
        </button>
        <button class="btn btn-success">Confirm Changes</button>
      </div>
    </form>
  </Modal>
</template>
