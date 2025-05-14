<script setup lang="ts">
import type { Patient, TransactionSchedule } from "~/lib/types";
const modalOpen = ref(false);
const { formatDate } = useDateFormatter();
const { formatName, formatRole } = useFormatter();
const props = defineProps<{
  schedules: TransactionSchedule[];
  patient: Patient;
}>();
</script>

<template>
  <button @click="modalOpen = true" class="btn btn-success btn-sm btn-soft">
    Schedule
  </button>
  <Modal
    @click-outside="modalOpen = false"
    max-width="800"
    :modal-open="modalOpen"
    header="Patient Schedule"
  >
    <div class="overflow-x-auto">
      <h1 class="text-base mb-5">Patient: {{ formatName(patient) }}</h1>
      <table class="table table-zebra w-full">
        <thead>
          <tr>
            <th>Day</th>
            <th>User</th>
            <th>Schedule</th>
            <th>Visited Date</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(schedule, index) in props.schedules"
            :key="index"
            class="hover"
          >
            <td>{{ schedule.name }}</td>
            <td>
              <span v-if="schedule.user">
                {{ schedule.user.username }} ({{
                  formatRole(schedule.user.role)
                }})
              </span>
              <div v-else class="badge badge-warning badge-sm">Ongoing</div>
            </td>
            <td>{{ formatDate(schedule.schedule) }}</td>
            <td>
              <span v-if="schedule.visited_date">
                {{ formatDate(schedule.visited_date) }}
              </span>
              <div v-else class="badge badge-warning badge-sm">Ongoing</div>
            </td>
            <td>
              <span :class="{ 'opacity-60': !schedule.remarks }">
                {{ schedule.remarks || "No remarks" }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </Modal>
</template>
