<template>
    <div class="p-2 border border-slate-900/20 rounded-md">
      <div class="relative overflow-x-auto">
        <table
          class="w-full text-sm text-left rtl:text-right text-gray-900 rounded-md"
        >
          <thead class="text-xs text-sky-100 uppercase bg-sky-900 rounded-md">
            <tr>
              <th scope="col" class="px-6 py-3">RIder Name</th>
              <th scope="col" class="px-6 py-3">Email</th>
              <th scope="col" class="px-6 py-3">Contact number</th>
              <th scope="col" class="px-6 py-3">Delete</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(user, index) in seller"
              :key="index "
              class="bg-gray-200 border-b border-gray-700"
            >
              <th
                scope="row"
                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
              >
                {{ user.rider_name }}
              </th>
              <td class="px-6 py-4">{{ user.rider_email }}</td>
              <td class="px-6 py-4">{{ user.rider_contact_number }}</td>
              <td class="px-6 py-4 flex justify-center">
                <button @click="deleteSeller(user.rider_id)">
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
      const seller = ref([]);
      const deleteSeller = async (ID) => {
        console.log("delete store: ", ID);
  
        if (
          window.confirm(
            "Are you sure you want to delete this customer? This action cannot be undone."
          )
        ) {
          try {
            // Execute the DELETE request to the server
            const response = await axios.post(
              `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=DeleteRider`,
              {
                rider_id: ID,
              }
            );
  
            console.log("Response received:", response.data);
  
            // Check the success status from the response data
            if (response.data && response.data.success) {
              alert("Customer deleted successfully!");
              window.location.reload(); // Reload the page to reflect changes
            } else {
              alert(
                "Failed to delete seller: " +
                  (response.data.message || "Unknown error")
              );
            }
          } catch (error) {
            console.error("Failed to delete customer:", error);
            alert("Error occurred: " + error.message);
          }
        } else {
          // User cancelled the confirmation
          console.log("Deletion cancelled by user.");
        }
      };
      const fetchRiders = async () => {
        try {
          // Assuming `get` is a predefined function that handles the fetching logic
          const response = await axios.get(
            `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=fetchAllriders`
          );
          console.log("Response received:", response.data);
          seller.value = response.data; // Returning the response to handle it where this function is called
        } catch (error) {
          // Handle errors that occur during the `get` call
          console.error("Failed to fetch customers:", error);
          throw error; // Optional: re-throw the error if you want to handle it further up the call stack
        }
      };
      onMounted(fetchRiders);
      return {
        deleteSeller,
        seller,
      };
    },
  };
  </script>
  