<template>
  <div class="p-2 border border-slate-900/20 rounded-md">
    <div class="relative overflow-x-auto">
      <table
        class="w-full text-sm text-left rtl:text-right text-gray-900 rounded-md"
      >
        <thead class="text-xs text-sky-100 uppercase bg-sky-900 rounded-md">
          <tr>
            <th scope="col" class="px-6 py-3">Customers Name</th>
            <th scope="col" class="px-6 py-3">Email</th>
            <th scope="col" class="px-6 py-3">Contact number</th>
            <th scope="col" class="px-6 py-3">Address</th>
            <th scope="col" class="px-6 py-3">Barangay</th>
            <th scope="col" class="px-6 py-3">Zone</th>
            <th scope="col" class="px-6 py-3">House no.</th>
            <th scope="col" class="px-6 py-3">Delete</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in customer"
            :key="user"
            class="bg-gray-200 border-b border-gray-700"
          >
            <th
              scope="row"
              class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
            >
              {{ user.username }}
            </th>
            <td class="px-6 py-4">{{ user.email }}</td>
            <td class="px-6 py-4">{{ user.contact_number }}</td>
            <td class="px-6 py-4">{{ user.address }}</td>
            <td class="px-6 py-4">{{ user.name }}</td>
            <td class="px-6 py-4">{{ user.Zone }}</td>
            <td class="px-6 py-4">{{ user.House_no }}</td>
            <td class="px-6 py-4 flex justify-center">
              <button @click="deleteCustomer(user.customer_id)">
                <Icon
                  icon="material-symbols-light:delete-sharp"
                  class="text-2xl text-red-500"
                />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script>
import { Icon } from "@iconify/vue";
import { onMounted, ref } from "vue";
import { API_URL } from "@/config";
import axios from "axios";
export default {
  components: {
    Icon,
  },
  setup() {
    const url = API_URL;
    const customer = ref([]);
    const deleteCustomer = (customer_id) => {
      console.log("delete customer: ", customer_id);
    };
    4;
    const fetchCustomer = async () => {
      try {
        // Assuming `get` is a predefined function that handles the fetching logic
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=getCustomers`
        );
        console.log("Response received:", response.data);
        customer.value = response.data; // Returning the response to handle it where this function is called
      } catch (error) {
        // Handle errors that occur during the `get` call
        console.error("Failed to fetch customers:", error);
        throw error; // Optional: re-throw the error if you want to handle it further up the call stack
      }
    };
    onMounted(fetchCustomer);
    return {
      deleteCustomer,
      customer,
    };
  },
};
</script>
