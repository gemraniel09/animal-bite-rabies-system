<script setup lang="ts">
import type { Transaction, TransactionSchedule } from "~/lib/types";
import { User, Clipboard, Calendar, Search, Pencil } from "lucide-vue-next";

const { formatDate } = useDateFormatter();
const { formatName, formatRole } = useFormatter();
const { isVisited, isUpcoming } = usePatientSchedule();
const { getTransaction } = useTransactionService();
const { updateSchedule } = useTransactionScheduleService();
const { toast, errorToast } = useToast();

const transaction = ref<Transaction>();
const transactionId = ref<number>();
const remarks = ref("");

const completedSchedules = computed(() => {
  if (!transaction.value) return 0;
  return transaction.value.schedules?.filter(
    (schedule) => schedule.visited_date !== null
  ).length;
});

const onSearch = async (transaction_id: number) => {
  if (isNaN(transaction_id) || transaction_id <= 0) {
    return;
  }
  try {
    const data = await getTransaction(transaction_id);
    transaction.value = data;
  } catch (e) {
    errorToast(e);
  }
};
const onConfirmVisit = async (schedule_id: number) => {
  console.log(schedule_id);
  try {
    const data = await updateSchedule(schedule_id, {
      remarks: remarks.value,
      visited_date: new Date().toISOString() as string,
    });
    toast(data.message, "success");
    onSearch(transactionId.value!);
  } catch (e) {
    errorToast(e);
  }
};
</script>

<template>
  <form
    @submit.prevent="onSearch(transactionId!)"
    v-if="!transaction"
    class="h-[70vh] w-full flex justify-center items-center"
  >
    <div class="join">
      <div>
        <label class="input validator join-item">
          <Search />
          <input
            type="number"
            placeholder="Enter transaction ID"
            required
            v-model="transactionId"
          />
        </label>
        <div class="validator-hint hidden">Enter a valid transaction ID</div>
      </div>
      <button type="submit" class="btn btn-neutral join-item">Search</button>
    </div>
  </form>
  <div v-if="transaction" class="min-h-screen p-4">
    <div class="container mx-auto">
      <!-- Page Header -->
      <button class="btn" @click="transaction = undefined">
        Searh Patient
      </button>
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold">Follow-up Transaction</h1>
        <p class="text-sm opacity-70">Transaction #{{ transaction.id }}</p>
      </div>

      <!-- Main Content -->
      <div class="flex gap-6 w-full">
        <!-- Patient Information Card -->
        <div class="card bg-base-100 shadow-xl w-full">
          <div class="card-body">
            <h2 class="card-title flex items-center gap-2">
              <User />
              Patient Information
            </h2>
            <div class="divider my-1"></div>
            <div class="grid grid-cols-2 gap-2">
              <div class="text-sm opacity-70">Name:</div>
              <div class="font-medium">
                {{ formatName(transaction.patient!) }}
              </div>

              <div class="text-sm opacity-70">Gender:</div>
              <div class="font-medium capitalize">
                {{ transaction.patient?.gender }}
              </div>

              <div class="text-sm opacity-70">Birth Date:</div>
              <div class="font-medium">
                {{transaction.patient?.birth_date }}
              </div>
              <!-- <div class="text-sm opacity-70">Barangay:</div>
              <div class="font-medium">
                {{ transaction.patient?.age?.name }}
              </div> -->

              <div class="text-sm opacity-70">Barangay:</div>
              <div class="font-medium">
                {{ transaction.patient?.barangay?.name }}
              </div>
            </div>
          </div>
        </div>

        <!-- Transaction Details Card -->
        <div class="card bg-base-100 shadow-xl w-full">
          <div class="card-body">
            <h2 class="card-title flex items-center justify-between">
              <div class="flex gap-2"><Clipboard />Transaction Details</div>
              <EditTransactionModal
                :newTransaction="(t) => (transaction = t)"
                :transaction="transaction"
              />
            </h2>
            <div class="divider my-1"></div>
            <div class="grid grid-cols-2 gap-2">
              <div class="text-sm opacity-70">Date:</div>
              <div class="font-medium">
                {{ formatDate(transaction.created_at) }}
              </div>

              <div class="text-sm opacity-70">Location:</div>
              <div class="font-medium">{{ transaction.place }}</div>

              <div class="text-sm opacity-70">Barangay:</div>
              <div class="font-medium">
                {{ transaction.barangay?.name ?? "N/A" }}
              </div>
              <div class="text-sm opacity-70">Animal:</div>
              <div class="font-medium">{{ transaction.animal?.name }}</div>
              <div class="text-sm opacity-70">Animal Status:</div>
              <div class="font-medium">
                {{ transaction.animal_status }}
              </div>
              <div class="text-sm opacity-70">Category:</div>
              <div class="font-medium">
                <CategoryBadge
                  class="badge-sm"
                  :category="transaction.category"
                />
              </div>

              <div class="text-sm opacity-70">Body Part:</div>
              <div class="font-medium">{{ transaction.body_part }}</div>

              <div class="text-sm opacity-70">Brand:</div>
              <div class="font-medium">{{ transaction.brand?.name! }}</div>

              <div class="text-sm opacity-70">Wash Bite:</div>
              <div
                class="font-medium"
                :class="transaction.wash_bite ? 'text-success' : 'text-error'"
              >
                {{ transaction.wash_bite ? "Yes" : "No" }}
              </div>
              <div class="text-sm opacity-70">RIG Given:</div>
              <div
                class="font-medium"
                :class="transaction.rig_given ? 'text-success' : 'text-error'"
              >
                {{ transaction.rig_given ? "Yes" : "No" }}
              </div>
              <div class="text-sm opacity-70">Booster Given:</div>
              <div
                class="font-medium"
                :class="transaction.booster_given ? 'text-success' : 'text-error'"
              >
                {{ transaction.booster_given ? "Yes" : "No" }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Schedule Timeline Section -->
      <div class="mt-8">
        <div class="flex items-center mb-4 gap-2">
          <Calendar :size="28" />
          <h2 class="text-2xl font-bold">Schedule</h2>
        </div>

        <div class="card bg-base-100 shadow-xl">
          <div class="card-body">
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>Day</th>
                    <th>User</th>
                    <th>Schedule Date</th>
                    <th>Visited Date</th>
                    <th>Remarks</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="schedule in transaction.schedules"
                    :key="schedule.id"
                  >
                    <td>
                      <div class="font-bold">{{ schedule.name }}</div>
                    </td>
                    <td>
                      <span v-if="schedule.user">
                        {{ schedule.user.username }} ({{
                          formatRole(schedule.user.role)
                        }})
                      </span>
                      <div v-else class="badge badge-warning badge-sm">
                        Ongoing
                      </div>
                    </td>
                    <td>
                      {{ formatDate(schedule.schedule) }}
                    </td>

                    <td>
                      {{
                        schedule.visited_date
                          ? formatDate(schedule.visited_date)
                          : "—"
                      }}
                    </td>
                    <td>{{ schedule.remarks || "—" }}</td>
                    <td>
                      <div
                        v-if="isVisited(schedule)"
                        class="badge badge-success"
                      >
                        Completed
                      </div>
                      <DialogButton
                        v-else
                        class="btn btn-info btn-soft btn-sm"
                        header="Confirm Patient Visit"
                        buttonText="Confirm Visit"
                        closeText="Close"
                        submitText="Confirm"
                        maxWidth="500"
                        :onSubmit="async () => await onConfirmVisit(schedule.id!)"
                      >
                        <fieldset class="fieldset">
                          <legend class="fieldset-legend">
                            Today's Remarks
                          </legend>
                          <textarea
                            v-model="remarks"
                            class="textarea h-24 w-full"
                            placeholder="Remarks"
                            >{{ remarks }}</textarea
                          >
                          <div class="fieldset-label">Optional</div>
                        </fieldset>
                      </DialogButton>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Treatment Progress -->
            <div v-if="transaction.schedules" class="mt-4">
              <p class="text-sm mb-2">Treatment Progress</p>
              <progress
                class="progress progress-success w-full"
                :value="completedSchedules"
                :max="transaction.schedules.length"
              ></progress>
              <div class="text-right text-sm mt-1">
                {{ completedSchedules }} of
                {{ transaction.schedules.length }} completed
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <!-- <div class="flex justify-end mt-6 gap-2">
          <button class="btn btn-outline">Print Record</button>
          <button class="btn btn-primary">Update Schedule</button>
        </div> -->
      </div>
    </div>
  </div>
</template>
