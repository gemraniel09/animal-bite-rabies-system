<script setup lang="ts">
import { Search } from "lucide-vue-next";
const { getPatients, downloadPatients } = usePatientService();
const { data: patients, loading, execute } = getPatients({ immediate: false });
const { formatDate } = useDateFormatter();
const { data: barangays } = useBarangayService().getBarangays();

const filter = ref({
  barangay: "",
  name: "",
  limit: -1,
  transactions: true,
});

const exec = () => {
  execute({
    showLoading: true,
    params: filter.value,
  });
};
watch(filter, () => exec(), { deep: true });

onMounted(() => exec());
</script>

<template>
  <div class="overflow-x-auto">
    <div class="mx-5 mb-5 mt-5">
      <h3 class="font-medium text-base mb-2">Filter Patients:</h3>
      <div class="mb-4 flex items-center justify-between">
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
        <div class="w-full max-w-md mt-6">
          <label class="input w-full">
            <span v-if="loading" class="loading loading-bars loading-lg"></span>
            <Search v-if="!loading" />
            <input
              v-model="filter.name"
              type="search"
              class="grow"
              placeholder="Search"
            />
          </label>
        </div>
      </div>
      <button @click="downloadPatients(filter)" class="btn btn-neutral">
        Download Report
      </button>
      <table class="table table-zebra">
        <thead>
          <tr>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Birth Date</th>
            <th>Gender</th>
            <th>Barangay</th>
            <th>Date Created</th>
            <th>Transactions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="patient in patients" :key="patient.id">
            <td>{{ patient.first_name }}</td>
            <td>{{ patient.middle_name }}</td>
            <td>{{ patient.last_name }}</td>
            <td>{{ patient.birth_date }}</td>          
            <td>{{ patient.gender }}</td>
            <td>{{ patient.barangay?.name ?? "N/A" }}</td>
            <td>{{ formatDate(patient.created_at) }}</td>
            <td>
              <div class="flex gap-2">
                <NuxtLink
                  class="btn btn-success btn-sm btn-soft"
                  :href="`patient/${patient.id}`"
                >
                  {{ patient.transactions_count }}
                  {{
                    patient.transactions_count! > 1
                      ? "Transactions"
                      : "Transaction"
                  }}
                </NuxtLink>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
