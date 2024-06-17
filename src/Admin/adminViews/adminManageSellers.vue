<template>
  <div class="p-2 border border-slate-900/20 rounded-md">
    <div
      class="border rounded-md w-60 shadow flex justify-between items-center px-4 mb-5"
    >
      <input
        type="text"
        v-model="searchQuery"
        @input="filterBySearch"
        placeholder="Search stores"
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
            <th scope="col" class="px-6 py-3">Store Name</th>
            <th scope="col" class="px-6 py-3">Email</th>
            <th scope="col" class="px-6 py-3">Address</th>
            <th scope="col" class="px-6 py-3">Contact number</th>
            <th scope="col" class="px-6 py-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in paginatedSellers"
            :key="user.store_id"
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
            <td class="px-6 py-4 flex justify-center gap-2">
              <button @click="openEditModal(user)">
                <Icon
                  icon="material-symbols:edit-outline"
                  class="text-2xl text-green-500"
                />
              </button>
              <button @click="deleteSeller(user.store_id)">
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
      <h2 class="text-lg font-semibold mb-4">Edit Seller</h2>
      <form @submit.prevent="saveSeller">
        <div class="mb-4">
          <label for="storeName" class="block font-medium mb-1"
            >Store Name</label
          >
          <input
            type="text"
            id="storeName"
            v-model="editedSeller.store_name"
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
          <label for="storeEmail" class="block font-medium mb-1">Email</label>
          <input
            type="email"
            id="storeEmail"
            v-model="editedSeller.store_email"
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
          <label for="storeAddress" class="block font-medium mb-1"
            >Address</label
          >
          <input
            type="text"
            id="storeAddress"
            v-model="editedSeller.store_address"
            class="border border-gray-300 rounded-md px-3 py-2 w-full"
            required
          />
        </div>
        <div class="mb-4">
          <label for="storeContact" class="block font-medium mb-1"
            >Contact Number</label
          >
          <input
            type="text"
            id="storeContact"
            v-model="editedSeller.store_contact_number"
            class="border border-gray-300 rounded-md px-3 py-2 w-full"
            required
          />
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
    const seller = ref([]);
    const searchQuery = ref("");
    const searchedSellers = ref([]);
    const paginatedSellers = ref([]);
    const currentPage = ref(1);
    const itemsPerPage = 10;
    const showEditModal = ref(false);
    const editedSeller = ref({
      store_id: null,
      store_name: "",
      store_email: "",
      store_address: "",
      store_contact_number: "",
    });
    const errorMessage = reactive({
      nameErr: null,
      emailErr: null,
      contactNumberErr: null,
    });
    const originalSeller = ref({
      rider_id: null,
      store_name: "",
      store_email: "",
      store_contact_number: "",
    });

    const nameValidation = () => {
      const pattern = /^[\p{L}'\- \p{M}]*$/u;
      if (!pattern.test(editedSeller.value.store_name.trim())) {
        return "Please enter a valid name.";
      }
      return null;
    };

    const emailValidation = () => {
      if (!editedSeller.value.store_email.endsWith("@gmail.com")) {
        return "Email must be a Gmail address (@gmail.com).";
      }
      return null;
    };

    const checkNameExists = async (name) => {
      try {
        const endpoint = `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=checkName`;
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
        const endpoint = `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=checkEmail`;
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
      if (!pattern.test(editedSeller.value.store_contact_number)) {
        return "Contact number must start with '8' or '9' after the '+63' prefix and be exactly 10 digits long.";
      }
      return null;
    };

    const pages = computed(() => {
      return Math.ceil(searchedSellers.value.length / itemsPerPage);
    });

    const updatePaginatedSellers = () => {
      const startIndex = (currentPage.value - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      paginatedSellers.value = searchedSellers.value.slice(
        startIndex,
        endIndex
      );
    };

    const filterBySearch = () => {
      const searchString = searchQuery.value.toLowerCase();
      searchedSellers.value = seller.value.filter((user) => {
        return (
          (user.store_name &&
            user.store_name.toLowerCase().includes(searchString)) ||
          (user.store_email &&
            user.store_email.toLowerCase().includes(searchString)) ||
          (user.store_address &&
            user.store_address.toLowerCase().includes(searchString)) ||
          (user.store_contact_number &&
            user.store_contact_number.toString().includes(searchString))
        );
      });
      currentPage.value = 1;
      updatePaginatedSellers();
    };

    watch(currentPage, updatePaginatedSellers);
    watch(searchQuery, filterBySearch);

    const deleteSeller = async (ID) => {
      if (
        window.confirm(
          "Are you sure you want to delete this seller? This action cannot be undone."
        )
      ) {
        try {
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=DeleteSeller`,
            {
              store_id: ID,
            }
          );

          if (response.data && response.data.success) {
            alert("Seller deleted successfully!");
            fetchSeller(); // Fetch sellers again to reflect changes
          } else {
            alert(
              "Failed to delete seller: " +
                (response.data.message || "Unknown error")
            );
          }
        } catch (error) {
          console.error("Failed to delete seller:", error);
          alert("Error occurred: " + error.message);
        }
      }
    };

    const fetchSeller = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=getSeller`
        );
        seller.value = response.data;
        filterBySearch(); // Apply search filter after fetching data
      } catch (error) {
        console.error("Failed to fetch sellers:", error);
      }
    };

    const openEditModal = (sellerData) => {
      editedSeller.value = { ...sellerData };
      originalSeller.value = { ...sellerData };
      showEditModal.value = true;
      errorMessage.nameErr = null;
      errorMessage.emailErr = null;
      errorMessage.contactNumberErr = null;
      console.log("to edit", editedSeller.value);
    };

    const closeEditModal = () => {
      showEditModal.value = false;
    };

    const saveSeller = async () => {
      console.log("Saving seller:", editedSeller.value);

      // Check if any field has changed
      const nameChanged =
        editedSeller.value.store_name !== originalSeller.value.store_name;
      const emailChanged =
        editedSeller.value.store_email !== originalSeller.value.store_email;
      const addressChanged =
        editedSeller.value.store_address !== originalSeller.value.store_address;
      const contactNumberChanged =
        editedSeller.value.store_contact_number !==
        originalSeller.value.store_contact_number;

      // If no fields have changed, proceed to save without validation
      if (
        !nameChanged &&
        !emailChanged &&
        !addressChanged &&
        !contactNumberChanged
      ) {
        try {
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=EditSeller`,
            {
              store_id: editedSeller.value.store_id,
              store_name: editedSeller.value.store_name,
              store_email: editedSeller.value.store_email,
              store_address: editedSeller.value.store_address,
              store_contact_number: editedSeller.value.store_contact_number,
            }
          );

          console.log("Response received:", response.data); // Log the response data

          if (response.data && response.data.success) {
            alert("Seller updated successfully!");
            closeEditModal();
            fetchSeller();
          } else {
            alert(
              "Failed to update seller: " +
                (response.data.message || "Unknown error")
            );
          }
        } catch (error) {
          console.error("Failed to update seller:", error);
          alert("Error occurred: " + error.message);
        }
        return;
      }

      // Perform validation checks if fields have changed
      let nameError = null;
      let emailError = null;
      let addressError = null;
      let contactNumberError = null;

      if (nameChanged) {
        nameError = await checkNameExists(editedSeller.value.store_name);
      }
      if (emailChanged) {
        emailError = await checkEmailExists(editedSeller.value.store_email);
      }
      // Add validation for address if necessary
      // if (addressChanged) {
      //   addressError = addressValidation();
      // }
      if (contactNumberChanged) {
        contactNumberError = contactNumberValidation();
      }

      errorMessage.nameErr = nameError;
      errorMessage.emailErr = emailError;
      // errorMessage.addressErr = addressError; // Uncomment if address validation is added
      errorMessage.contactNumberErr = contactNumberError;

      // If there are validation errors, do not proceed with saving
      if (nameError || emailError || addressError || contactNumberError) {
        return;
      }

      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=EditSeller`,
          {
            store_id: editedSeller.value.store_id,
            store_name: editedSeller.value.store_name,
            store_email: editedSeller.value.store_email,
            store_address: editedSeller.value.store_address,
            store_contact_number: editedSeller.value.store_contact_number,
          }
        );

        console.log("Response received:", response.data); // Log the response data

        if (response.data && response.data.success) {
          alert("Seller updated successfully!");
          closeEditModal();
          fetchSeller();
        } else {
          alert(
            "Failed to update seller: " +
              (response.data.message || "Unknown error")
          );
        }
      } catch (error) {
        console.error("Failed to update seller:", error);
        alert("Error occurred: " + error.message);
      }
    };

    onMounted(fetchSeller);

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
      deleteSeller,
      seller,
      searchQuery,
      filterBySearch,
      searchedSellers,
      paginatedSellers,
      currentPage,
      pages,
      updatePaginatedSellers,
      paginationClass,
      openEditModal,
      closeEditModal,
      showEditModal,
      editedSeller,
      saveSeller,
      errorMessage,
      originalSeller,
    };
  },
};
</script>
