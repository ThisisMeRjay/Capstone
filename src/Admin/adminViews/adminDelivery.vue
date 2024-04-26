<template>
  <div class="mx-4">
    <div class="pb-3 px-2 font-bold text-2xl text-sky-900">
      <h1>Delivery</h1>
    </div>
    <hr />
    <div class="flex mt-5 w-full">
      <div>
        <div class="flex gap-3">
          <div
            class="border rounded-md w-60 shadow flex justify-between items-center px-4"
          >
            <input
              type="number"
              v-model="searchQuery"
              @change="filterBySearch"
              placeholder="Search by order number"
              class="outline-none placeholder:text-sm placeholder:font-light py-2 pl-2 w-full rounded-full"
            />
            <Icon
              icon="carbon:search"
              class="text-xl text-slate-500 my-2 ml-2"
            />
          </div>

          <form class="flex justify-center items-center ml-3 text-sm gap-3">
            <label for="">Status: </label>
            <select
              id="status"
              v-model="selectedStatus"
              @change="filteredOrders"
              class="shadow border text-gray-900 outline-none text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-32 px-3 py-2.5"
            >
              <option value="">Default</option>
              <option value="processing">Processing</option>
              <option value="out_for_delivery">Out for Delivery</option>
              <option value="delivered">Delivered</option>
            </select>
          </form>
        </div>

        <div class="my-5 w-full p-2 border border-slate-900/20 rounded-md">
          <div
            class="relative max-w-[1200px] overflow-x-auto shadow-md rounded-md max-h-[500px]"
          >
            <table
              class="min-w-full text-sm text-left rtl:text-right text-gray-900 rounded-md"
            >
              <thead
                class="text-xs text-sky-100 uppercase bg-sky-900 rounded-md sticky top-0 z-40"
              >
                <tr
                  class="text-center bg-gray-100/10 border-b border-gray-600/50"
                >
                  <th scope="col" class="px-6 py-2 sticky left-0 bg-sky-900">
                    Order Number
                  </th>
                  <th scope="col" class="px-6 py-2">Product name</th>
                  <th scope="col" class="px-6 py-2">store name</th>
                  <th scope="col" class="px-6 py-2">STATUS</th>
                  <th scope="col" class="px-6 py-2">QUANTITY</th>
                  <th scope="col" class="px-6 py-2">CUSTOMER NAME</th>
                  <th scope="col" class="px-6 py-2">PRICE</th>
                  <th scope="col" class="px-6 py-2">PAYMENT METHOD</th>
                  <th scope="col" class="px-6 py-2">ESTIMATED DELIVERY</th>
                  <th scope="col" class="px-6 py-2">out for delivery</th>
                  <th scope="col" class="px-6 py-2">date delivered</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <tr
                  v-for="item in orders"
                  :key="item.id"
                  class="bg-gray-100/10 border-b border-gray-600/50"
                >
                  <td class="px-6 py-1 sticky left-0 top-0 bg-slate-50 z-10">
                    {{ item.order_number }}
                  </td>
                  <td class="px-6 py-1">{{ item.product_name }}</td>
                  <td class="px-6 py-1">{{ item.store_name }}</td>
                  <td class="px-6 py-1">
                    <p
                      @click="editStatus(item.order_detail_id)"
                      class="shadow px-3 py-1 text-center rounded-full flex justify-center gap-1 cursor-pointer hover:bg-slate-200 transition"
                      :class="{
                        'text-orange-500 bg-orange-300/10':
                          item.status === 'pending',
                        'text-gray-500 bg-yellow-300/10':
                          item.status === 'confirmed',
                        'text-yellow-500 bg-yellow-300/10':
                          item.status === 'processing',
                        'text-blue-500 bg-blue-300/10':
                          item.status === 'out_for_delivery',
                        'text-green-500 bg-red-300/10':
                          item.status === 'delivered',
                        'text-red-500 bg-red-300/10':
                          item.status === 'cancelled',
                      }"
                    >
                      {{ item.status }}
                      <Icon
                        icon="material-symbols:edit"
                        class="text-lg text-green-500"
                      />
                    </p>
                  </td>
                  <td class="px-6 py-1">{{ item.quantity }}</td>
                  <td class="px-6 py-1">{{ item.username }}</td>
                  <td class="px-6 py-1">{{ item.total_price_products }}</td>
                  <td class="px-6 py-1">
                    <p class="text-violet-600">
                      {{ item.payment_method }}
                    </p>
                  </td>
                  <td class="px-6 py-1">
                    {{ item.estimated_delivery }}
                  </td>
                  <td class="px-6 py-1">
                    {{ item.delivery_date }}
                  </td>
                  <td class="px-6 py-1">
                    {{ item.delivered_date }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- edit status modal -->
  <div
    v-if="showStatusModal"
    class="z-40 fixed top-0 left-0 h-full w-full flex justify-center items-center"
  >
    <form
      @submit.prevent="handleEditStatusOrder"
      class="w-72 bg-gray-200 p-5 rounded-md shadow"
    >
      <div v-if="barangayname.length > 0">
        <p class="text-sm">Customer Information:</p>
        <p class="bg-gray-500/20 rounded-md my-2 p-2">
          Name :{{ editableOrderStatus.username }}
        </p>
        <p class="bg-gray-500/20 rounded-md my-2 p-2">
          Contact no: {{ editableOrderStatus.contact_number }}
        </p>
        <p
          class="bg-gray-500/20 rounded-md my-2 p-2"
          v-for="barangay in barangayname"
          :key="barangay.barangay_id"
        >
          Address: {{ barangay.name }},{{ editableOrderStatus.address }}
        </p>
      </div>
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
      <div v-if="selectValue === 'pending' || selectValue === 'confirmed'">
        <h1 class="text-lg font-semibold text-blue-400 mt-4">
          Estimated Delivery Date:
        </h1>
        <input
          type="date"
          v-model="estimatedDelivery"
          class="w-full p-2 rounded-md border-gray-300 shadow-sm"
        />
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
  <div v-if="riderModal">
    <div
      class="z-50 fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
        <h1 class="font-semibold text-lg text-gray-800 mb-4">Select Rider</h1>
        <div class="overflow-x-auto" v-if="Riders.length > 0">
          <table class="w-full text-left rounded-lg overflow-hidden">
            <thead class="bg-blue-500 text-white">
              <tr>
                <th class="px-4 py-2">Rider Name</th>
                <th class="px-4 py-2">Contact number</th>
                <th class="px-4 py-2">Load</th>
                <th class="px-4 py-2">Action</th>
              </tr>
            </thead>
            <tbody
              class="bg-white divide-y divide-gray-300"
              v-for="(rider, index) in Riders"
              :key="index"
            >
              <tr class="hover:bg-gray-100">
                <td class="px-4 py-2">{{ rider.rider_name }}</td>
                <td class="px-4 py-2">{{ rider.rider_contact_number }}</td>
                <td class="px-4 py-2">
                  <span class="text-blue-500 bg-blue-50 p-2 rounded-full">{{
                    rider.order_count
                  }}</span>
                </td>
                <td class="px-4 py-2">
                  <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline"
                    @click="insertRider(rider.rider_id)"
                  >
                    Assign
                  </button>
                </td>
              </tr>
              <!-- Repeat <tr> for more riders as needed -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
// YourComponent.vue <script> part
import { ref, onMounted } from "vue";
import { Icon } from "@iconify/vue";
import axios from "axios";
import { userLogin, getUserFromLocalStorage } from "@/scripts/Seller"; // Adjust the path as necessary
import moment from "moment-timezone";
import { API_URL } from "@/config";
export default {
  components: {
    Icon,
  },
  setup() {
    const url = API_URL;
    const refreshPage = () => {
      location.reload(true);
    };

    const selectedStatus = ref("");

    const orders = ref([]);

    const selectValue = ref("");

    let estimatedDelivery = ref("");

    const showStatusModal = ref(false);

    const editableOrderStatus = ref([]);

    const userOrderName = ref(""); // constaining  the name of the order that is being edited in status modal

    let orderIdToEdit = ref(null);

    const temp_orders = ref([]); // pass the id to this

    const searchQuery = ref("");

    const Riders = ref([]);

    const riderModal = ref(false);

    const fetchRiders = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=fetchAllriders`
        );
        // Assuming you might want to do something with the response here
        Riders.value = response.data;
        console.log("riders", response.data);
      } catch (error) {
        console.error("Error getting riders:", error);
      }
    };

    const filterBySearch = () => {
      if (!searchQuery.value) {
        fetchOrders(); // Fetch all orders if no status is selected or reset to default
      } else {
        // Filter directly if there's a selected status
        orders.value = orders.value.filter(
          (order) => order.order_number === searchQuery.value
        );
      }
    };

    const filteredOrders = () => {
      if (temp_orders.value.length === 0) {
        temp_orders.value = orders.value;
      } else {
        orders.value = temp_orders.value;
      }
      if (!selectedStatus.value) {
        fetchOrders(); // Fetch all orders if no status is selected or reset to default
      } else {
        // Filter directly if there's a selected status
        orders.value = orders.value.filter(
          (order) => order.status === selectedStatus.value
        );
      }
    };

    const options = ref([]);

    const updateOptions = () => {
      if (selectValue.value === "ready_to_pickup") {
        // Only show 'Out for delivery' and 'Delivered' when 'out_for_delivery' is selected
        options.value = [
          { value: "ready_to_pickup", text: "Ready to Pickup" },
          { value: "reserved_for_rider", text: "Reserved for Rider" },
        ];
      }
    };

    const barangayname = ref([]);
    const editStatus = async (orderId) => {
      orderIdToEdit.value = orderId;
      const orderToEdit = orders.value.find(
        (order) => order.order_detail_id === orderId
      );
      if (orderToEdit) {
        editableOrderStatus.value = orderToEdit; // Direct assignment without spreading
        userOrderName.value = editableOrderStatus.value.username;
        selectValue.value = editableOrderStatus.value.status;
        updateOptions();
        if (editableOrderStatus.value.status == "ready_to_pickup") {
          showStatusModal.value = true;
        }
        console.log("info", editableOrderStatus.value);
        try {
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=barangay`,
            {
              id: orderToEdit.barangay_id,
            }
          );
          // Assuming you might want to do something with the response here
          barangayname.value = response.data;
          console.log(response.data);
        } catch (error) {
          console.error("Error getting barangay:", error);
        }
      }
    };
    const closeEditStatusModal = () => {
      showStatusModal.value = false;
    };

    // request a axios to update the status for an order
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
      showStatusModal.value = false;
      if (selectValue.value === "reserved_for_rider") {
        riderModal.value = true;
        console.log("ID ", orderIdToEdit.value);
      }
    };

    const insertRider = async (ID) => {
      // Show confirmation dialog
      const confirmAction = confirm(
        "Are you sure you want to assign this rider to the order?"
      );

      if (confirmAction) {
        // User clicked OK
        console.log("rider ID: ", ID);
        console.log("order ID: ", orderIdToEdit.value);

        try {
          const response = await axios.put(
            `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=insertRider`,
            {
              id: orderIdToEdit.value,
              rider_id: ID,
            }
          );
          // Assuming you might want to do something with the response here
          console.log(response.data);

          // Optionally alert the user that the operation was successful
          alert("Rider has been successfully assigned!");
        } catch (error) {
          console.error("Error editing status:", error);
          alert("Failed to assign the rider."); // Notify user about the error
        }
      } else {
        // User clicked Cancel
        console.log("Operation canceled by the user.");
      }
      refreshPage();
    };

    // Now userLogin is directly accessible here  , and it's reactive

    const fetchOrders = async () => {
      console.log("seller ", userLogin.value.store_id);
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=getOrdersAdmin`
        );
        orders.value = response.data;
        console.log("orders: ", orders.value);
      } catch (error) {
        console.error("Error fetching orders:", error);
      }
    };

    const editData = (id) => {
      console.log(id);
    };

    onMounted(() => {
      updateOptions();
      getUserFromLocalStorage();
      fetchOrders();
      fetchRiders();
    });

    return {
      insertRider,
      riderModal,
      Riders,
      barangayname,
      orders,
      editData,
      selectValue,
      selectedStatus,
      filteredOrders,
      temp_orders,
      searchQuery,
      filterBySearch,
      selectValue,

      editStatus,
      showStatusModal,
      closeEditStatusModal,
      handleEditStatusOrder,
      userOrderName,
      editableOrderStatus,
      estimatedDelivery,
      updateOptions,
      options,
    };
  },
};
</script>
