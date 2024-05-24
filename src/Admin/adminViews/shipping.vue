<template>
  <div class="py-2">
    <p class="text-sm">Base Shipping Fee:</p>
    <div class="flex gap-2 justify-between">
      <input type="number" v-model="baseShippingFee" class="p-2 rounded-md" />
      <button
        @click="calculateShippingFee"
        class="text-sm p-2 bg-blue-500 hover:bg-blue-700 text-white rounded-md"
      >
        Calculate Shipping Fee
      </button>
      <button
        @click="confirmSaveShippingFee"
        class="text-sm p-2 bg-green-500 hover:bg-green-700 text-white rounded-md"
      >
        Save Shipping Fee
      </button>
    </div>
  </div>

  <div class="gap-5 flex items-end mt-4">
    <div>
      <h1 class="text-xs font-medium">Weight (kg):</h1>
      <input
        type="number"
        class="w-full p-2 rounded-md my-1 border outline-none"
        v-model="weight"
      />
    </div>
    <div>
      <h1 class="text-xs font-medium">Height (cm):</h1>
      <input
        type="number"
        class="w-full p-2 rounded-md my-1 border outline-none"
        v-model="height"
      />
    </div>
    <div>
      <h1 class="text-xs font-medium">Length (cm):</h1>
      <input
        type="number"
        class="w-full p-2 rounded-md my-1 border outline-none"
        v-model="length"
      />
    </div>
    <div>
      <h1 class="text-xs font-medium">Width (cm):</h1>
      <input
        type="number"
        class="w-full p-2 rounded-md my-1 border outline-none"
        v-model="width"
      />
    </div>
  </div>

  <div v-if="shippingFees.length > 0" class="mt-4 overflow-y-auto">
    <h2 class="text-xl font-bold mb-2">Shipping Fees by Barangay</h2>
    <div class="overflow-x-auto h-[500px]">
      <table class="w-full border-collapse table-auto">
        <thead>
          <tr class="sticky top-0">
            <th class="py-2 px-4 bg-gray-200 text-left">Barangay</th>
            <th class="py-2 px-4 bg-gray-200 text-left">Shipping Fee</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(fee, index) in shippingFees" :key="index">
            <td class="py-2 px-4 border-b">{{ fee.barangay }}</td>
            <td class="py-2 px-4 border-b">{{ fee.fee }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from "vue";
import axios from "axios";
import { getDistance } from "geolib";
import { API_URL } from "@/config";

export default {
  setup() {
    const url = API_URL;
    const baseShippingFee = ref(0);
    const weight = ref(null);
    const height = ref(null);
    const length = ref(null);
    const width = ref(null);
    const shippingFees = ref([]);

    const calculateShippingFee = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/auth.php?action=getBrgy`
        );
        const barangays = response.data;
        const volumeCm3 = length.value * width.value * height.value;
        const weightFactor = 1; // Cost per kilogram
        const volumeFactor = 0.005; // Cost per cubic centimeter
        const distanceFactor = 0.001; // Cost per meter

        const shippingFeesByBarangay = barangays.map((barangay) => {
          const customerLocation = {
            latitude: parseFloat(barangay.lat),
            longitude: parseFloat(barangay.lon),
          };

          const productLocation = {
            latitude: 13.12929, // Replace with the customer's latitude
            longitude: 123.75641, // Replace with the customer's longitude
          };

          const distanceMeters = getDistance(productLocation, customerLocation);

          // Convert baseShippingFee to a number with decimal places
          const baseShippingFeeWithDecimal = parseFloat(baseShippingFee.value);

          const fee =
            baseShippingFeeWithDecimal +
            distanceMeters * distanceFactor +
            weight.value * weightFactor +
            volumeCm3 * volumeFactor;

          return {
            barangay: barangay.name,
            fee: fee.toFixed(2),
          };
        });

        shippingFees.value = shippingFeesByBarangay;
      } catch (error) {
        console.error("Error calculating shipping fees:", error);
      }
    };

    const shipping = ref("");

    const fetchProducts = async () => {
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=getAllProducts`
        );
        shipping.value = response.data;
        baseShippingFee.value = shipping.value.shipping_fee;
        console.log("Products ", shipping.value.shipping_fee);
      } catch (error) {
        console.error("Error fetching orders:", error);
      }
    };

    const confirmSaveShippingFee = () => {
      if (confirm("Are you sure you want to save the new shipping fee?")) {
        saveShippingFee();
      }
    };

    const saveShippingFee = async () => {
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=updateShippingFee`,
          {
            shipping_fee: baseShippingFee.value,
          }
        );
        console.log("Shipping fee saved:", response.data);
        // Optionally, you can show a success message or perform additional actions
      } catch (error) {
        console.error("Error saving shipping fee:", error);
        // Optionally, you can show an error message or perform error handling
      }
    };

    fetchProducts();

    return {
      shipping,
      fetchProducts,
      baseShippingFee,
      weight,
      height,
      length,
      width,
      shippingFees,
      calculateShippingFee,
      confirmSaveShippingFee,
    };
  },
};
</script>
