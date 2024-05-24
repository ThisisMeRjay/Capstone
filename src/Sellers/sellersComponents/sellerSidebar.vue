<template>
  <div class="bg-gray-100/10 shadow h-screen w-60">
    <div class="h-full">
      <div class="py-3 px-3 font-semibold text-gray-700">
        <img
          :src="'data:image/png;base64,' + logo"
          class="w-12 h-12 rounded-full mr-2"
          :alt="userLogin.store_name"
        />
      </div>
      <div class="px-2">
        <hr />
        <h1 class="font-semibold text-gray-500 py-3">Main Menu</h1>
        <hr />
        <div>
          <RouterLink to="/seller_products">
            <div
              :class="{
                ' text-slate-700 bg-sky-500/25 shadow font-medium ':
                  $route.name === 'seller_products',
                'text-slate-500 bg-sky-100/10 font-normal':
                  $route.name !== 'seller_products',
                ' font-semibold text-base': true,
              }"
              class="px-2 py-1 my-2 flex gap-2 justify-start rounded-md items-center"
            >
              <Icon icon="akar-icons:home" class="text-xl" />
              Dashboard
            </div>
          </RouterLink>
          <RouterLink to="/seller_order">
            <div
              :class="{
                ' text-slate-700 bg-sky-500/25 shadow font-medium':
                  $route.name === 'seller_order',
                'text-slate-500 bg-sky-100/10 font-normal':
                  $route.name !== 'seller_order',
                ' font-semibold text-base': true,
              }"
              class="px-2 py-1 my-2 flex gap-2 justify-start rounded-md items-center"
            >
              <Icon icon="bi:cart" class="text-xl" />
              Order
            </div>
          </RouterLink>
          <div>
            <hr />
            <h1 class="font-semibold text-gray-500 py-3">Products</h1>
            <hr />
          </div>
          <RouterLink to="/seller_Add_products">
            <div
              :class="{
                ' text-slate-700 bg-sky-500/25 shadow font-medium':
                  $route.name === 'seller_Add_products',
                'text-slate-500 bg-sky-100/10 font-normal':
                  $route.name !== 'seller_Add_products',
                ' font-semibold text-base': true,
              }"
              class="px-2 py-1 my-2 flex gap-2 justify-start rounded-md items-center"
            >
              <Icon icon="gala:add" class="text-xl" />

              Add Products
            </div>
          </RouterLink>
          <RouterLink to="/seller_product_list">
            <div
              :class="{
                ' text-slate-700 bg-sky-500/25 shadow font-medium':
                  $route.name === 'seller_product_list',
                'text-slate-500 bg-sky-100/10 font-normal':
                  $route.name !== 'seller_product_list',
                ' font-semibold text-base': true,
              }"
              class="px-2 py-1 my-2 flex gap-2 justify-start rounded-md items-center"
            >
              <Icon icon="cil:list" class="text-xl" />
              Product List
            </div>
          </RouterLink>
        </div>
      </div>
    </div>
    <div class="relative w-full my-2">
      <div class="absolute bottom-0 bg-slate-50 w-full z-10 px-2">
        <hr />
        <h1 class="font-semibold text-gray-500 py-3">Settings</h1>
        <hr />
        <div class="my-1">
          <button
            class="flex gap-3 justify-start items-center font-semibold hover:bg-slate-400/20 rounded-md text-slate-700 w-full py-2"
            @click="ShowProfile()"
          >
            <div>
              <img
                :src="'data:image/png;base64,' + logo"
                class="w-12 h-12 rounded-full mr-2"
                :alt="userLogin.store_name"
              />
            </div>
            {{ userLogin.store_name }}
          </button>
        </div>

        <div class="mt-1 mb-3">
          <button
            @click="logout"
            class="flex justify-start items-center font-semibold hover:bg-slate-400/20 rounded-md text-slate-700 w-full py-2"
          >
            <Icon icon="solar:logout-line-duotone" class="text-2xl" />
            Logout
          </button>
        </div>
      </div>
    </div>
    <!-- Profile Modal -->
    <div
      v-if="ShowProfileModal"
      class="fixed inset-0 z-50 flex items-center justify-center"
    >
      <div class="absolute inset-0 bg-black opacity-50"></div>
      <div
        class="bg-white p-4 sm:max-w-lg max-w-80 mx-auto rounded-lg shadow-xl z-10"
      >
        <div class="flex justify-between items-start">
          <!-- Close button -->
          <button
            @click="ShowProfileModal = false"
            class="text-black hover:text-gray-700 rounded-full px-3 py-1 bg-slate-300"
          >
            <span class="text-lg">&times;</span>
          </button>
          <!-- Edit button/icon -->
          <button
            @click="toggleEdit"
            class="text-black hover:text-gray-700 rounded px-2 py-1"
          >
            <span
              v-if="isEditing"
              class="text-md hover:text-blue-500 cursor-pointer"
              >Back</span
            >
            <span v-else class="text-md hover:text-blue-500 cursor-pointer"
              >Edit</span
            >
            <!-- Use an icon here if preferred -->
          </button>
        </div>
        <div class="text-center mt-4 m-10">
          <p class="font-semibold text-lg mb-4">Profile Settings</p>
          <div v-if="isEditing" class="space-y-4">
            <!-- Image Upload -->
            <div class="py-2">
              <input
                id="userprofile"
                type="file"
                @change="handleImageChange"
                class="hidden"
                ref="fileInput"
              />
              <!-- Parent div for relative positioning -->
              <div class="mb-6 flex justify-center">
                <div
                  class="relative inline-block"
                  style="width: 60px; height: 60px"
                >
                  <img
                    v-if="showuserprofile"
                    :src="logo"
                    class="object-cover rounded-full shadow"
                    :alt="userLogin.store_name"
                    style="width: 100%; height: 100%"
                    @click="triggerFileInput"
                  />
                  <img
                    v-else
                    :src="'data:image/png;base64,' + logo"
                    class="object-cover rounded-full shadow"
                    :alt="userLogin.store_name"
                    style="width: 100%; height: 100%"
                    @click="triggerFileInput"
                  />
                  <!-- Iconify edit icon positioned absolutely within the relative parent -->
                  <div
                    class="absolute bottom-0 right-0 bg-gray-300 rounded-full p-1 cursor-pointer transform translate-x-1/2 -translate-y-1/2"
                    @click="triggerFileInput"
                  >
                    <Icon icon="lucide:edit" />
                  </div>
                </div>
              </div>
            </div>
            <div class="relative">
              <div class="flex justify-end gap-5 items-center">
                <label for="username" class="">Name:</label>
                <input
                  type="text"
                  id="name"
                  v-model="userLogin.store_name"
                  placeholder="Name"
                  required
                  :class="[
                    'border',
                    'w-full',
                    'p-2',
                    'rounded-md',
                    'my-1',
                    'bg-gray-100',
                    errorMessage.nameErr &&
                    userLogin.store_name.lenght > 0
                      ? 'border-red-500'
                      : userLogin.store_name.length > 0
                      ? 'border-green-500'
                      : 'border-gray-300',
                  ]"
                />
              </div>
              <p
                class="px-3 py-1 rounded-md text-red-500"
                v-if="errorMessage.nameErr && userLogin.store_name.length > 0"
              >
                {{ errorMessage.nameErr }}
              </p>
            </div>

            <div class="relative">
              <div class="flex justify-end gap-5 items-center">
                <label for="username" class="">Contact no:</label>
                <div
                  class="flex border w-full p-2 rounded-md my-1 bg-gray-100 items-center"
                >
                  <span class="bg-gray-200 px-2">+63</span>
                  <input
                    type="tel"
                    id="number"
                    v-model="userLogin.store_contact_number"
                    placeholder="8123456789 or 9123456789"
                    required
                    :class="[
                      'border',
                      'w-full',
                      'p-2',
                      'rounded-md',
                      'my-1',
                      'bg-gray-100',
                      errorMessage.contactNumberErr &&
                      userLogin.store_contact_number.length > 0
                        ? 'border-red-500'
                        : userLogin.store_contact_number.length > 0
                        ? 'border-green-500'
                        : 'border-gray-300',
                    ]"
                  />
                </div>
              </div>
              <p
                v-if="
                  errorMessage.contactNumberErr &&
                  userLogin.store_contact_number.length > 0
                "
                class="text-red-500"
              >
                {{ errorMessage.contactNumberErr }}
              </p>
            </div>

            <div class="text-right mt-4">
              <button
                @click="saveProfile"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                Save
              </button>
            </div>
          </div>

          <div v-else class="space-y-4">
            <!-- Image Upload -->
            <div class="py-2">
              <!-- Display the selected or default image -->
              <div class="mb-6 flex justify-center">
                <img
                  v-if="logo"
                  :src="'data:image/png;base64,' + logo"
                  class="object-cover rounded-full shadow"
                  :alt="userLogin.store_name"
                  style="width: 60px; height: 60px"
                />
                <img
                  v-else
                  src="../assets/profile.jpg"
                  class="object-cover rounded-full shadow"
                  :alt="userLogin.store_name"
                  style="width: 60px; height: 60px"
                />
              </div>
            </div>
            <div class="flex items-center justify-between">
              <span class="mr-2">Name:</span>
              <p class="border-2 rounded-lg border-gray-300 p-2 w-3/4">
                {{ userLogin.store_name }}
              </p>
            </div>

            <div class="flex items-center justify-between">
              <span class="mr-2">Contact No:</span>
              <p class="border-2 rounded-lg border-gray-300 p-2 w-3/4">
                {{ userLogin.store_contact_number }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ref, onMounted, computed, reactive } from "vue";
import { Icon } from "@iconify/vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { API_URL } from "@/config";
export default {
  components: {
    Icon,
  },
  setup() {
    const url = API_URL;

    const router = useRouter();
    const logout = () => {
      if (confirm("Are you sure you want to logout?")) {
        localStorage.removeItem("seller");
        router.push("/seller_index");
      } else {
        console.log("Logout canceled or dialog closed.");
      }
    };
    var userLogin = ref([]);
    const getUserFromLocalStorage = () => {
      const userData = localStorage.getItem("seller");
      if (userData) {
        userLogin.value = JSON.parse(userData);
      } else {
        router.push("/seller_index");
      }
      console.log(userLogin.value);
      return null;
    };

    getUserFromLocalStorage();

    const logo = ref("");
    const getUserprofile = async () => {
      try {
        console.log("id", userLogin.value.store_id);
        const res = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=getLogo`,
          {
            store_id: userLogin.value.store_id,
          }
        );
        logo.value = res.data.logo;
        console.log("profile: ", res.data);
      } catch (err) {
        console.log(err);
      }
    };

    onMounted(getUserprofile);

    const ShowProfileModal = ref(false);
    const ShowProfile = () => {
      ShowProfileModal.value = !ShowProfileModal.value;
      getUserprofile();
    };

    const isEditing = ref(false);
    const toggleEdit = () => {
      isEditing.value = !isEditing.value;
    };

    const showuserprofile = ref(false);

    const handleImageChange = (event) => {
      const file = event.target.files[0];
      showuserprofile.value = true;
      if (!file) {
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        logo.value = e.target.result;
      };
      reader.readAsDataURL(file);
    };
    const fileInput = ref(null);
    const triggerFileInput = () => {
      // Programmatically click the file input
      if (fileInput.value) {
        fileInput.value.click();
      }
    };

    const errorMessage = reactive({
      nameErr: null,
      contactNumberErr: null,
    });

    const nameValidation = computed(() => {
      const pattern = /^[\p{L}'\- ]+$/u;
      if (!pattern.test(userLogin.value.store_name.trim())) {
        return "Please enter a valid name.";
      }
      return null;
    });

    const contactNumberValidation = computed(() => {
      // This pattern checks for numbers starting with '8' or '9' after the '+63' prefix and ensures they are 10 digits in total.
      const pattern = /^[89]\d{9}$/;
      if (!pattern.test(userLogin.value.store_contact_number)) {
        return "Contact number must start with '8' or '9' after the '+63' prefix and be exactly 10 digits long.";
      }
      return null;
    });

    const saveProfile = async () => {
      // Perform a final validation check on form submission
      errorMessage.nameErr = nameValidation.value;
      errorMessage.contactNumberErr = contactNumberValidation.value;
      if (errorMessage.nameErr || errorMessage.contactNumberErr) {
        console.log(errorMessage.nameErr, errorMessage.contactNumberErr);
        return;
      }
      try {
        const res = await axios.put(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=save`,
          {
            store_id: userLogin.value.store_id,
            store_name: userLogin.value.store_name,
            store_contact_number: userLogin.value.store_contact_number,
            logo: logo.value,
          }
        );
        console.log("edit feedback: ", res.data);
        // Assuming you want to show the alert right after logging the response
        alert(
          "Profile updated successfully. Please log in again to see the updates."
        );
      } catch (err) {
        console.error(err);
        alert("Failed to update profile.");
      }
    };

    return {
      contactNumberValidation,
      errorMessage,
      nameValidation,
      saveProfile,
      triggerFileInput,
      fileInput,
      showuserprofile,
      handleImageChange,
      toggleEdit,
      isEditing,
      ShowProfileModal,
      ShowProfile,
      logo,
      getUserprofile,
      logout,
      userLogin,
    };
  },
};
</script>
