<script setup lang="ts">

// FUUNCTION FOR PREDICTION
const {getPredictedCases} = useTransactionService();

// FETCHIING API
const {
  data: transactions,
  loading,
  execute,
} = getPredictedCases({immediate: false});

//PANG PREDICTIOPN
const exec = () => {
  execute({
    showLoading: true,
    params: {
      monthYear: monthYear.value // EXUCTING THE RIGHT MONTH
    }
  });
};

// PARA SA MONTH NA NAPILI
const monthYear = ref(null)

// PAG NABAGO ANG DATA AUTOMATIC MAG EXECUTE
watch(monthYear, () => exec());

// iseset ang default na buwan at taon sa susunod na buwan
onMounted(() => {
  const date = new Date();
  date.setMonth(date.getMonth() + 1);
  monthYear.value = date.toISOString().slice(0, 7)
  exec(); // OUTPPUT
})
</script>
<template>
  <div class="mx-5 mb-5 mt-10 overflow-x-auto">
    <div class="grid grid-cols-2 gap-2 w-full">
      <div></div>
      <div>
        <div class="font-bold">Choose Month and Year:</div>
        <input type="month" class="input input-bordered w-full" v-model="monthYear">
      </div>
    </div>
    <div class="mt-10 mb-8" style="width: 80%">
      This tool helps clinics prepare for animal bite cases by predicting future trends based on past data. Select a
      month and year to estimate cases in each barangay and ensure adequate medical supplies and staff readiness.
    </div>
    <div class="legend">
      <div class="title">Risk Levels for Animal Bite Cases</div>
      <div class="item"><span class="color low"></span>Low Risk (0-5 cases) – Minimal cases, routine precautions
        recommended.
      </div>
      <div class="item"><span class="color moderate"></span>Moderate Risk (6-15 cases) – Increased cases, clinics should
        prepare supplies.
      </div>
      <div class="item"><span class="color high"></span>High Risk (16-30 cases) – Significant rise, ensure staff
        readiness and vaccine availability.
      </div>
      <div class="item"><span class="color critical"></span>Critical Risk (31+ cases) – Very high cases, urgent
        preparedness needed.
      </div>
    </div>
    <table class="table">
      <thead>
      <tr>
        <th>#</th>
        <th>Barangay</th>
        <th>Total Cases</th>
        <th>Prediction</th>
      </tr>
      </thead>
      <tbody>
      <tr v-if="loading">
        <td colspan="8 " class="text-center">
          <p class="loading text-center"></p>
        </td>
      </tr>
      <tr v-else v-for="(barangay, index) in transactions">
        <td>{{ index + 1 }}</td>
        <td><div :class="'box ' + barangay.risk_level.toLowerCase()"></div> {{ barangay.name }}</td>
        <td>{{ barangay.transaction_count }}</td>
        <td> {{ barangay.predictedCase }}</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.legend {
  .title {
    font-weight: bolder;
  }

  .item {
    .color {
      display: inline-block;
      width: 20px;
      height: 20px;
      margin-right: 10px;

      &.low {
        background-color: #00d26a;
      }

      &.moderate {
        background-color: #fcd53f;
      }

      &.high {
        background-color: #ff6723;
      }

      &.critical {
        background-color: #f8312f;
      }
    }
  }
}

table {
  tr {
    td {
      .box {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        &.low {
          background-color: #00d26a;
        }

        &.moderate {
          background-color: #fcd53f;
        }

        &.high {
          background-color: #ff6723;
        }

        &.critical {
          background-color: #f8312f;
        }
      }
    }
  }
}
</style>
