<template>
  <div class="p-2 border border-slate-900/20 rounded-md">
    <div class="relative overflow-x-auto">
      <h1 class="pb-3 sticky left-0">Ready for Pickup</h1>
      <table class="min-w-full text-sm text-left text-gray-900 sm:rounded-md">
        <thead
          class="text-xs text-sky-100 uppercase bg-gradient-to-r from-blue-500 from-10% via-violet-500 via-30% to-orange-500 to-90% sm:rounded-md"
        >
          <tr>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">Store</th>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">Order number</th>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">Customer name</th>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">Contact no.</th>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">Estimated delivery</th>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">status</th>
            <th scope="col" class="px-2 py-2 sm:px-6 sm:py-3">order details</th>
          </tr>
        </thead>
        <tbody v-for="(order, index) in orders" :key="index">
          <tr class="bg-gray-200 border-b border-gray-700 text-xs">
            <td class="px-2 py-2 sm:px-6 sm:py-4">{{ order.store_name }}</td>
            <td class="px-2 py-2 sm:px-6 sm:py-4">{{ order.order_number }}</td>
            <td class="px-2 py-2 sm:px-6 sm:py-4">{{ order.username }}</td>
            <td class="px-2 py-2 sm:px-6 sm:py-4">
              {{ order.contact_number }}
            </td>
            <td class="px-2 py-2 sm:px-6 sm:py-4">
              {{ order.estimated_delivery }}
            </td>
            <td class="px-2 py-2 sm:px-6 sm:py-4">
                <p
                @click="updateStatus(order.order_detail_id)"
                      class="shadow px-3 py-1 text-center rounded-full flex justify-center gap-3 cursor-pointer hover:bg-slate-200 transition"
                      :class="{
                        'text-orange-500 bg-orange-300/10':
                        order.status === 'pending',
                        'text-gray-500 bg-yellow-300/10':
                        order.status === 'confirmed',
                        'text-gray-700 bg-gray-200/10':
                        order.status === 'processing',
                        'text-yellow-600 bg-yellow-200/10':
                        order.status === 'ready_to_pickup',
                        'text-pink-400 bg-blue-300/10':
                        order.status === 'reserved_for_rider',
                        'text-blue-500 bg-blue-300/10':
                        order.status === 'out_for_delivery',
                        'text-green-500 bg-green-300/10':
                        order.status === 'delivered',
                        'text-red-500 bg-red-300/10':
                        order.status === 'cancelled',
                        'text-purple-500 bg-purple-300/10':
                        order.status === 'delayed',
                        'text-pink-500 bg-pink-300/10':
                        order.status === 'return_in_progress',
                        'text-teal-500 bg-teal-300/10':
                        order.status === 'return_completed',
                        'text-red-600 bg-red-200/10':
                        order.status === 'return_declined',
                        'text-blue-400 bg-blue-200/10':
                        order.status === 'return_requested',
                        'text-blue-600 bg-blue-300/10':
                        order.status === 'return_approved',
                        'text-gray-500 bg-gray-300/10':
                        order.status === 'closed',
                      }"
                    >
                      <div>
                        {{ order.status }}
                      </div>
                      <div>
                        <Icon
                        icon="material-symbols:edit"
                        class="text-lg text-green-500"
                      />
                      </div>
                    </p>
            </td>
            <td class="px-2 py-2 sm:px-6 sm:py-4">
              <button
                @click="getDetails(order.order_detail_id)"
                class="bg-blue-500 p-1 items-center flex rounded hover:bg-blue-600"
              >
                Details
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- edit status modal -->
  <div
    v-if="showStatusModal"
    class="z-50 fixed top-0 left-0 h-full w-full flex justify-center items-center"
  >
    <form
      @submit.prevent="handleEditStatusOrder"
      class="w-72 bg-gray-200 p-5 rounded-md shadow text-xs"
    >
      <div>
        <h1 class="text-lg font-semibold text-blue-400">
          Select order Status:
        </h1>
        <select v-model="selectValue" class="w-full p-2 rounded-md">
          <option v-for="option in options" :value="option.value">
            {{ option.text }}
          </option>
        </select>
      </div>
      <!-- Add more inputs as needed here -->
      <div class="flex justify-evenly my-5 gap-5 items-center">
        <button
          type="button"
          @click="closeEditStatusModal()"
          class="px-4 py-2 bg-gray-400/20 text-slate-700 w-full rounded-md shadow"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="px-4 py-2 bg-green-400/20 text-green-700 hover:bg-green-500/25 w-full rounded-md shadow"
        >
          Update
        </button>
      </div>
    </form>
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
          <li>
            <strong>Address:</strong> {{ detail.address }}, {{ detail.Zone }},
            {{ detail.name }}
          </li>
          <li><strong>Estimated Delivery:</strong> {{ detail.estimated_delivery }}</li>
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
import { Icon } from "@iconify/vue";
import moment from "moment-timezone";
export default {
  components: {
    Icon,
  },
  setup() {
    const showModal = ref(false);
    const url = API_URL;

    const refreshPage = () => {
      location.reload(true);
    };

    let selectValue = ref("");

    let estimatedDelivery = ref("");

    const showStatusModal = ref(false);

    const editableOrderStatus = ref([]);

    const userOrderName = ref(""); // constaining  the name of the order that is being edited in status modal

    let orderIdToEdit = ref(null);

    const temp_orders = ref([]);

    onMounted(() => {
      getUserFromLocalStorage();
      getOrders();
      updateOptions();
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

      const updateStatus = async (orderId) => {
      orderIdToEdit.value = orderId;
      const orderToEdit = orders.value.find(
        (order) => order.order_detail_id === orderId
      );
      if (orderToEdit) {
        editableOrderStatus.value = orderToEdit; // Direct assignment without spreading
        userOrderName.value = editableOrderStatus.value.username;
        selectValue.value = editableOrderStatus.value.status;
        updateOptions();
        if (
          editableOrderStatus.value.status == "reserved_for_rider" ||
          editableOrderStatus.value.status == "out_for_delivery" ||
          editableOrderStatus.value.status == "delayed"
        ) {
          showStatusModal.value = true;
        } else {
          showStatusModal.value = false;
        }
      }
    };
    const closeEditStatusModal = () => {
      showStatusModal.value = false;
    };

    const options = ref([]);

    const updateOptions = () => {
      if (selectValue.value === "reserved_for_rider") {
        // Only show 'Out for delivery' and 'Delivered' when 'out_for_delivery' is selected
        options.value = [
          { value: "reserved_for_rider", text: "Reserved for rider" },
          { value: "out_for_delivery", text: "Out for delivery" },
        ];
      } else if (selectValue.value === "out_for_delivery") {
        // Only show 'Out for delivery' and 'Delivered' when 'out_for_delivery' is selected
        options.value = [
          { value: "out_for_delivery", text: "Out for delivery" },
          { value: "delivered", text: "Delivered" },
          { value: "delayed", text: "Delayed" },
        ];
      } else if (selectValue.value === "delayed") {
        // Only show 'Out for delivery' and 'Delivered' when 'out_for_delivery' is selected
        options.value = [
          { value: "delayed", text: "Delayed" },
          { value: "return_in_progress", text: "Return in Progress" },
        ];
      } 
    }; 

    const handleEditStatusOrder = async () => {
      console.log("Status: ", selectValue.value);
      console.log("orderId:  ", orderIdToEdit.value);
      console.log("estimated date:  ", estimatedDelivery.value);

      // Generate current date and time in Philippine time zone and format it
      const DateToupdate = moment()
        .tz("Asia/Manila")
        .format("YYYY-MM-DD HH:mm:ss");
      console.log("date:  ", DateToupdate);

      try {
        const response = await axios.put(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=EditStatus`,
          {
            id: orderIdToEdit.value,
            status: selectValue.value,
            estimated_delivery: estimatedDelivery.value,
            date: DateToupdate,
          }
        );
        // Assuming you might want to do something with the response here
        console.log(response.data);
      } catch (error) {
        console.error("Error editing status:", error);
      }
      refreshPage();
    };

    return {
      handleEditStatusOrder,
      closeEditStatusModal,
      updateStatus,
      getDetails,
      details,
      showModal,
      getOrders,
      orders,
      selectValue,
      estimatedDelivery,
      showStatusModal,
      editableOrderStatus,
      userOrderName,
      orderIdToEdit,
      temp_orders,
      options,
      updateOptions,
    };
  },
};
</script>
