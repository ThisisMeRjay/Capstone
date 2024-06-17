<template>
  <div class="p-2 border border-slate-900/20 rounded-md">
    <div
      class="border rounded-md w-60 shadow flex justify-between items-center px-4 mb-5"
    >
      <input
        type="text"
        v-model="searchQuery"
        @input="filterBySearch"
        placeholder="Search riders"
        class="outline-none placeholder:text-sm placeholder:font-light py-2 pl-2 w-full rounded-full"
      />
      <Icon icon="carbon:search" class="text-xl text-slate-500 my-2 ml-2" />
    </div>
    <div class="relative overflow-x-auto">
      <table
        class="w-full text-sm text-left rtl:text-right text-gray-900 rounded-md"
      >
        <thead class="text-xs text-sky-100 uppercase bg-sky-900 rounded-md">
          <tr>
            <th scope="col" class="px-6 py-3">Rider Name</th>
            <th scope="col" class="px-6 py-3">Email</th>
            <th scope="col" class="px-6 py-3">Contact number</th>
            <th scope="col" class="px-6 py-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in paginatedRiders"
            :key="user.rider_id"
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
            <td class="px-6 py-4 flex justify-center gap-2">
              <button @click="openEditModal(user)">
                <Icon
                  icon="material-symbols:edit-outline"
                  class="text-2xl text-green-500"
                />
              </button>
              <button @click="deleteRider(user.rider_id)">
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
    <div class="flex justify-center mt-4">
      <nav aria-label="Pagination">
        <ul class="flex list-none p-0">
          <li class="mt-2">
            <a
              href="#"
              @click.prevent="currentPage = 1"
              :class="paginationClass(currentPage === 1, true)"
            >
              First
            </a>
          </li>
          <li
            v-for="page in pages"
            :key="page"
            :class="paginationClass(page === currentPage)"
          >
            <a href="#" @click.prevent="currentPage = page">{{ page }}</a>
          </li>
          <li class="mt-2">
            <a
              href="#"
              @click.prevent="currentPage = pages"
              :class="paginationClass(currentPage === pages, false, true)"
            >
              Last
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>

  <!-- Edit Modal -->
  <div
    v-if="showEditModal"
    class="fixed inset-0 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg p-6 shadow-lg">
      <h2 class="text-lg font-semibold mb-4">Edit Rider</h2>
      <form @submit.prevent="saveRider">
        <div class="mb-4">
          <label for="riderName" class="block font-medium mb-1"
            >Rider Name</label
          >
          <input
            type="text"
            id="riderName"
            v-model="editedRider.rider_name"
            class="border border-gray-300 rounded-md px-3 py-2 w-full"
            required
          />
          <p
            class="px-3 py-1 rounded-md text-red-500"
            v-if="errorMessage.nameErr"
          >
            {{ errorMessage.nameErr }}
          </p>
        </div>
        <div class="mb-4">
          <label for="riderEmail" class="block font-medium mb-1">Email</label>
          <input
            type="email"
            id="riderEmail"
            v-model="editedRider.rider_email"
            class="border border-gray-300 rounded-md px-3 py-2 w-full"
            required
          />
          <p
            class="px-3 py-1 rounded-md text-red-500"
            v-if="errorMessage.emailErr"
          >
            {{ errorMessage.emailErr }}
          </p>
        </div>
        <div class="mb-4">
          <label for="riderContact" class="block font-medium mb-1"
            >Contact Number</label
          >
          <div
            class="flex items-center border border-gray-300 rounded-md px-3 py-2"
          >
            <span class="bg-gray-200 px-2">+63</span>
            <input
              type="text"
              id="riderContact"
              v-model="editedRider.rider_contact_number"
              class="flex-1 border-none outline-none ml-2"
              placeholder="8123456789 or 9123456789"
              required
            />
          </div>
          <p
            class="px-3 py-1 rounded-md text-red-500"
            v-if="errorMessage.contactNumberErr"
          >
            {{ errorMessage.contactNumberErr }}
          </p>
        </div>
        <div class="flex justify-end">
          <button
            type="button"
            class="mr-2 px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300"
            @click="closeEditModal"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
          >
            Save
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Icon } from "@iconify/vue";
import { ref, computed, watch, onMounted, reactive } from "vue";
import axios from "axios";
import { API_URL } from "@/config";
import { debounce } from "lodash";

export default {
  components: {
    Icon,
  },
  setup() {
    const url = API_URL;
    const riders = ref([]);
    const searchQuery = ref("");
    const searchedRiders = ref([]);
    const paginatedRiders = ref([]);
    const currentPage = ref(1);
    const itemsPerPage = 10;
    const showEditModal = ref(false);
    const editedRider = ref({
      rider_id: null,
      rider_name: "",
      rider_email: "",
      rider_contact_number: "",
    });
    const errorMessage = reactive({
      nameErr: null,
      emailErr: null,
      contactNumberErr: null,
    });
    const originalRider = ref({
      rider_id: null,
      rider_name: "",
      rider_email: "",
      rider_contact_number: "",
    });

    const nameValidation = () => {
      const pattern = /^[\p{L}'\- \p{M}]*$/u;
      if (!pattern.test(editedRider.value.rider_name.trim())) {
        return "Please enter a valid name.";
      }
      return null;
    };

    const emailValidation = () => {
      if (!editedRider.value.rider_email.endsWith("@gmail.com")) {
        return "Email must be a Gmail address (@gmail.com).";
      }
      return null;
    };

    const checkNameExists = async (name) => {
      try {
        const endpoint = `${url}/Ecommerce/vue-project/src/backend/rider/riderAuth.php?action=checkName`;
        const response = await axios.post(endpoint, { name });

        if (response.data.exists) {
          return "This name already exists.";
        }
      } catch (error) {
        console.error("Error checking name:", error);
        return "Error occurred while checking name.";
      }
      return nameValidation();
    };

    const checkEmailExists = async (email) => {
      try {
        const endpoint = `${url}/Ecommerce/vue-project/src/backend/rider/riderAuth.php?action=checkEmail`;
        const response = await axios.post(endpoint, { email });

        if (response.data.exists) {
          return "This email is already registered.";
        }
      } catch (error) {
        console.error("Error checking email:", error);
        return "Error occurred while checking email.";
      }
      return emailValidation();
    };

    const contactNumberValidation = () => {
      const pattern = /^[89]\d{9}$/;
      if (!pattern.test(editedRider.value.rider_contact_number)) {
        return "Contact number must start with '8' or '9' after the '+63' prefix and be exactly 10 digits long.";
      }
      return null;
    };

    const pages = computed(() => {
      return Math.ceil(searchedRiders.value.length / itemsPerPage);
    });

    const updatePaginatedRiders = () => {
      const startIndex = (currentPage.value - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      paginatedRiders.value = searchedRiders.value.slice(startIndex, endIndex);
    };

    const filterBySearch = () => {
      const searchString = searchQuery.value.toLowerCase();
      searchedRiders.value = riders.value.filter((user) => {
        return (
          (user.rider_name &&
            user.rider_name.toLowerCase().includes(searchString)) ||
          (user.rider_email &&
            user.rider_email.toLowerCase().includes(searchString)) ||
          (user.rider_contact_number &&
            user.rider_contact_number.toString().includes(searchString))
        );
      });
      currentPage.value = 1;
      updatePaginatedRiders();
    };

    watch(currentPage, updatePaginatedRiders);
    watch(searchQuery, filterBySearch);

    const deleteRider = async (ID) => {
      if (
        window.confirm(
          "Are you sure you want to delete this rider? This action cannot be undone."
        )
      ) {
        try {
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=DeleteRider`,
            {
              rider_id: ID,
            }
          );

          if (response.data && response.data.success) {
            alert("Rider deleted successfully!");
            fetchRiders(); // Fetch riders again to reflect changes
          } else {
            alert(
              "Failed to delete rider: " +
                (response.data.message || "Unknown error")
            );
          }
        } catch (error) {
          console.error("Failed to delete rider:", error);
          alert("Error occurred: " + error.message);
        }
      }
    };

    const fetchRiders = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=fetchAllriders`
        );
        riders.value = response.data;
        filterBySearch(); // Apply search filter after fetching data
      } catch (error) {
        console.error("Failed to fetch riders:", error);
      }
    };

    const openEditModal = (rider) => {
      // Copy current rider details to editedRider for editing
      editedRider.value = { ...rider };
      // Copy current rider details to originalRider for comparison
      originalRider.value = { ...rider };
      showEditModal.value = true;
      errorMessage.nameErr = null;
      errorMessage.emailErr = null;
      errorMessage.contactNumberErr = null;
      console.log("to edit", editedRider.value);
    };

    const closeEditModal = () => {
      showEditModal.value = false;
    };

    const saveRider = async () => {
      console.log("Saving rider:", editedRider.value);

      // Check if any field has changed
      const nameChanged =
        editedRider.value.rider_name !== originalRider.value.rider_name;
      const emailChanged =
        editedRider.value.rider_email !== originalRider.value.rider_email;
      const contactNumberChanged =
        editedRider.value.rider_contact_number !==
        originalRider.value.rider_contact_number;

      // If no fields have changed, proceed to save without validation
      if (!nameChanged && !emailChanged && !contactNumberChanged) {
        try {
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/rider/riderApi.php?action=EditRider`,
            {
              rider_id: editedRider.value.rider_id,
              rider_name: editedRider.value.rider_name,
              rider_email: editedRider.value.rider_email,
              rider_contact_number: editedRider.value.rider_contact_number,
            }
          );

          console.log("Response received:", response.data); // Log the response data

          if (response.data && response.data.success) {
            alert("Rider updated successfully!");
            closeEditModal();
            fetchRiders();
          } else {
            alert(
              "Failed to update rider: " +
                (response.data.message || "Unknown error")
            );
          }
        } catch (error) {
          console.error("Failed to update rider:", error);
          alert("Error occurred: " + error.message);
        }
        return;
      }

      // Perform validation checks if fields have changed
      let nameError = null;
      let emailError = null;
      let contactNumberError = null;

      if (nameChanged) {
        nameError = await checkNameExists(editedRider.value.rider_name);
      }
      if (emailChanged) {
        emailError = await checkEmailExists(editedRider.value.rider_email);
      }
      if (contactNumberChanged) {
        contactNumberError = contactNumberValidation();
      }

      errorMessage.nameErr = nameError;
      errorMessage.emailErr = emailError;
      errorMessage.contactNumberErr = contactNumberError;

      // If there are validation errors, do not proceed with saving
      if (nameError || emailError || contactNumberError) {
        return;
      }

      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/rider/riderApi.php?action=EditRider`,
          {
            rider_id: editedRider.value.rider_id,
            rider_name: editedRider.value.rider_name,
            rider_email: editedRider.value.rider_email,
            rider_contact_number: editedRider.value.rider_contact_number,
          }
        );

        console.log("Response received:", response.data); // Log the response data

        if (response.data && response.data.success) {
          alert("Rider updated successfully!");
          closeEditModal();
          fetchRiders();
        } else {
          alert(
            "Failed to update rider: " +
              (response.data.message || "Unknown error")
          );
        }
      } catch (error) {
        console.error("Failed to update rider:", error);
        alert("Error occurred: " + error.message);
      }
    };

    onMounted(fetchRiders);

    const paginationClass = (isActive, isFirst = false, isLast = false) => {
      let baseClass = "px-3 py-2 leading-tight border border-gray-300";
      if (isFirst) baseClass += " rounded-l-lg";
      if (isLast) baseClass += " rounded-r-lg";
      if (isActive) {
        return `${baseClass} text-blue-600 bg-blue-50 cursor-default`;
      } else {
        return `${baseClass} text-gray-500 bg-white hover:bg-gray-100 hover:text-gray-700`;
      }
    };

    return {
      searchQuery,
      paginatedRiders,
      currentPage,
      pages,
      deleteRider,
      openEditModal,
      closeEditModal,
      showEditModal,
      saveRider,
      paginationClass,
      editedRider,
      errorMessage,
      originalRider,
    };
  },
};
</script>
