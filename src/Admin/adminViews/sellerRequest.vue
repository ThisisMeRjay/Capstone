<template>
  <div class="p-2 border border-slate-900/20 rounded-md">
    <div class="relative overflow-x-auto">
      <table
        class="w-full text-sm text-left rtl:text-right text-gray-900 rounded-md"
      >
        <thead class="text-xs text-sky-100 uppercase bg-sky-900 rounded-md">
          <tr>
            <th scope="col" class="px-6 py-3">Store Name</th>
            <th scope="col" class="px-6 py-3">Email</th>
            <th scope="col" class="px-6 py-3">Address</th>
            <th scope="col" class="px-6 py-3">Contact number</th>
            <th scope="col" class="px-6 py-3 flex justify-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in seller"
            :key="user"
            class="bg-gray-200 border-b border-gray-700"
          >
            <th
              scope="row"
              class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
            >
              {{ user.store_name }}
            </th>
            <td class="px-6 py-4">{{ user.store_email }}</td>
            <td class="px-6 py-4">{{ user.store_address }}</td>
            <td class="px-6 py-4">{{ user.store_contact_number }}</td>
            <td class="px-6 py-4 flex justify-between gap-3 cursor-pointer">
              <button
                class="bg-green-500 font-bold p-2 text-xs text-white shadow rounded hover:bg-green-500/60"
                @click="approved(user.store_id)"
              >
                Approve
              </button>
              <button
                class="bg-red-500 font-bold p-2 text-xs text-white shadow rounded hover:bg-red-500/60"
                @click="rejected(user.store_id)"
              >
                Reject
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
    const seller = ref([]);
    const deleteSeller = (store_id) => {
      console.log("delete store: ", store_id);
    };
    4;
    const fetchSeller = async () => {
      try {
        // Assuming `get` is a predefined function that handles the fetching logic
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=getSellerRequest`
        );
        console.log("Response received:", response.data);
        seller.value = response.data; // Returning the response to handle it where this function is called
      } catch (error) {
        // Handle errors that occur during the `get` call
        console.error("Failed to fetch customers:", error);
        throw error; // Optional: re-throw the error if you want to handle it further up the call stack
      }
    };
    onMounted(fetchSeller);
    const approved = async (ID) => {
      console.log("Store ID to edit:", ID);
      const newStatus = 2;

      // Confirm before proceeding
      if (window.confirm("Are you sure you want to approve this item?")) {
        try {
          // Execute the PUT request to the server
          const response = await axios.put(
            `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=UpdateStatus`,
            {
              store_id: ID,
              status: newStatus,
            }
          );

          console.log("Response received:", response.data);

          // Check the success status from the response data
          if (response.data && response.data.success) {
            alert("Seller approved successfully!");
            window.location.reload(); // Reload the page to reflect changes
          } else {
            alert(
              "Failed to approve: " + (response.data.message || "Unknown error")
            );
          }
        } catch (error) {
          console.error("Failed to approve:", error);
          alert("Error occurred: " + error.message);
        }
      } else {
        // User cancelled the confirmation
        console.log("Error");
      }
    };

    const rejected = async (ID) => {
      console.log("Store ID to edit:", ID);
      const newStatus = 3;

      // Confirm before proceeding
      if (window.confirm("Are you sure you want to reject this item?")) {
        try {
          // Execute the PUT request to the server
          const response = await axios.put(
            `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=UpdateStatus`,
            {
              store_id: ID,
              status: newStatus,
            }
          );

          console.log("Response received:", response.data);

          // Check the success status from the response data
          if (response.data && response.data.success) {
            alert("Seller rejected successfully!");
            window.location.reload(); // Reload the page to reflect changes
          } else {
            alert(
              "Failed to reject: " + (response.data.message || "Unknown error")
            );
          }
        } catch (error) {
          console.error("Failed to reject:", error);
          alert("Error occurred: " + error.message);
        }
      } else {
        // User cancelled the confirmation
        console.log("Error");
      }
    };
    return {
      rejected,
      approved,
      deleteSeller,
      seller,
    };
  },
};
</script>
