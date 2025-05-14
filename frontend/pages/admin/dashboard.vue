<script setup>
import BarChartCases from "~/components/charts/BarChartCases.vue";
import PieChartCases from "~/components/charts/PieChartCases.vue";
import SimpleWidget from "~/components/widgets/SimpleWidget.vue";
import TopBarangayCases from "~/components/charts/TopBarangayCases.vue";

const {getCounts} = useTransactionService();
const {data: counts} = getCounts()
</script>


<template>
  <div class="mx-5 mb-5 mt-10">
    <div class="flex content-center justify-center gap-5 mb-5">
      <div v-for="(count, key) in counts">
        <SimpleWidget :title="key" :number="count" color="#50d71e"/>
      </div>
    </div>
    <BarChartCases/>
    <div class="flex gap-5">
      <PieChartCases/>
      <TopBarangayCases/>
    </div>
  </div>
</template>
