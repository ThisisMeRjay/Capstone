<template>
  <div class="p-2 border border-slate-900/20 rounded-md">
    <!-- Search input -->
    <div
      class="border rounded-md w-60 shadow flex justify-between items-center px-4 mb-5"
    >
      <input
        type="text"
        v-model="searchQuery"
        @input="filterBySearch"
        placeholder="Search customers"
        class="outline-none placeholder:text-sm placeholder:font-light py-2 pl-2 w-full rounded-full"
      />
      <Icon icon="carbon:search" class="text-xl text-slate-500 my-2 ml-2" />
    </div>

    <!-- Table -->
    <div class="relative overflow-x-auto">
      <table
        class="w-full text-sm text-left rtl:text-right text-gray-900 rounded-md"
      >
        <thead class="text-xs text-sky-100 uppercase bg-sky-900 rounded-md">
          <tr>
            <th scope="col" class="px-6 py-3">Customer Name</th>
            <th scope="col" class="px-6 py-3">Email</th>
            <th scope="col" class="px-6 py-3">Contact Number</th>
            <th scope="col" class="px-6 py-3">City</th>
            <th scope="col" class="px-6 py-3">Barangay</th>
            <th scope="col" class="px-6 py-3">Street</th>
            <th scope="col" class="px-6 py-3">House#/bldg /apt#/Zone#</th>
            <th scope="col" class="px-6 py-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="customer in paginatedCustomers"
            :key="customer.customer_id"
            class="bg-gray-200 border-b border-gray-700"
          >
            <td class="px-6 py-4">{{ customer.username }}</td>
            <td class="px-6 py-4">{{ customer.email }}</td>
            <td class="px-6 py-4">{{ customer.contact_number }}</td>
            <td class="px-6 py-4">{{ customer.address }}</td>
            <td class="px-6 py-4">{{ customer.name }}</td>
            <td class="px-6 py-4">{{ customer.Zone }}</td>
            <td class="px-6 py-4">{{ customer.House_no }}</td>
            <td class="px-6 py-4 flex justify-center gap-2">
              <button @click="openEditModal(customer)">
                <Icon
                  icon="material-symbols:edit-outline"
                  class="text-2xl text-green-500"
                />
              </button>
              <button @click="deleteCustomer(customer.user_id)">
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

    <!-- Pagination -->
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

    <!-- Edit Modal -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg p-6 shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Edit Customer</h2>
        <form @submit.prevent="saveCustomer">
          <!-- Customer Name input -->
          <div class="mb-4">
            <label for="customerName" class="block font-medium mb-1"
              >Customer Name</label
            >
            <input
              type="text"
              id="customerName"
              v-model="editedCustomer.username"
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
          <!-- Customer Email input -->
          <div class="mb-4">
            <label for="customerEmail" class="block font-medium mb-1"
              >Email</label
            >
            <input
              type="email"
              id="customerEmail"
              v-model="editedCustomer.email"
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
          <!-- Customer Contact Number input -->
          <div class="mb-4">
            <label for="customerContact" class="block font-medium mb-1"
              >Contact Number</label
            >
            <input
              type="text"
              id="customerContact"
              v-model="editedCustomer.contact_number"
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
          <!-- Customer Address input -->
          <div class="mb-4">
            <label for="customerAddress" class="block font-medium mb-1"
              >Address</label
            >
            <input
              type="text"
              id="customerAddress"
              v-model="editedCustomer.address"
              class="border border-gray-300 rounded-md px-3 py-2 w-full"
              required
            />
          </div>
          <!-- Barangay Selection -->
          <div class="mb-4">
            <label for="barangay" class="block font-medium mb-1"
              >Barangay</label
            >
            <select
              id="barangay"
              v-model="editedCustomer.barangay_id"
              class="border border-gray-300 rounded-md px-3 py-2 w-full"
            >
              <option value="" disabled>Select Barangay</option>
              <option
                v-for="brgy in barangay"
                :key="brgy.barangay_id"
                :value="brgy.barangay_id"
                required
              >
                {{ brgy.name }}
              </option>
            </select>
          </div>
          <!-- Zone input -->
          <div class="mb-4">
            <label for="zone" class="block font-medium mb-1">Street</label>
            <input
              type="text"
              id="zone"
              v-model="editedCustomer.Zone"
              class="border border-gray-300 rounded-md px-3 py-2 w-full"
            />
          </div>
          <!-- House Number input -->
          <div class="mb-4">
            <label for="houseNumber" class="block font-medium mb-1"
              >House#/bldg /apt#/Zone#</label
            >
            <input
              type="text"
              id="houseNumber"
              v-model="editedCustomer.House_no"
              class="border border-gray-300 rounded-md px-3 py-2 w-full"
            />
          </div>
          <!-- Save and Cancel buttons -->
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
    const customers = ref([]);
    const searchQuery = ref("");
    const searchedCustomers = ref([]);
    const paginatedCustomers = ref([]);
    const currentPage = ref(1);
    const itemsPerPage = 10;
    const showEditModal = ref(false);
    const editedCustomer = ref({
      user_id: null, // Changed from customer_id
      username: "",
      email: "",
      contact_number: "",
      address: "",
      barangay_id: null,
      Zone: "",
      House_no: "",
    });
    const errorMessage = reactive({
      nameErr: null,
      emailErr: null,
      contactNumberErr: null,
    });
    const originalCustomer = ref({
      user_id: null, // Changed from customer_id
      username: "",
      email: "",
      contact_number: "",
    });
    const barangay = ref([]);

    const nameValidation = () => {
      const pattern = /^[\p{L}'\- \p{M}]*$/u;
      if (!pattern.test(editedCustomer.value.username.trim())) {
        return "Please enter a valid name.";
      }
      return null;
    };

    const emailValidation = () => {
      if (!editedCustomer.value.email.endsWith("@gmail.com")) {
        return "Email must be a Gmail address (@gmail.com).";
      }
      return null;
    };

    const checkNameExists = async (name) => {
      try {
        const endpoint = `${url}/Ecommerce/vue-project/src/backend/auth.php?action=checkName`;
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
        const endpoint = `${url}/Ecommerce/vue-project/src/backend/auth.php?action=checkEmail`;
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
      if (!pattern.test(editedCustomer.value.contact_number)) {
        return "Contact number must start with '8' or '9' after the '+63' prefix and be exactly 10 digits long.";
      }
      return null;
    };

    const fetchBarangays = async () => {
      try {
        const res = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/auth.php?action=getBrgy`
        );
        barangay.value = res.data;
        console.log("Barangays fetched:", res.data);
      } catch (err) {
        console.error("Failed to fetch barangays:", err);
      }
    };

    const saveCustomer = async () => {
      console.log("Saving customer:", editedCustomer.value);

      // Check if any field has changed
      const nameChanged =
        editedCustomer.value.username !== originalCustomer.value.username;
      const emailChanged =
        editedCustomer.value.email !== originalCustomer.value.email;
      const addressChanged =
        editedCustomer.value.address !== originalCustomer.value.address;
      const contactNumberChanged =
        editedCustomer.value.contact_number !==
        originalCustomer.value.contact_number;

      // If no fields have changed, proceed to save without validation
      if (
        !nameChanged &&
        !emailChanged &&
        !addressChanged &&
        !contactNumberChanged
      ) {
        try {
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=EditCustomer`,
            {
              user_id: editedCustomer.value.user_id,
              customer_name: editedCustomer.value.username,
              customer_email: editedCustomer.value.email,
              customer_address: editedCustomer.value.address,
              customer_contact_number: editedCustomer.value.contact_number,
              barangay_id: editedCustomer.value.barangay_id,
              Zone: editedCustomer.value.Zone,
              House_no: editedCustomer.value.House_no,
            }
          );

          console.log("Response received:", response.data);

          if (response.data && response.data.success) {
            alert("Customer updated successfully!");
            closeEditModal();
            fetchCustomers();
          } else {
            alert(
              "Failed to update customer: " +
                (response.data.message || "Unknown error")
            );
          }
        } catch (error) {
          console.error("Failed to update customer:", error);
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
        nameError = await checkNameExists(editedCustomer.value.username);
      }
      if (emailChanged) {
        emailError = await checkEmailExists(editedCustomer.value.email);
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
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=EditCustomer`,
          {
            user_id: editedCustomer.value.user_id,
            customer_name: editedCustomer.value.username,
            customer_email: editedCustomer.value.email,
            customer_address: editedCustomer.value.address,
            customer_contact_number: editedCustomer.value.contact_number,
            barangay_id: editedCustomer.value.barangay_id,
            Zone: editedCustomer.value.Zone,
            House_no: editedCustomer.value.House_no,
          }
        );

        console.log("Response received:", response.data);

        if (response.data && response.data.success) {
          alert("Customer updated successfully!");
          closeEditModal();
          fetchCustomers();
        } else {
          alert(
            "Failed to update customer: " +
              (response.data.message || "Unknown error")
          );
        }
      } catch (error) {
        console.error("Failed to update customer:", error);
        alert("Error occurred: " + error.message);
      }
    };

    const fetchCustomers = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=getCustomers`
        );
        customers.value = response.data;
        filterBySearch();
      } catch (error) {
        console.error("Failed to fetch customers:", error);
      }
    };

    const openEditModal = (customerData) => {
      editedCustomer.value = { ...customerData };
      originalCustomer.value = { ...customerData };
      showEditModal.value = true;
      errorMessage.nameErr = null;
      errorMessage.emailErr = null;
      errorMessage.contactNumberErr = null;
      console.log(customerData);
    };

    const closeEditModal = () => {
      showEditModal.value = false;
    };

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

    const updatePaginatedCustomers = () => {
      const startIndex = (currentPage.value - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      paginatedCustomers.value = searchedCustomers.value.slice(
        startIndex,
        endIndex
      );
    };

    const filterBySearch = debounce(() => {
      const searchString = searchQuery.value.toLowerCase();
      searchedCustomers.value = customers.value.filter((customer) => {
        return (
          (customer.username &&
            customer.username.toLowerCase().includes(searchString)) ||
          (customer.email &&
            customer.email.toLowerCase().includes(searchString)) ||
          (customer.address &&
            customer.address.toLowerCase().includes(searchString)) ||
          (customer.contact_number &&
            customer.contact_number.toString().includes(searchString)) ||
          (customer.Zone && customer.Zone.toString().includes(searchString)) ||
          (customer.House_no &&
            customer.House_no.toString().includes(searchString)) ||
          (customer.barangay_id &&
            customer.barangay_id.toString().includes(searchString))
        );
      });
      currentPage.value = 1;
      updatePaginatedCustomers();
    }, 300);

    watch(currentPage, updatePaginatedCustomers);
    watch(searchQuery, filterBySearch);

    onMounted(() => {
      fetchCustomers();
      fetchBarangays();
    });

    const pages = computed(() => {
      return Math.ceil(searchedCustomers.value.length / itemsPerPage);
    });

    return {
      customers,
      searchQuery,
      paginatedCustomers,
      currentPage,
      showEditModal,
      editedCustomer,
      errorMessage,
      openEditModal,
      closeEditModal,
      saveCustomer,
      paginationClass,
      barangay,
      pages,
    };
  },
};
</script>
