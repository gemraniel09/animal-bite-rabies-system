<script setup lang="ts">
import type { Patient, Transaction, TransactionSchedule } from "~/lib/types";
import { VACCINATION_STATUS } from "~/lib/enums";
import { CircleCheck, User, Clipboard, Calendar } from "lucide-vue-next";
import { format } from "date-fns";
const { getAnimals } = useAnimalService();
const { toast, errorToast } = useToast();
const { formatDate } = useDateFormatter();
const { data: animals } = getAnimals();
const { generateSchedule } = usePatientSchedule();
const transactionService = useTransactionService();
const { getBarangays } = useBarangayService();
const { data: barangays } = getBarangays();
const { getBrands } = useBrandService();
const { data: brands } = getBrands();

const tab = ref("new");
const loading = ref(false);
const showForm = ref(true);
const modal = ref<{ open: boolean; transaction_id: number | null }>({
  open: false,
  transaction_id: null,
});

const emptyPatient = {
  first_name: "",
  middle_name: "",
  last_name: "",
  barangay_id: "" as any,
  barangay: "" as any,
  birth_date: "",
  // age: "",
  gender: "",
  full_name: "",
  transactions: [],
};

const emptyTransaction = {
  barangay_id: "" as any,
  barangay: "" as any,
  brand_id: "" as any,
  place: "",
  vaccination_status: VACCINATION_STATUS.REQUIRED,
  animal_id: "" as any,
  animal_status: "",
  body_part: "",
  category: "" as any,
  rig_given: false,
  wash_bite: false,
  booster_given: false,
};

const patient = ref<Patient>(emptyPatient as any);
const transaction = ref<Transaction>(emptyTransaction as any);
const includeDays = ref({ day14: false, day27: false });
const transaction_schedules = ref<TransactionSchedule[]>(
  generateSchedule(new Date(), [0, 3, 7])
);
const isSelectStatus = ref(true);

function onOldPatientSelect(oldPatient: Patient) {
  patient.value = oldPatient;
  showForm.value = true;
}

function resetForm() {
  patient.value = {
    first_name: "",
    middle_name: "",
    last_name: "",
    barangay_id: "" as any,
    barangay: "" as any,
    birth_date: "",
    // age: "",
    gender: "",
    full_name: "",
    transactions: [],
  };
  transaction.value = {
    barangay_id: "" as any,
    brand_id: "" as any,
    barangay: "" as any,
    place: "",
    vaccination_status: VACCINATION_STATUS.REQUIRED,
    animal_id: "" as any,
    animal_status: "",
    body_part: "",
    category: "" as any,
    rig_given: false,
    wash_bite: false,
    booster_given: false,
  };
}

watch(tab, () => {
  patient.value = emptyPatient;
  if (tab.value === "old") {
    showForm.value = false;
  } else {
    showForm.value = true;
  }
});

watch(
  () => transaction.value.category,
  () => {
    transaction.value.booster_given = false
    if (transaction.value.category === 1) {
      transaction.value.vaccination_status =
        VACCINATION_STATUS.OPTIONAL_OPTED_OUT;
    } else {
      transaction.value.vaccination_status = VACCINATION_STATUS.REQUIRED;
    }
  }
);

watch(
  () => transaction.value.booster_given,
  () => {
    if (!!transaction.value.booster_given) {
      transaction.value.vaccination_status = VACCINATION_STATUS.OPTIONAL_OPTED_OUT
    }
  }
);


watch(
  () => transaction.value.animal_status,
  () => {
    if (transaction.value.animal_status === "Other") {
      isSelectStatus.value = false;
      transaction.value.animal_status = "";
    }
  }
);
// bago kong inaadd sa code
// watch(
//   () => transaction.value.place,
//   (newValue) => {
//     if (newValue) {
//       transaction.value.place = newValue
//         .toLowerCase()
//         .replace(/\b\w/g, (char) => char.toUpperCase());
//     }
//   }
// );

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
    transaction_schedules.value = generateSchedule(new Date(), days);
  },
  { deep: true }
);

async function confirmTransaction() {
  loading.value = true;
  try {
    //Existing patient will be determined if the patient has id, no id means new patient.
    patient.value.barangay_id = patient.value.barangay?.id ?? 0;
    transaction.value.barangay_id = transaction.value.barangay?.id ?? 0;

    const data = await transactionService.createTransaction({
      patient: patient.value,
      transaction: transaction.value,
      transaction_schedules:
        transaction.value.vaccination_status ===
        VACCINATION_STATUS.OPTIONAL_OPTED_OUT
          ? undefined
          : transaction_schedules.value,
    });

    toast(data.message, "success");
    tab.value = "new";
    resetForm();
    modal.value.open = true;
    modal.value.transaction_id = data.transaction_id;
  } catch (error: any) {
    errorToast(error);
  }
  loading.value = false;
}
</script>

<template>
  <div class="mt-8 ml-2 mr-2">
    <div class="flex gap-5 ml-6">
      <label class="flex items-center gap-2">
        <input
          type="radio"
          v-model="tab"
          value="new"
          class="radio radio-primary"
        />
        <span>New Patient</span>
      </label>
      <label class="flex items-center gap-2">
        <input
          type="radio"
          v-model="tab"
          value="old"
          class="radio radio-primary"
        />
        <span>Old Patient</span>
      </label>
    </div>
    <!-- Main Content -->
    <div class="card bg-base-100 w-full shadow-sm">
      <div class="card-body">
        <h2 class="card-title">
          <User />
          {{ tab === "new" ? "New Patient" : "Old Patient" }}
        </h2>
        <div class="divider my-1"></div>
        <!-- Patient Search -->
        <div v-if="tab === 'old'">
          <SearchPatient @old-patient-select="onOldPatientSelect" />
        </div>

        <div v-if="showForm">
          <form @submit.prevent="confirmTransaction">
            <!-- Patient Details -->
            <div class="flex flex-wrap gap-4">
              <!-- Left column -->
              <div class="flex-1 min-w-[280px]">
                <!-- First Name -->
                <div class="form-control w-full mb-2">
                  <label class="label">
                    <span class="label-text mt-2 mb-0.5">First Name</span>
                  </label>
                  <input
                    type="text"
                    v-model="patient.first_name"
                    placeholder="Enter first name"
                    class="input input-bordered w-full"
                    required
                  />
                </div>

                <!-- Middle Name -->
                <div class="form-control w-full mb-2">
                  <label class="label">
                    <span class="label-text mt-2 mb-0.5">Middle Name </span>
                  </label>
                  <input
                    required
                    type="text"
                    v-model="patient.middle_name"
                    placeholder="Enter middle name"
                    class="input input-bordered w-full"
                  />
                </div>
                <!-- Last Name -->
                <div class="form-control w-full mb-2">
                  <label class="label">
                    <span class="label-text mt-2 mb-0.5">Last Name</span>
                  </label>
                  <input
                    type="text"
                    v-model="patient.last_name"
                    placeholder="Enter last name"
                    class="input input-bordered w-full"
                    required
                  />
                </div>
              </div>
              <!-- Right column -->
              <div class="flex-1 min-w-[280px]">
                <!-- Patient Barangay -->
                <div class="form-control w-full mb-2">
                  <label class="label">
                    <span class="label-text mt-2 mb-0.5">Barangay</span>
                  </label>
                  <select
                    v-model="patient.barangay"
                    required
                    class="select w-full"
                  >
                    <option selected disabled value="">
                      Choose a Barangay
                    </option>
                    <option v-for="barangay in barangays" :value="barangay">
                      {{ barangay.name }}
                    </option>
                  </select>
                </div>
                <!-- Birth Date -->
                <div class="form-control w-full mb-2">
                  <label class="label">
                    <span class="label-text mt-2 mb-0.5">Birth Date</span>
                  </label>
                  <input
                    type="date"
                    v-model="patient.birth_date"
                    placeholder="Enter date"
                    class="input input-bordered w-full"
                    required
                  />
                </div>              
                <!-- Gender -->
                <div class="form-control w-full mb-2`">
                  <label class="label">
                    <span class="label-text mt-2 mb-0.5">Gender</span>
                  </label>
                  <select
                    required
                    v-model="patient.gender"
                    class="select w-full"
                  >
                    <option selected disabled value="">Choose a gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Transaction Details -->
            <h2 class="card-title mt-8">
              <Clipboard />
              Transaction
            </h2>
            <div class="divider my-1"></div>
            <div class="flex flex-wrap gap-4 w-full">
              <!-- Left column -->
              <div class="flex-1 min-w-[280px]">
                <!-- Category and Animal -->
                <div class="flex gap-4 mb-2 w-full">
                  <!-- Category -->
                  <div class="flex w-full gap-2">
                    <div class="form-control w-full">
                      <label class="label">
                        <span class="label-text mt-2">Category</span>
                      </label>
                      <select
                        required
                        v-model="transaction.category"
                        class="select w-full"
                      >
                        <option selected disabled value="">
                          Choose category
                        </option>
                        <option :value="1">I</option>
                        <option :value="2">II</option>
                        <option :value="3">III</option>
                      </select>
                    </div>
                    <!-- Brands -->
                    <div class="form-control w-full">
                      <label class="label">
                        <span class="label-text mt-2">Brand</span>
                      </label>
                      <select
                        required
                        v-model="transaction.brand_id"
                        class="select w-full"
                      >
                        <option selected disabled value="">Choose brand</option>
                        <option v-for="brand in brands" :value="brand.id">
                          {{ brand.name }}
                        </option>
                      </select>
                    </div>
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
                  <div class="form-control w-full">
  <label class="label">
    <span class="label-text mt-2 mb-0.5">Site (Body Part) / Type of (B/NB)</span>
  </label>
  <input
    type="text"
    v-model="transaction.body_part"
    placeholder="Enter body part"
    class="input input-bordered w-full"
    required
    @input="transaction.body_part = transaction.body_part.toUpperCase()"
  />
</div>

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
                      <option disabled selected value="">
                        Choose animal status
                      </option>
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
                      <span class="label-text mt-2 mb-0.5">Place(Where bitten accured)</span>
                    </label>
                    <input
                      type="text"
                      v-model="transaction.place"
                      placeholder="Enter place"
                      class="input input-bordered w-full"
                      required
                      @input="transaction.place = transaction.place.toUpperCase()"
                    />
                  </div>
                  <!-- Barangay  -->
                  <div class="form-control w-full mb-2">
                    <label class="label">
                      <span class="label-text mt-2 mb-0.5">Barangay/Routes</span>
                    </label>
                    <select
                      v-model="transaction.barangay"
                      required
                      class="select w-full"
                    >
                      <option selected disabled value="">
                        Choose barangay
                      </option>
                      <option v-for="barangay in barangays" :value="barangay">
                        {{ barangay.name }}
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Checkboxes -->
                <div class="flex items-center gap-6 mt-5">
                  <label class="flex items-center gap-2">
                    <input
                      v-model="transaction.wash_bite"
                      type="checkbox"
                      class="checkbox checkbox-accent"
                    />
                    <span>Washed the bitten area</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      v-model="transaction.rig_given"
                      type="checkbox"
                      class="checkbox checkbox-accent"
                    />
                    <span>RIG given</span>
                  </label>
                  <label class="flex items-center gap-2" 
                  v-if="transaction.category === 1">
                    <input
                      v-model="transaction.booster_given"
                      type="checkbox"
                      class="checkbox checkbox-accent"
                      
                    />
                    <span>Booster given</span>
                  </label>
                  <label
                    v-if="transaction.category === 1 && !transaction.booster_given"
                    class="flex items-center gap-2"
                  >
                    <!-- true/false-value, Life savers -->
                    <input
                      type="checkbox"
                      class="checkbox checkbox-accent"
                      v-model="transaction.vaccination_status"
                      :true-value="VACCINATION_STATUS.OPTIONAL_OPTED_OUT"
                      :false-value="VACCINATION_STATUS.OPTIONAL_OPTED_IN"
                    />
                    <span>Patient chooses not to receive vaccination</span>
                  </label>
                </div>
              </div>
            </div>

            
            <!-- Right column -->
            <!-- Schedule Timeline Section -->
            <div
              v-if="
                transaction.vaccination_status ===
                  VACCINATION_STATUS.OPTIONAL_OPTED_IN ||
                transaction.category > 1
              "
              class="mt-8"
            >
              <div class="flex items-center mb-4 gap-2">
                <Calendar :size="28" />
                <h2 class="text-2xl font-bold">Schedule</h2>
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
              <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                  <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                      <thead>
                        <tr>
                          <th>Day</th>
                          <th>Schedule Date</th>
                          <th>Remarks</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(schedule, i) in transaction_schedules"
                          :key="schedule.id"
                        >
                          <td>
                            <div class="font-bold">{{ schedule.name }}</div>
                          </td>
                          <td>
                            {{
                              format(schedule.schedule, "EEEE, MMMM dd, yyyy")
                            }}
                          </td>
                          <td>
                            {{ schedule.remarks }}
                          </td>
                          <td>
                            <RemarksModal
                              @remarks-save="
                                (remarks) => (schedule.remarks = remarks)
                              "
                              :current-remarks="schedule?.remarks || ''"
                              v-if="i === 0"
                            />
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-control mt-6 flex justify-end">
              <button
                type="submit"
                class="btn btn-primary mb-24"
                :disabled="loading"
              >
                Confirm Transaction
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <Modal max-width="500" :modal-open="modal.open" header="Transaction ID">
    <div class="flex flex-col items-center justify-center py-4">
      <p class="text-sm text-gray-500 mb-2">
        Transaction has been created successfully
      </p>
      <div class="bg-base-200 w-full rounded-xl p-6 text-center">
        <h1 class="text-4xl font-bold tracking-wider text-primary">
          #{{ modal?.transaction_id ?? "-" }}
        </h1>
        <p class="text-xs mt-2 opacity-70">
          Use this number for patient's next transaction reference
        </p>
      </div>
      <div class="flex items-center mt-4">
        <CircleCheck class="text-success mr-1" />
        <span class="text-sm"
          >Patient details and schedule have been saved</span
        >
      </div>
    </div>

    <div class="card-actions justify-end mt-6 gap-2">
      <button
        @click="
          {
            modal.open = false;
            modal.transaction_id = null;
          }
        "
        class="btn btn-success"
      >
        Confirm
      </button>
    </div>
  </Modal>
</template>
