<script setup>
import { ref, watch, onMounted } from "vue";
import { Chart, registerables } from "chart.js"; 
import { PieChart } from "vue-chart-3"; 


Chart.register(...registerables);

// Kunin ang function na nagbabalik ng bilang ng kagat ng hayop
const { getOnlyAnimalsCount } = useTransactionService();

// pang fetchh
const {
  data: transactions,
  loading,
  execute,
} = getOnlyAnimalsCount({ immediate: false }); // need muna ixecute


const getRandomHexColor = () => {
  return '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
};

// details sa pie chart
const chartData = ref({
  labels: ["Dog"], 
  datasets: [
    {
      label: "Animal Bites Count", 
      backgroundColor: [], 
      data: [], 
    },
  ],
});

// Mga setting ng chart
const chartOptions = ref({
  responsive: true, 
  plugins: {
    legend: { position: "bottom" }, 
    tooltip: { enabled: true }, 
  },
});


watch(transactions, (newTransactions) => {
  chartData.value.labels = Object.keys(newTransactions); // para pag pag ni update and data
  chartData.value.datasets[0].data = Object.values(newTransactions); //  Ina-update na ang data ng chart
  chartData.value.datasets[0].backgroundColor = Object.values(newTransactions).map(() => getRandomHexColor()); 
});

onMounted(() => {
  execute(); //  pang tawag ng api
});
</script>

<template>

  <div class="w-[50%] mt-5 rounded-2xl shadow-sm p-6 border border-gray-200">
    <PieChart :chart-data="chartData" :chart-options="chartOptions" />
  </div>
</template>

<style scoped>
</style>
