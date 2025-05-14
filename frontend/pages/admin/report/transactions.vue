<script setup lang="ts">
import { Info, Search } from "lucide-vue-next";

const { formatDate } = useDateFormatter();
const { washBite, formatName } = useFormatter();
const { getTransactions, downloadTransactions } = useTransactionService();
const { data: barangays } = useBarangayService().getBarangays();
const { data: animals } = useAnimalService().getAnimals();
const { data: brands } = useBrandService().getBrands();

const {
  data: transactions,
  loading,
  execute,
} = getTransactions({ immediate: false });

const today = new Date().toISOString().split("T")[0];

const filter = ref({
  search: "",
  dateFrom: "",
  dateTo: "",
  animal: "",
  barangay: "",
  brand: "",
});

watch(filter, () => exec(), { deep: true });

const exec = () => {
  execute({
    showLoading: true,
    params: filter.value,
  });
};

onMounted(() => {
  filter.value.dateTo = today;
  filter.value.dateFrom = today;
});
</script>
<template>
  <div class="mx-5 mt-10 overflow-x-auto">
    <div>
      <h3 class="font-medium text-base mb-2">Filter Transactions:</h3>
      <div class="flex flex-col gap-4 px-1">
        <!-- Filters row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <!-- Animal Filter -->
          <div class="form-control">
            <label class="label">
              <span class="label-text">Animal</span>
            </label>
            <select
              v-model="filter.animal"
              class="select select-bordered w-full"
            >
              <option value="">All Animals</option>
              <option
                v-for="animal in animals"
                :value="animal.id"
                :key="animal.id"
              >
                {{ animal.name }}
              </option>
            </select>
          </div>
          <!-- Barangay Filter -->
          <div class="form-control">
            <label class="label">
              <span class="label-text">Barangay</span>
            </label>
            <select
              v-model="filter.barangay"
              class="select select-bordered w-full"
            >
              <option value="">All Barangays</option>
              <option
                v-for="barangay in barangays"
                :value="barangay.id"
                :key="barangay.id"
              >
                {{ barangay.name }}
              </option>
            </select>
          </div>
          <!-- Brand Filter -->
          <div class="form-control">
            <label class="label">
              <span class="label-text">Brand</span>
            </label>
            <select
              v-model="filter.brand"
              class="select select-bordered w-full"
            >
              <option value="">All Brands</option>
              <option v-for="brand in brands" :value="brand.id" :key="brand.id">
                {{ brand.name }}
              </option>
            </select>
          </div>
        </div>
        <!-- Date and Search row -->
        <div
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 w-full mb-4"
        >
          <div class="form-control">
            <label class="label">
              <span class="label-text">Date From</span>
            </label>
            <input
              type="date"
              v-model="filter.dateFrom"
              class="input input-bordered w-full"
            />
          </div>
          <div class="form-control">
            <label class="label">
              <span class="label-text">Date To</span>
            </label>
            <input
              type="date"
              v-model="filter.dateTo"
              class="input input-bordered w-full"
            />
          </div>
          <div class="form-control">
            <label class="label">
              <span class="label-text">Search</span>
            </label>
            <label class="input input-bordered flex items-center gap-2 w-full">
              <span
                v-if="loading"
                class="loading loading-spinner loading-sm"
              ></span>
              <Search v-else :size="18" />
              <input
                v-model="filter.search"
                type="search"
                class="grow"
                placeholder="Search by ID, patient name, etc."
              />
            </label>
          </div>
        </div>
      </div>
    </div>
    <button @click="downloadTransactions(filter)" class="btn btn-neutral">
      Download Report
    </button>
    <table class="table table-zebra over">
      <thead>
        <tr>
          <th>Transaction ID</th>
          <th>Patient</th>
          <th>Age</th>
          <th>Place</th>
          <th>Barangay</th>
          <th>Animal</th>
          <th>Animal Status</th>
          <th>Body Part</th>
          <th>Brand</th>
          <th>Category</th>
          <th>Wash Bite</th>
          <th>RIG Given</th>
          <th>Booster Given</th>
          <th>Date Created</th>
          <th>Schedule</th>
         
        </tr>
      </thead>
      <tbody>
        <tr v-if="loading">
          <td colspan="8 " class="text-center">
            <p class="loading text-center"></p>
          </td>
        </tr>
        <template v-else>
          <tr
            v-if="transactions.length > 0"
            v-for="transaction in transactions"
            :key="transaction.id"
          >
            <td>{{ transaction.id }}</td>
            <td>{{ transaction.patient?.full_name }}</td>
            <td>{{ transaction.age }}</td>
            <td>{{ transaction.place }}</td>
            <td>{{ transaction.barangay?.name ?? "N/A" }}</td>
            <td>{{ transaction.animal?.name }}</td>
            <td>{{ transaction.animal_status }}</td>
            <td>{{ transaction.body_part }}</td>
            <td>{{ transaction.brand?.name! }}</td>
            <td>
              <CategoryBadge :category="transaction.category" />
            </td>
            <td>{{ transaction.wash_bite ? "Yes" : "No" }}</td>
            <td>{{ transaction.rig_given ? "Yes" : "No" }}</td>
            <td>{{ transaction.booster_given ? "Yes" : "No" }}</td>
            <td>{{ formatDate(transaction.created_at) }}</td>
            <td>
              <div class="flex gap-2 w-full">
                <ScheduleModal
                  v-if="
                    transaction.schedules && transaction.schedules.length > 0
                  "
                  :patient="transaction.patient!"
                  :schedules="transaction.schedules"
                />
                <div
                  v-else
                  class="tooltip tooltip-left text-sm flex items-center justify-center gap-1 w-full h-full"
                  data-tip="Patient chose to not be vaccinated"
                >
                  None
                  <Info :size="18" class="" />
                </div>
              </div>
            </td>
          </tr>
          <tr v-else>
            <td colspan="9">No Transactions</td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>
