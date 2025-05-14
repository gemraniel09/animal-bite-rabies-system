<script setup lang="ts">

import { ref, watch, onMounted } from "vue"; // lifecycle hooks
import { Chart, registerables } from "chart.js";
import { BarChart } from "vue-chart-3"; 


Chart.register(...registerables);

//  para sa  API
const { getMonth6Cases } = useTransactionService();

//  Gumamit ng ref para sa data, loading state, at function para kunin ang data
const { data: transactions, loading, execute } = getMonth6Cases({ immediate: false });

//  Function para makuha ang 6 months
const getLastSixMonths = () => {
  const months = [];
  const now = new Date();

  //  Loop para sa 12 months
  for (let i = 23; i >= 0; i--) {
    const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
    months.push({
      year: date.getFullYear(),
      month: date.getMonth() + 1, //  +1 kasi 0 based ang buwan
      label: date.toLocaleString("default", { month: "short" }) + ` ${date.getFullYear()}`, // para sa date
    });
  }

  return months;
};

//  Kunin ang labels ng nakaraang 6 mothns
const months = getLastSixMonths();

// I-set up ang default na chart data
const chartData = ref({
  labels: months.map((m) => m.label), //  pabalagbag ung design
  datasets: [
    {
      label: "Cases", 
      backgroundColor: "#3b25c1", 
      data: new Array(months.length).fill(0), // default, 0 muna lahat
    },
  ],
});


const chartOptions = ref({
  responsive: true, // responsive pwede paliitin or palakihin
  maintainAspectRatio: false, // para hindi mabago ang ratio
});

//  pag may nilagay na bagong data
watch(transactions, (newTransactions) => {
  if (!newTransactions) return; // hiindi itutuloy ang pag lalagay

  //  Gumawa ng Map para matiyak na lahat ng buwan ay may value (kahit 0)
  const monthMap = new Map(months.map((m) => [m.label, 0]));

  // Mag update sa bagong transactions
  newTransactions.forEach((val) => {
    if (monthMap.has(val.label)) {
      monthMap.set(val.label, val.count);
    }
  });

  //  I-update ang chart data para ma-reflect ang bagong values
  chartData.value.datasets[0].data = months.map((m) => monthMap.get(m.label) || 0);
});

//  Kapag na-mount ang component, simulan ang pagkuha ng data
onMounted(() => {
  execute(); // pang tawag sa api
});
</script>

<template>
  <div class="rounded-2xl shadow-sm p-6 border border-gray-200">
    <h2 class="text-xl font-semibold mt-4">Cases Report: Past 12 Months</h2>

   <!-- loading loading -->
    <div v-if="loading">Loading...</div>

    <!-- output -->
    <BarChart v-else :chartData="chartData" :chartOptions="chartOptions" />
  </div>
</template>
