<template>
  <div v-if="isVisible">
    <div v-if="showLogin">
      <div
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur overflow-y-auto h-full w-full z-50"
      >
        <div
          class="bg-slate-300 relative top-20 mx-auto border border-slate-900/20 shadow-lg px-3 py-2 w-96 rounded-lg"
        >
          <div class="flex justify-end">
            <div
              class="bg-slate-600/20 rounded-full text-red-500 shadow p-2"
              @click="close"
            >
              <Icon icon="iconamoon:close-bold" />
            </div>
          </div>
          <div class="text-white px-3 py-2 rounded-md mt-3">
            <h1 class="font-bold text-sky-900 text-2xl text-center">Login</h1>
          </div>
          <form @submit.prevent="signIn">
            <div class="p-2 rounded-md shadow-sm mb-2">
              <div class="gap-2 mt-2">
                <p class="font-semibold">
                  Email <span class="text-red-500">*</span>
                </p>
                <input
                  type="email"
                  id="email"
                  v-model="loginEmail"
                  required
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>
              <div class="gap-2">
                <p class="font-semibold">
                  Password <span class="text-red-500">*</span>
                </p>
                <input
                  type="password"
                  id="password"
                  v-model="loginPassword"
                  required
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>
              <div class="my-5">
                <button
                  type="submit"
                  class="bg-sky-900 w-full font-semibold text-lg text-white px-5 py-2 rounded-md"
                >
                  Login
                </button>
                <div class="flex justify-center gap-2 py-2">
                  <p>Don't have account?</p>
                  <span
                    class="text-blue-500 hover:text-blue-700"
                    @click="showRegisterModal"
                    >Sign up
                  </span>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div v-if="showRegister">
      <div
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur overflow-y-auto h-full w-full z-50"
      >
        <div
          class="bg-slate-300 relative top-20 mx-auto border border-slate-900/20 shadow-lg px-3 py-2 w-96 rounded-lg"
        >
          <div class="flex justify-end">
            <button
              class="bg-slate-600/20 rounded-full text-red-500 shadow p-2"
              @click="close"
            >
              <Icon icon="iconamoon:close-bold" />
            </button>
          </div>
          <div class="text-white px-3 py-2 rounded-md mt-3">
            <h1 class="font-bold text-sky-900 text-2xl text-center">
              Register
            </h1>
            <p
              v-if="registerResponseMessage"
              class="text-green-600 px-4 shadow-sm mt-2 py-3 rounded-md bg-green-400/10"
            >
              {{ registerResponseMessage }}
            </p>
          </div>
          <form @submit.prevent="signUp">
            <div class="p-2 rounded-md shadow-sm mb-2">
              <div class="gap-2 mt-2">
                <label for="name" class="font-semibold"
                  >Name <span class="text-red-500">*</span></label
                >
                <input
                  type="text"
                  id="name"
                  v-model="registerName"
                  placeholder="Full Name"
                  required
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>
              <div class="gap-2 mt-2">
                <label for="email" class="font-semibold"
                  >Email <span class="text-red-500">*</span></label
                >
                <input
                  type="email"
                  id="email"
                  v-model="registerEmail"
                  placeholder="Email"
                  required
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>
              <div class="gap-2 mt-2">
                <label for="password" class="font-semibold"
                  >Password <span class="text-red-500">*</span></label
                >
                <input
                  type="password"
                  id="password"
                  v-model="registerPassword"
                  placeholder="Password"
                  required
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>
              <div class="gap-2 mt-2">
                <label for="number" class="font-semibold"
                  >Contact Number <span class="text-red-500">*</span></label
                >
                <input
                  type="tel"
                  id="number"
                  v-model="contactNumber"
                  pattern="[0-9]{11}"
                  placeholder="09123456789"
                  required
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>
              <div class="gap-2 mt-2">
                <label for="address" class="font-semibold"
                  >Address <span class="text-red-500">*</span></label
                >
                <input
                  type="text"
                  id="address"
                  v-model="address"
                  placeholder="Municipality"
                  required
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
                <select
                  id="barangay"
                  class="w-full p-2 rounded-md my-1 border outline-none"
                  v-model="selectedBarangay"
                  required
                >
                  <option value="" disabled selected>Select Barangay</option>
                  <option
                    v-for="brgy in barangay"
                    :key="brgy.barangay_id"
                    :value="brgy.barangay_id"
                  >
                    {{ brgy.name }}
                  </option>
                </select>
              </div>
              <div class="gap-2 mt-2">
                <label for="zone" class="font-semibold"
                  >Zone <span class="text-red-500">*</span></label
                >
                <input
                  type="text"
                  id="zone"
                  v-model="registerZone"
                  placeholder="ex. Zone 7"
                  required
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>
              <div class="gap-2 mt-2">
                <label for="houseno" class="font-semibold"
                  >House no. <span class="text-red-500">*</span></label
                >
                <input
                  type="text"
                  id="houseno"
                  v-model="registerHouseno"
                  placeholder="House no."
                  required
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>
              <div class="my-5">
                <button
                  type="submit"
                  class="bg-sky-900 w-full font-semibold text-lg text-white px-5 py-2 rounded-md"
                >
                  Register
                </button>
                <div class="flex justify-center gap-2 py-2">
                  <p>Already have account?</p>
                  <button
                    class="text-blue-500 hover:text-blue-700"
                    @click="showLoginModal"
                  >
                    Sign In
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { Icon } from "@iconify/vue";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { API_URL } from "@/config";
export default {
  components: {
    Icon,
  },
  props: {
    isVisible: {
      type: Boolean,
      required: true,
    },
  },
  emits: ["update:isVisible", "login-completed"],

  data() {
    return {
      showRegister: false,
      showLogin: true,
    };
  },
  methods: {
    showRegisterModal() {
      this.showRegister = true;
      this.showLogin = false;
    },
    showLoginModal() {
      this.showRegister = false;
      this.showLogin = true;
    },

    close() {
      this.$emit("update:isVisible", false);
    },
  },
  setup(props, { emit }) {
    const url = API_URL;

    const selectedBarangay = ref("");
    const barangay = ref([]);

    const GetBarangays = async () => {
      try {
        const res = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/auth.php?action=getBrgy`
        );
        barangay.value = res.data;
        console.log("barangaysss: ", res.data);
      } catch (err) {
        console.log(err);
      }
    };

    onMounted(GetBarangays);

    const loginEmail = ref("");
    const loginPassword = ref("");
    const router = useRouter();
    let name = ref("");
    const refreshPage = () => {
      location.reload(true);
    };

    const signIn = async () => {
      try {
        const urli = `${url}/Ecommerce/vue-project/src/backend/auth.php?action=login`;
        const res = await axios.post(
          urli,
          {
            email: loginEmail.value,
            password: loginPassword.value,
          },
          { headers: { "Content-Type": "application/json" } }
        );

        name.value = res.data.customer;
        localStorage.setItem("user", JSON.stringify(name.value));
        refreshPage();
        const role = res.data.role;
        if (role === "admin") {
          router.push("/admin_dashboard");
        } else {
          router.push("/home");
          emit("update:isVisible", false);
          emit("login-completed", name.value);
        }
      } catch {
        
      }
    };
    const registerZone = ref("");
    const registerHouseno = ref("");
    const registerEmail = ref("");
    const registerName = ref("");
    const registerPassword = ref("");
    const contactNumber = ref("");
    const customerRole = "customer";
    const address = ref("");
    const registerResponseMessage = ref("");

    const signUp = async () => {
      try {
        console.log("barangay id: ", selectedBarangay.value);
        const urli = `${url}/Ecommerce/vue-project/src/backend/auth.php?action=register`;
        const res = await axios.post(
          urli,
          {
            name: registerName.value,
            email: registerEmail.value,
            password: registerPassword.value,
            contact_number: contactNumber.value,
            role: customerRole,
            address: address.value,
            barangay: selectedBarangay.value,
            zone: registerZone.value,
            house_no: registerHouseno.value,
          },
          { headers: { "Content-Type": "application/json" } }
        );
        registerResponseMessage.value = res.data.message;
      } catch (res) {
        console.log(res.data.success);
      }
      registerName.value = "";
      registerEmail.value = "";
      registerPassword.value = "";
      contactNumber.value = "";
      role.value = "";
    };

    return {
      GetBarangays,
      barangay,
      selectedBarangay,
      customerRole,

      loginEmail,
      loginPassword,
      signIn,

      registerName,
      registerEmail,
      registerPassword,
      contactNumber,
      signUp,
      registerZone,
      registerHouseno,

      registerResponseMessage,
      name,
      address,
    };
  },
};
</script>
