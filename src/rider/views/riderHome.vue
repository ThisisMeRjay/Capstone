<template>
  <div class="p-2 border border-slate-900/20 rounded-md">
    <div class="relative overflow-x-auto">
      <h1 class="pb-3">Ready for Pickup</h1>
      <table class="min-w-full text-sm text-left text-gray-900 sm:rounded-md">
        <thead
          class="text-xs text-sky-100 uppercase bg-gradient-to-r from-blue-500 from-10% via-violet-500 via-30% to-orange-500 to-90% sm:rounded-md"
        >
          <tr>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">Customer name</th>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">Contact no.</th>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">status</th>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">order details</th>
          </tr>
        </thead>
        <tbody v-for="(order, index) in orders" :key="index">
          <tr class="bg-gray-200 border-b border-gray-700">
            <td class="px-2 py-2 sm:px-6 sm:py-4">{{ order.username }}</td>
            <td class="px-2 py-2 sm:px-6 sm:py-4">
              {{ order.contact_number }}
            </td>
            <td class="px-2 py-2 sm:px-6 sm:py-4">
              <p class="bg-orange-300 p-1 rounded-full px-2">
                {{ order.status }}
              </p>
            </td>
            <td class="px-2 py-2 sm:px-6 sm:py-4 flex justify-center">
              <button
                @click="getDetails(order.order_detail_id)"
                class="bg-blue-500 p-3 rounded hover:bg-blue-600"
              >
                Details
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal for order details -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4"
    >
      <div class="bg-white rounded-lg p-5 shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Order Details</h2>
        <ul class="space-y-2" v-for="(detail, index) in details" :key="index">
          <li><strong>Customer name:</strong> {{ detail.username }}</li>
          <li><strong>Contact no:</strong> {{ detail.contact_number }}</li>
          <li><strong>Address:</strong> {{ detail.address }}, {{ detail.Zone }}, {{ detail.name }}</li>
          <li><strong>Product:</strong> {{ detail.product_name }}</li>
          <li><strong>Quantity:</strong> {{ detail.quantity }}</li>
          <li><strong>Price:</strong> {{ detail.total_price_products }}</li>
        </ul>
        <div class="mt-4 flex justify-between">
          <div></div>
          <button
            @click="showModal = false"
            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";
import { API_URL } from "@/config";
import { userLogin, getUserFromLocalStorage } from "@/scripts/Rider";
export default {
  setup() {
    const showModal = ref(false);
    const url = API_URL;

    onMounted(() => {
      getUserFromLocalStorage();
      getOrders();
    });

    const orders = ref([]);

    const getOrders = async () => {
      try {
        const urli = `${url}/Ecommerce/vue-project/src/backend/rider/riderApi.php?action=getOrders`;
        const res = await axios.post(
          urli,
          {
            id: userLogin.value.rider_id,
          },
          { headers: { "Content-Type": "application/json" } }
        );
        console.log(res.data);
        orders.value = res.data;
      } catch {}
    };

    const details = ref([]);

    const getDetails = async (ID) => {
      showModal.value = true;
      console.log("rider ID: ", ID);
      try {
        const urli = `${url}/Ecommerce/vue-project/src/backend/rider/riderApi.php?action=getDetails`;
        const res = await axios.post(
          urli,
          {
            id: userLogin.value.rider_id,
            detail_id: ID,
          },
          { headers: { "Content-Type": "application/json" } }
        );
        console.log(res.data);
        details.value = res.data;
      } catch {}
    };

    return {
      getDetails,
      details,
      showModal,
      getOrders,
      orders,
    };
  },
};
</script>
@/scripts/Rider
