<script setup lang="ts">
const paramsId = Number(useRoute().params.id);
const { formatDate } = useDateFormatter();
const { getPatient } = usePatientService();
const { data: patient, loading } = getPatient(paramsId);
const { washBite } = useFormatter();

import { ClipboardPlus, Info, User } from "lucide-vue-next";
</script>
<template>
  <div class="pt-10">
    <div class="container mx-auto">
      <button
        class="btn btn-neutral join-item"
        @click="useRouter().push('/admin/report/patients')"
      >
        Back
      </button>
      <div class="card bg-base-100 shadow-xl w-full mt-10">
        <div class="card-body">
          <h2 class="card-title flex items-center gap-2">
            <User />
            Patient Information
          </h2>
          <div class="divider my-1"></div>
          <div class="grid grid-cols-2 gap-2">
            <div class="text-sm opacity-70">Name:</div>
            <div class="font-medium">
              {{ patient?.full_name }}
            </div>

            <div class="text-sm opacity-70">Gender:</div>
            <div class="font-medium capitalize">
              {{ patient?.gender }}
            </div>

            <div class="text-sm opacity-70">Birth Date:</div>
            <div class="font-medium">
              {{ patient?.birth_date }}
            </div>
            <!-- <div class="text-sm opacity-70">Age:</div>
            <div class="font-medium">
              {{ patient?.age }}
            </div> -->
            <div class="text-sm opacity-70">Barangay:</div>
            <div class="font-medium">
              {{ patient?.barangay?.name }}
            </div>
            <div class="text-sm opacity-70">Created Date:</div>
            <div class="font-medium">
              {{ formatDate(patient?.created_at!) }}
            </div>
          </div>
        </div>
      </div>

      <div class="mt-10 flex gap-2">
        <ClipboardPlus :size="28" />
        <h2 class="text-2xl font-bold">Transactions</h2>
      </div>

      <div class="card bg-base-100 shadow-xl mt-10">
        <div class="card-body">
          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr>
                  <th>Transaction ID</th>               
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
                    v-if="patient.transactions.length > 0"
                    v-for="transaction in patient.transactions"
                    :key="transaction.id"
                  >
                    <td>{{ transaction.id }}</td>
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
                            transaction.schedules &&
                            transaction.schedules.length > 0
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
        </div>
      </div>
    </div>
  </div>
</template>
