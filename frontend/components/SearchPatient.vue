<script setup lang="ts">
const patientService = usePatientService();
import { ChevronRight, Search } from "lucide-vue-next";
import type { Patient } from "~/lib/types";

const {
  data: patients,
  loading,
  execute,
} = patientService.getPatients(
  //Don't fetch data immediately
  { immediate: false }
);
const searchVal = ref("");
const modalOpen = ref(false);

watch(searchVal, () => {
  execute({
    showLoading: true,
    params: {
      name: searchVal.value,
      limit: 10,
    },
  });
});

const props = defineProps<{
  onOldPatientSelect: (patient: Patient) => void;
}>();
function onOldPatientSelect(patient: Patient) {
  props.onOldPatientSelect(patient);
  modalOpen.value = false;
}
</script>

<template>
  <button @click="modalOpen = true" class="btn flex gap-2 btn-primary">
    <Search />Search Old Patient
  </button>
  <Modal
    v-on:click-outside="modalOpen = false"
    :modal-open="modalOpen"
    header="Search Patient"
    max-width="600"
  >
    <div>
      <label class="input w-full">
        <span v-if="loading" class="loading loading-bars loading-lg"></span>
        <Search v-if="!loading" />
        <input
          v-model="searchVal"
          type="search"
          class="grow"
          placeholder="Search"
        />
      </label>

      <div class="mt-5 max-h-96 overflow-y-auto flex flex-col gap-1">
        <ul
          v-for="patient in patients"
          class="list bg-base-100 rounded-box shadow-md"
        >
          <li
            class="list-row flex justify-between items-center cursor-pointer"
            @click="onOldPatientSelect(patient)"
          >
            <div>
              <div class="text-xs uppercase font-semibold opacity-60">
                {{
                  patient.first_name +
                  " " +
                  patient.middle_name +
                  ". " +
                  patient.last_name
                }}
              </div>
              <div class="text-xs uppercase opacity-60">
                {{ patient.gender }}
              </div>
            </div>
            <ChevronRight />
          </li>
        </ul>
      </div>
    </div>
  </Modal>
</template>
