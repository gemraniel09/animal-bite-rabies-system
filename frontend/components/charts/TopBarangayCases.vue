<script setup>
import {ref} from "vue";
import {Chart, registerables} from "chart.js";
import {PieChart} from "vue-chart-3"; 


Chart.register(...registerables);

const {getTopBarangay} = useTransactionService();

const {
  data: transactions,
} = getTopBarangay();

</script>
<template>
  <div class="w-[50%] mt-5 rounded-2xl shadow-sm p-6 border border-gray-200">
    <h2 class="font-bold">Top Barangay Cases: Past Months</h2>
    <table class="table table-zebra over">
      <thead>
      <tr>
        <th>#</th>
        <th>Barangay</th>
        <th>Cases</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(value, index) in transactions">
        <td>{{ index + 1 }}</td>
        <td>{{value.name}}</td>
        <td>{{value.transaction_count}}</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>

</style>