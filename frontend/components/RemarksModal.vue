<script setup lang="ts">
const modalOpen = ref(false);
const remarks = ref("");
const props = defineProps<{
  onRemarksSave: (remarks: string) => void;
  currentRemarks: string;
}>();
function onRemarksSave(remark: string) {
  props.onRemarksSave(remark);
  remarks.value = remark;
  modalOpen.value = false;
}
function onCancel() {
  modalOpen.value = false;
  remarks.value = props.currentRemarks;
}
</script>

<template>
  <div>
    <button class="btn btn-info btn-sm" type="button" @click="modalOpen = true">
      Remarks
    </button>

    <Modal
      :modal-open="modalOpen"
      header="Remarks"
      @click-outside="onCancel()"
      max-width="600"
    >
      <fieldset class="fieldset">
        <legend class="fieldset-legend">Today's Remarks</legend>
        <textarea
          v-model="remarks"
          class="textarea h-24 w-full"
          placeholder="Remarks"
          >{{ props.currentRemarks }}</textarea
        >
        <div class="fieldset-label">Optional</div>
      </fieldset>
      <div class="card-actions justify-end mt-6 gap-2">
        <button @click="onCancel" class="btn btn-ghost">Cancel</button>
        <button @click="onRemarksSave(remarks)" class="btn btn-success">
          Confirm
        </button>
      </div>
    </Modal>
  </div>
</template>
