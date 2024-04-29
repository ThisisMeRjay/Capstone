<template>
  <div class="mx-4">
    <div class="py-3 px-10 font-bold text-2xl text-slate-700">
      <h1>Dashboard</h1>
    </div>

    <!-- Date Range Selection with Labels -->
    <div class="mb-4 flex items-end gap-4">
      <div class="flex items-center gap-3">
        <label for="startDate" class="block text-sm font-medium text-slate-700">From:</label>
        <input
          id="startDate"
          type="date"
          v-model="startDate"
          @change="updateDataBasedOnDateRange"
          class="mt-1 px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>
      <div class="flex items-center gap-3">
        <label for="endDate" class="block text-sm font-medium text-slate-700">To:</label>
        <input
          id="endDate"
          type="date"
          v-model="endDate"
          @change="updateDataBasedOnDateRange"
          class="mt-1 px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>
    </div>

    <!-- Dashboard Content -->
    <div class="sm:flex gap-5">
      <!-- Total Sales & Cost Block -->
      <div class="bg-slate-400/10 rounded-md h-32 w-full text-slate-700 shadow">
        <div class="p-2">
          <h1 class="text-base font-semibold">Revenue</h1>
          <p class="text-xs" v-if="isDefaultDateRange">Last 7 Days</p>
          <p class="text-xs" v-else>From {{ startDate }} to {{ endDate }}</p>
        </div>
        <div class="p-2">
          <h1 class="text-3xl font-extrabold">₱ {{ totalSales }}</h1>
        </div>
      </div>

      <!-- Inventory Status Block -->
      <div class="bg-slate-400/10 rounded-md h-32 w-full text-slate-700 shadow">
        <div class="p-2">
          <h1 class="text-base font-semibold">Total</h1>
          <p class="text-xs">As of {{ currentDate }}</p>
        </div>
        <div class="p-2">
          <h1 class="text-3xl font-extrabold">{{ totalStocks }}</h1>
        </div>
      </div>
    </div>
    <!-- Chart -->
    <div class="chart-container" style="position: relative; height:60vh; width:80vw">
      <Bar id="my-chart-id" :options="chartOptions" :data="chartData" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { userLogin, getUserFromLocalStorage } from "@/scripts/Seller";
import { Bar } from "vue-chartjs";
import moment from "moment-timezone";
import { API_URL } from "@/config";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
);

const chartData = ref({
  labels: [], // Initially empty, will be filled with month names
  datasets: [
    {
      label: "Real-Time Revenue",
      data: [], // Initially empty, will be filled with real-time sales data
      backgroundColor: "rgba(54, 162, 235, 0.2)",
      borderColor: "rgba(54, 162, 235, 1)",
      borderWidth: 2,
      borderRadius: 5,
    },
  ],
});

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false, // Add this line
  plugins: {
    title: {
      display: true,
      text: "MONTHLY REVENUE",
      font: {
        size: 20,
      },
      padding: {
        top: 30,
        bottom: 20,
      },
    },
    // Add other plugin options as needed
  },
});

const url = API_URL;

const totalSales = ref(0);
const totalStocks = ref("");
const startDate = ref("");
const endDate = ref("");
const currentDate = ref("");
const isDefaultDateRange = ref(true);

const fetchRealTimeMonthlySales = async () => {
  try {
    const response = await axios.post(
      `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=fetchRealTimeMonthlySales`,
      { store_id: userLogin.value.store_id }
    );

    const realTimeData = response.data;
    console.log("realtime ", realTimeData);

    if (realTimeData.error) {
      console.error("Error:", realTimeData.message);
      return;
    }

    // Helper function to convert month number to month name
    const getMonthName = (monthNumber) => {
      const date = new Date(0); // Create a date far in the past
      date.setMonth(monthNumber - 1); // Set the month of the date. Month is 0-indexed
      return date.toLocaleString("default", { month: "long" });
    };

    const labels = realTimeData.map((item) =>
      getMonthName(parseInt(item.saleMonth))
    );
    const salesData = realTimeData.map((item) => parseFloat(item.totalSales)); // Ensure data is in the correct format

    console.log("labels ", labels);
    console.log("salesData ", salesData);

    chartData.value = {
      ...chartData.value,
      labels: labels,
      datasets: [
        {
          ...chartData.value.datasets[0],
          data: salesData,
        },
      ],
    };

    console.log(
      "Real-time monthly sales data fetched and chart updated successfully."
    );
  } catch (error) {
    console.error("Error fetching real-time monthly sales data:", error);
  }
};

// Fetches total sales and costs data
const fetchSalesData = async (start, end) => {
  console.log("start ", start);
  console.log("end ", end);
  try {
    const response = await axios.post(
      `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=fetchRevenue`,
      {
        start: start,
        end: end
      }
    );
    totalSales.value = response.data.totalSales;
    console.log("Sales data fetched successfully:", totalSales.value);
  } catch (error) {
    console.error("Error fetching sales data:", error);
    // Handle the error, e.g., by showing an error message to the user.
  }
};

// Fetches current inventory status, independently of the date range for sales/costs
const fetchCurrentInventoryStatus = async () => {
  const today = new Date().toISOString().split("T")[0];
  try {
    const response = await axios.post(
      `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=totalRevenue`);
    totalStocks.value = "Revenue: " + response.data.totalQuantity;
    currentDate.value = today; // Update the reference date for inventory status display
    console.log("Stocks:", totalStocks.value);
  } catch (error) {
    console.error("Error fetching sales data:", error);
    // Handle the error, e.g., by showing an error message to the user.
  }
};

const updateDataBasedOnDateRange = async () => {
  if (startDate.value && endDate.value) {
    fetchSalesData(startDate.value, endDate.value);
    isDefaultDateRange.value = false;
    console.log("show ", isDefaultDateRange.value);
  }
  // Always fetch the current inventory status, regardless of the selected date range
  fetchCurrentInventoryStatus();
};

// Initialize isDefaultDateRange based on whether the initially set dates match the last 7 days
onMounted(() => {
  getUserFromLocalStorage();

  // Use moment-timezone to set the time to Philippine time zone (UTC+8)
  const philippinesTime = moment().tz("Asia/Manila");

  // Format the date as needed for your inputs
  const todayFormatted = philippinesTime.format("YYYY-MM-DD");
  const last7DaysFormatted = philippinesTime
    .subtract(7, "days")
    .format("YYYY-MM-DD");

  startDate.value = last7DaysFormatted;
  endDate.value = todayFormatted;

  // Fetch both sales data and current inventory status on mount
  fetchSalesData(startDate.value, endDate.value);
  fetchCurrentInventoryStatus();
  fetchRealTimeMonthlySales();
});
</script>
<style>
#my-chart-id {
  margin-top: 3%;
  border: 2px solid #909090db; /* Example: 2px solid black border */
  border-radius: 5px; /* Optional: if you want rounded corners */
}
</style>
