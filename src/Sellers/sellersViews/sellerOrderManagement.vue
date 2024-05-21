<template>
  <div class="mx-4">
    <div class="py-3 px-10 font-bold text-2xl text-slate-700">
      <h1>Order</h1>
    </div>
    <hr />
    <div class="flex mt-5 w-full">
      <div>
        <div class="flex gap-3">
          <div
            class="border rounded-md w-60 shadow flex justify-between items-center px-4"
          >
            <input
              type="text"
              v-model="searchQuery"
              @input="filterBySearch"
              placeholder="Search orders"
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
              @change="filterByStatus"
              class="shadow border text-gray-900 outline-none text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-32 px-3 py-2.5"
            >
              <option value="">Default</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="processing">Processing</option>
              <option value="ready_to_pickup">Ready to Pick Up</option>
              <option value="ready_to_pickup">Reserved for Rider</option>
              <option value="pick_up">Pick Up</option>
              <option value="out_for_delivery">Out for Delivery</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
              <option value="delayed">Delayed</option>
              <option value="return_in_progress">Return in Progress</option>
              <option value="return_completed">Return Completed</option>
              <!-- <option value="return_requested">Return Requested</option>
              <option value="return_declined">Return Declined</option>
              <option value="return_approved">Return Approved</option> -->
              <option value="closed">Closed</option>
            </select>
          </form>
        </div>

        <div class="my-5 w-full">
          <div
            class="relative max-w-[1200px] max-h-[570px] overflow-x-auto shadow-md rounded-md"
          >
            <table
              class="min-w-full text-sm text-left rtl:text-right text-gray-900 rounded-md"
            >
              <thead
                class="text-xs text-slate-800 bg-slate-100 uppercase rounded-md sticky top-0 z-40"
              >
                <tr
                  class="text-center bg-gray-100/10 border-b border-gray-600/50"
                >
                  <th scope="col" class="px-6 py-2 sticky left-0 bg-gray-100">
                    Order Number
                  </th>
                  <th scope="col" class="px-6 py-2">Product name</th>
                  <th scope="col" class="px-6 py-2">STATUS</th>
                  <th scope="col" class="px-6 py-2">QUANTITY</th>
                  <th scope="col" class="px-6 py-2">CUSTOMER NAME</th>
                  <th scope="col" class="px-6 py-2">PRICE</th>
                  <th scope="col" class="px-6 py-2">PAYMENT METHOD</th>
                  <th scope="col" class="px-6 py-2">ORDER DATE</th>
                  <th scope="col" class="px-6 py-2">ESTIMATED DELIVERY</th>
                  <th scope="col" class="px-6 py-2">date delivered</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <tr
                  v-for="(item, index) in paginatedOrders"
                  :key="item.id"
                  class="bg-gray-100/10 border-b border-gray-600/50"
                >
                  <td class="px-6 py-1 sticky left-0 top-0 bg-slate-50 z-10">
                    {{ item.order_number }}
                  </td>
                  <td class="px-6 py-1">{{ item.product_name }}</td>
                  <td class="px-6 py-1">
                    <div
                      @click="editStatus(item.order_detail_id)"
                      class="shadow px-3 py-1 text-center rounded-full flex justify-center gap-3 cursor-pointer hover:bg-slate-200 transition"
                      :class="{
                        'text-orange-500 bg-orange-300/10':
                          item.status === 'pending',
                        'text-gray-500 bg-yellow-300/10':
                          item.status === 'confirmed',
                        'text-gray-700 bg-gray-200/10':
                          item.status === 'processing',
                        'text-yellow-600 bg-yellow-200/10':
                          item.status === 'ready_to_pickup',
                        'text-blue-500 bg-blue-300/10':
                          item.status === 'out_for_delivery',
                        'text-green-500 bg-green-300/10':
                          item.status === 'delivered',
                        'text-red-500 bg-red-300/10':
                          item.status === 'cancelled',
                        'text-purple-500 bg-purple-300/10':
                          item.status === 'delayed',
                        'text-pink-500 bg-pink-300/10':
                          item.status === 'return_in_progress',
                        'text-teal-500 bg-teal-300/10':
                          item.status === 'return_completed',
                        'text-red-600 bg-red-200/10':
                          item.status === 'return_declined',
                        'text-blue-400 bg-blue-200/10':
                          item.status === 'return_requested',
                        'text-blue-600 bg-blue-300/10':
                          item.status === 'return_approved',
                        'text-gray-500 bg-gray-300/10':
                          item.status === 'closed',
                      }"
                    >
                      <div>
                        {{ item.status }}
                      </div>
                      <div>
                        <Icon
                          icon="material-symbols:edit"
                          class="text-lg text-green-500"
                        />
                      </div>
                    </div>
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
                    {{ item.date_purchased }}
                  </td>
                  <td class="px-6 py-1">
                    {{ item.estimated_delivery }}
                  </td>
                  <td class="px-6 py-1">
                    {{ item.delivered_date }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="flex justify-center mt-4">
            <nav aria-label="Pagination">
              <ul class="flex list-none p-0">
                <li class="mt-2">
                  <a
                    href="#"
                    @click.prevent="currentPage = 1"
                    :class="{
                      'px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700':
                        currentPage !== 1,
                      'px-3 py-2 ml-0 leading-tight text-blue-600 bg-blue-50 border border-gray-300 rounded-l-lg cursor-default':
                        currentPage === 1,
                    }"
                    >First</a
                  >
                </li>
                <li
                  v-for="page in pages"
                  :key="page"
                  :class="{
                    'px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700':
                      page !== currentPage,
                    'px-3 py-2 leading-tight text-blue-600 bg-blue-50 border border-gray-300 cursor-default':
                      page === currentPage,
                  }"
                >
                  <a href="#" @click.prevent="currentPage = page">{{ page }}</a>
                </li>
                <li class="mt-2">
                  <a
                    href="#"
                    @click.prevent="currentPage = pages.length"
                    :class="{
                      'px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700':
                        currentPage !== pages.length,
                      'px-3 py-2 leading-tight text-blue-600 bg-blue-50 border border-gray-300 rounded-r-lg cursor-default':
                        currentPage === pages.length,
                    }"
                    >Last</a
                  >
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- edit status modal -->
  <div
    v-if="showStatusModal"
    class="z-50 fixed top-0 left-0 h-full w-full flex justify-center items-center"
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
</template>
<script>
import { ref, onMounted, computed, watch } from "vue";
import { Icon } from "@iconify/vue";
import axios from "axios";
import { userLogin, getUserFromLocalStorage } from "@/scripts/Seller";
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
    const estimatedDelivery = ref("");
    const showStatusModal = ref(false);
    const editableOrderStatus = ref([]);
    const userOrderName = ref("");
    const orderIdToEdit = ref(null);
    const temp_orders = ref([]);
    const searchQuery = ref("");
    const paginatedOrders = ref([]);
    const currentPage = ref(1);
    const itemsPerPage = 10;
    const filteredOrders = ref([]);
    const searchedOrders = ref([]);

    const updatePaginatedOrders = () => {
      const startIndex = (currentPage.value - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      paginatedOrders.value = searchedOrders.value.slice(startIndex, endIndex);
      console.log("Paginated Orders:", paginatedOrders.value);
    };

    const filterBySearch = () => {
      const searchString = searchQuery.value.toLowerCase();
      searchedOrders.value = filteredOrders.value.filter((order) => {
        return (
          (order.order_number &&
            order.order_number.toString().includes(searchString)) ||
          (order.product_name &&
            order.product_name.toLowerCase().includes(searchString)) ||
          (order.status && order.status.toLowerCase().includes(searchString)) ||
          (order.quantity &&
            order.quantity.toString().includes(searchString)) ||
          (order.username &&
            order.username.toLowerCase().includes(searchString)) ||
          (order.total_price_products &&
            order.total_price_products.toString().includes(searchString)) ||
          (order.payment_method &&
            order.payment_method.toLowerCase().includes(searchString)) ||
          (order.date_purchased &&
            order.date_purchased.toLowerCase().includes(searchString)) ||
          (order.estimated_delivery &&
            order.estimated_delivery.toLowerCase().includes(searchString)) ||
          (order.delivered_date &&
            order.delivered_date.toLowerCase().includes(searchString))
        );
      });
      currentPage.value = 1;
      updatePaginatedOrders();
    };

    const pages = computed(() => {
      const totalPages = Math.ceil(searchedOrders.value.length / itemsPerPage);
      return Array.from({ length: totalPages }, (_, index) => index + 1);
    });

    watch(currentPage, updatePaginatedOrders);
    watch(filteredOrders, filterBySearch);
    watch(searchQuery, filterBySearch);
    watch(searchedOrders, updatePaginatedOrders);

    const filterByStatus = () => {
      if (!selectedStatus.value) {
        filteredOrders.value = orders.value;
      } else {
        filteredOrders.value = orders.value.filter(
          (order) => order.status === selectedStatus.value
        );
      }
      filterBySearch();
    };

    const barangayname = ref([]);
    const editStatus = async (orderId) => {
      orderIdToEdit.value = orderId;
      const orderToEdit = orders.value.find(
        (order) => order.order_detail_id === orderId
      );
      if (orderToEdit) {
        editableOrderStatus.value = { ...orderToEdit };
        userOrderName.value = editableOrderStatus.value.username;
        selectValue.value = editableOrderStatus.value.status;
        updateOptions();
        if (
          [
            "pending",
            "confirmed",
            "processing",
            "return_requested",
            "return_in_progress",
            "delivered",
          ].includes(editableOrderStatus.value.status)
        ) {
          showStatusModal.value = true;
        } else {
          showStatusModal.value = false;
        }
        try {
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=barangay`,
            { id: orderToEdit.barangay_id }
          );
          barangayname.value = response.data;
        } catch (error) {
          console.error("Error getting barangay:", error);
        }
      }
    };

    const closeEditStatusModal = () => {
      showStatusModal.value = false;
    };

    const handleEditStatusOrder = async () => {
      const DateToupdate = moment()
        .tz("Asia/Manila")
        .format("YYYY-MM-DD HH:mm:ss");

      try {
        await axios.put(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=EditStatus`,
          {
            id: orderIdToEdit.value,
            status: selectValue.value,
            estimated_delivery: estimatedDelivery.value,
            date: DateToupdate,
          }
        );
        refreshPage();
      } catch (error) {
        console.error("Error editing status:", error);
      }
    };

    onMounted(() => {
      updateOptions();
      getUserFromLocalStorage();
      fetchOrders();
    });

    const fetchOrders = async () => {
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=getOrders`,
          { store_id: userLogin.value.store_id }
        );
        orders.value = response.data;
        temp_orders.value = response.data;
        filteredOrders.value = response.data; // Initialize filteredOrders with all orders
        filterByStatus(); // Apply the status filter initially
      } catch (error) {
        console.error("Error fetching orders:", error);
      }
    };

    const editData = (id) => {
      console.log(id);
    };

    const options = ref([]);

    const updateOptions = () => {
      const statusOptions = {
        pending: [
          { value: "pending", text: "Pending" },
          { value: "confirmed", text: "Confirmed" },
          { value: "cancelled", text: "Cancelled" },
        ],
        confirmed: [
          { value: "confirmed", text: "Confirmed" },
          { value: "processing", text: "Processing" },
        ],
        processing: [
          { value: "processing", text: "Processing" },
          { value: "ready_to_pickup", text: "Ready to Pickup" },
        ],
        delivered: [
          { value: "delivered", text: "Delivered" },
          { value: "closed", text: "Closed" },
        ],
        return_in_progress: [
          { value: "return_in_progress", text: "Return in progress" },
          { value: "return_completed", text: "Return completed" },
        ],
      };
      options.value = statusOptions[selectValue.value] || [];
    };

    return {
      searchedOrders,
      filterByStatus,
      filteredOrders,
      paginatedOrders,
      currentPage,
      pages,
      filterBySearch,
      updatePaginatedOrders,
      options,
      barangayname,
      orders,
      editData,
      selectValue,
      selectedStatus,
      temp_orders,
      searchQuery,
      editStatus,
      showStatusModal,
      closeEditStatusModal,
      handleEditStatusOrder,
      userOrderName,
      editableOrderStatus,
      estimatedDelivery,
      updateOptions,
    };
  },
};
</script>
