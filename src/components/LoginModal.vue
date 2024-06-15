<template>
  <div v-if="isVisible">
    <div v-if="showLogin">
      <div
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur overflow-y-auto h-full w-full z-50"
      >
        <div
          class="bg-slate-300 relative top-20 mx-auto border border-slate-900/20 shadow-lg px-3 py-2 w-72 sm:w-96 rounded-lg"
        >
          <div class="flex justify-end">
            <div
              class="bg-slate-600/20 rounded-full text-red-500 shadow p-2 cursor-pointer"
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
                  placeholder="email"
                  required
                  :class="[
                    'border',
                    'w-full',
                    'p-2',
                    'rounded-md',
                    'my-1',
                    'bg-gray-100',
                    errorMessage.emailErr && loginEmail.length > 0
                      ? 'border-red-500'
                      : loginEmail.length > 0
                      ? 'border-green-500'
                      : 'border-gray-300',
                  ]"
                />
                <p
                  v-if="errorMessage.emailErr && loginEmail.length > 0"
                  class="text-red-500 pl-3 pb-2"
                >
                  {{ errorMessage.emailErr }}
                </p>
              </div>

              <div class="relative mt-2 gap-2" style="position: relative">
                <label for="password" class="font-semibold">
                  Password <span class="text-red-500">*</span>
                </label>
                <div style="position: relative">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    v-model="loginPassword"
                    placeholder="Password"
                    required
                    class="border w-full p-2 rounded-md my-1 bg-gray-100 pr-10"
                    :class="{
                      'border-red-500': errorMessage.passwordErr,
                      'border-green-500': loginPassword.length > 0,
                    }"
                    style="padding-right: 2.5rem"
                  />
                  <button
                    type="button"
                    class="absolute inset-y-0 right-0 flex items-center password-toggle-button"
                    style="
                      top: 50%;
                      transform: translateY(-50%);
                      right: 0.75rem;
                    "
                  >
                    <icon
                      :icon="showPassword ? 'mdi:eye-off' : 'mdi:eye'"
                      class="text-lg cursor-pointer"
                      @click.stop="toggleShowPassword"
                    />
                  </button>
                </div>
                <p
                  class="px-3 py-1 rounded-md text-red-500"
                  v-if="errorMessage.passwordErr && loginPassword.length > 0"
                >
                  {{ errorMessage.passwordErr }}
                </p>
              </div>

              <div class="my-5">
                <!-- <div class="pt-3">
                  <button
                    type="submit"
                    class="flex w-full justify-center rounded-md bg-sky-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
                  >
                    Register
                  </button>
                </div>
                <div class="flex justify-center items-center gap-2 pt-2">
                  Already have an account?
                  <button
                    class="flex w-full justify-center rounded-md bg-sky-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
                    @click="showLoginModal"
                  >
                    Sign In
                  </button>
                </div> -->
                <button
                  type="submit"
                  class="flex w-full justify-center rounded-md bg-sky-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
                >
                  Login
                </button>
                <div class="flex justify-center gap-2 py-2">
                  <p>Don't have account?</p>
                  <span
                    class="text-blue-500 hover:text-blue-700 cursor-pointer"
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
          class="bg-slate-300 relative top-20 mx-auto border border-slate-900/20 shadow-lg px-3 py-2 w-72 sm:w-96 rounded-lg"
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
            <div class="flex items-center justify-center mt-4 gap-8 relative">
              <button
                @click="setActive('Customer')"
                class="text-black font-normal hover:text-blue-700 relative"
                :class="{ 'font-bold': activeOption === 'Customer' }"
              >
                Customer
                <span
                  v-if="activeOption === 'Customer'"
                  class="ml-2 font-semibold"
                >
                </span>
                <span
                  v-if="activeOption === 'Customer'"
                  class="absolute bottom-0 left-0 w-full h-0.5 bg-slate-600 rounded-full"
                ></span>
              </button>
              <button
                @click="setActive('Store')"
                class="text-black hover:text-blue-700 relative font-normal"
                :class="{ 'font-bold': activeOption === 'Store' }"
              >
                Store/Seller
                <span
                  v-if="activeOption === 'Store'"
                  class="absolute bottom-0 left-0 w-full h-0.5 bg-slate-600 rounded-full"
                ></span>
              </button>
              <button
                @click="setActive('Rider')"
                class="text-black hover:text-blue-700 relative font-normal"
                :class="{ 'font-bold': activeOption === 'Rider' }"
              >
                Rider
                <span
                  v-if="activeOption === 'Rider'"
                  class="absolute bottom-0 left-0 w-full h-0.5 bg-slate-600 rounded-full"
                ></span>
              </button>
            </div>
            <p
              v-if="registerResponseMessage"
              class="text-green-600 px-4 shadow-sm mt-2 py-3 rounded-md bg-green-400/10"
            >
              {{ registerResponseMessage }}
            </p>
          </div>
          <form @submit.prevent="signUp" v-if="activeOption === 'Customer'">
            <div class="rounded-md shadow-sm mb-2">
              <div class="gap-2 mt-2">
                <label for="name" class="font-semibold"
                  >Name <span class="text-red-500">*</span></label
                >
                <input
                  type="text"
                  id="name"
                  v-model="registerName"
                  placeholder="Name"
                  required
                  :class="[
                    'border',
                    'w-full',
                    'p-2',
                    'rounded-md',
                    'my-1',
                    'bg-gray-100',
                    errorMessage.nameErr
                      ? 'border-red-500'
                      : registerName.length > 0
                      ? 'border-green-500'
                      : 'border-gray-300',
                  ]"
                />
                <p
                  class="px-3 py-1 rounded-md text-red-500"
                  v-if="errorMessage.nameErr && registerName.length > 0"
                >
                  {{ errorMessage.nameErr }}
                </p>
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
                  :class="[
                    'border',
                    'w-full',
                    'p-2',
                    'rounded-md',
                    'my-1',
                    'bg-gray-100',
                    errorMessage.emailErr
                      ? 'border-red-500'
                      : registerEmail.length > 0
                      ? 'border-green-500'
                      : 'border-gray-300',
                  ]"
                />
                <p
                  class="px-3 py-1 rounded-md text-red-500"
                  v-if="errorMessage.emailErr && registerEmail.length > 0"
                >
                  {{ errorMessage.emailErr }}
                </p>
              </div>
              <div class="relative mt-2 gap-2" style="position: relative">
                <label for="password" class="font-semibold">
                  Password <span class="text-red-500">*</span>
                </label>
                <div style="position: relative">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    v-model="registerPassword"
                    placeholder="Password"
                    required
                    class="border w-full p-2 rounded-md my-1 bg-gray-100 pr-10"
                    :class="{
                      'border-red-500': errorMessage.passwordErr,
                      'border-green-500': registerPassword.length > 0,
                      'border-gray-300':
                        !errorMessage.passwordErr &&
                        registerPassword.length === 0,
                    }"
                    style="padding-right: 2.5rem"
                  />
                  <button
                    type="button"
                    class="absolute inset-y-0 right-0 flex items-center password-toggle-button"
                    style="
                      top: 50%;
                      transform: translateY(-50%);
                      right: 0.75rem;
                    "
                  >
                    <icon
                      :icon="showPassword ? 'mdi:eye-off' : 'mdi:eye'"
                      class="text-lg cursor-pointer"
                      @click.stop="toggleShowPassword"
                    />
                  </button>
                </div>
                <p
                  class="px-3 py-1 rounded-md text-red-500"
                  v-if="errorMessage.passwordErr && registerPassword.length > 0"
                >
                  {{ errorMessage.passwordErr }}
                </p>
              </div>

              <div class="gap-2 mt-2">
                <label for="number" class="font-semibold">
                  Contact Number <span class="text-red-500">*</span>
                </label>
                <div
                  class="flex border w-full p-2 rounded-md my-1 bg-gray-100 items-center"
                >
                  <span class="bg-gray-200 px-2">+63</span>
                  <input
                    type="tel"
                    id="number"
                    v-model="contactNumber"
                    placeholder="8123456789 or 9123456789"
                    required
                    :class="[
                      'flex-1',
                      'border-none',
                      'outline-none',
                      'bg-gray-100',
                      errorMessage.contactNumberErr && contactNumber.length > 0
                        ? 'border-red-500'
                        : contactNumber.length > 0
                        ? 'border-green-500'
                        : 'border-gray-300',
                    ]"
                  />
                </div>
                <p
                  v-if="
                    errorMessage.contactNumberErr && contactNumber.length > 0
                  "
                  class="text-red-500"
                >
                  {{ errorMessage.contactNumberErr }}
                </p>
              </div>

              <div class="gap-2 mt-2">
                <label for="address" class="font-semibold"
                  >City <span class="text-red-500">*</span></label
                >
                <input
                  type="text"
                  id="address"
                  v-model="address"
                  placeholder="Legazpi City"
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
                <label for="zone" class="font-semibold">Street(Optional)</label>
                <input
                  type="text"
                  id="zone"
                  v-model="registerZone"
                  placeholder="Enter your street"
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>

              <div class="gap-2 mt-2">
                <label for="houseno" class="font-semibold"
                  >House#/bldg/apt#/Zone#(Optional)</label
                >
                <input
                  type="text"
                  id="houseno"
                  v-model="registerHouseno"
                  placeholder="Enter your house#/bldg/apt#/Zone#"
                  class="w-full p-2 rounded-md my-1 bg-gray-100"
                />
              </div>

              <div class="pt-3">
                <button
                  type="submit"
                  class="flex w-full justify-center rounded-md bg-sky-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
                >
                  Register
                </button>
              </div>
              <div class="flex justify-center items-center gap-2 pt-2">
                Already have an account?
                <button
                  class="text-blue-500 hover:text-blue-700"
                  @click="showLoginModal"
                >
                  Sign In
                </button>
              </div>
            </div>
          </form>
          <form
            class="space-y-6 text-sm"
            @submit.prevent="signUpStore"
            v-if="activeOption === 'Store'"
          >
            <div class="gap-2 mt-2">
              <label for="name" class="font-semibold"
                >Name <span class="text-red-500">*</span></label
              >
              <input
                type="text"
                id="name"
                v-model="registerName"
                placeholder="Name"
                required
                :class="[
                  'border',
                  'w-full',
                  'p-2',
                  'rounded-md',
                  'my-1',
                  'bg-gray-100',
                  errorMessage.nameErr
                    ? 'border-red-500'
                    : registerName.length > 0
                    ? 'border-green-500'
                    : 'border-gray-300',
                ]"
              />
              <p
                class="px-3 py-1 rounded-md text-red-500"
                v-if="errorMessage.nameErr && registerName.length > 0"
              >
                {{ errorMessage.nameErr }}
              </p>
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
                :class="[
                  'border',
                  'w-full',
                  'p-2',
                  'rounded-md',
                  'my-1',
                  'bg-gray-100',
                  errorMessage.emailErr
                    ? 'border-red-500'
                    : registerEmail.length > 0
                    ? 'border-green-500'
                    : 'border-gray-300',
                ]"
              />
              <p
                class="px-3 py-1 rounded-md text-red-500"
                v-if="errorMessage.emailErr && registerEmail.length > 0"
              >
                {{ errorMessage.emailErr }}
              </p>
            </div>

            <div class="relative mt-2 gap-2" style="position: relative">
              <label for="password" class="font-semibold">
                Password <span class="text-red-500">*</span>
              </label>
              <div style="position: relative">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  id="password"
                  v-model="registerPassword"
                  placeholder="Password"
                  required
                  class="border w-full p-2 rounded-md my-1 bg-gray-100 pr-10"
                  :class="{
                    'border-red-500': errorMessage.passwordErr,
                    'border-green-500': registerPassword.length > 0,
                    'border-gray-300':
                      !errorMessage.passwordErr &&
                      registerPassword.length === 0,
                  }"
                  style="padding-right: 2.5rem"
                />
                <button
                  type="button"
                  class="absolute inset-y-0 right-0 flex items-center password-toggle-button"
                  style="top: 50%; transform: translateY(-50%); right: 0.75rem"
                >
                  <icon
                    :icon="showPassword ? 'mdi:eye-off' : 'mdi:eye'"
                    class="text-lg cursor-pointer"
                    @click.stop="toggleShowPassword"
                  />
                </button>
              </div>
              <p
                class="px-3 py-1 rounded-md text-red-500"
                v-if="errorMessage.passwordErr && registerPassword.length > 0"
              >
                {{ errorMessage.passwordErr }}
              </p>
            </div>

            <div class="gap-2 mt-2">
              <label for="address" class="font-semibold"
                >Address <span class="text-red-500">*</span></label
              >
              <input
                type="text"
                id="address"
                v-model="registerAddress"
                placeholder="Legazpi City"
                required
                class="w-full p-2 rounded-md my-1 bg-gray-100"
              />
            </div>

            <div class="gap-2 mt-2">
              <label for="number" class="font-semibold">
                Contact Number <span class="text-red-500">*</span>
              </label>
              <div
                class="flex border w-full p-2 rounded-md my-1 bg-gray-100 items-center"
              >
                <span class="bg-gray-200 px-2">+63</span>
                <input
                  type="tel"
                  id="number"
                  v-model="contactNumber"
                  placeholder="8123456789 or 9123456789"
                  required
                  :class="[
                    'flex-1',
                    'border-none',
                    'outline-none',
                    'bg-gray-100',
                    errorMessage.contactNumberErr && contactNumber.length > 0
                      ? 'border-red-500'
                      : contactNumber.length > 0
                      ? 'border-green-500'
                      : 'border-gray-300',
                  ]"
                />
              </div>
              <p
                v-if="errorMessage.contactNumberErr && contactNumber.length > 0"
                class="text-red-500"
              >
                {{ errorMessage.contactNumberErr }}
              </p>
            </div>

            <div>
              <button
                type="submit"
                class="flex w-full justify-center rounded-md bg-sky-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
              >
                Register
              </button>
            </div>
            <div class="flex justify-center items-center gap-2">
              Already have an account?
              <button
                class="text-blue-500 hover:text-blue-700"
                @click="showLoginModal"
              >
                Sign In
              </button>
            </div>
          </form>
          <form
            class="space-y-6 text-sm"
            @submit.prevent="signUpRider"
            v-if="activeOption === 'Rider'"
          >
            <div class="gap-2 mt-2">
              <label for="name" class="font-semibold"
                >Name <span class="text-red-500">*</span></label
              >
              <input
                type="text"
                id="name"
                v-model="registerName"
                placeholder="Name"
                required
                :class="[
                  'border',
                  'w-full',
                  'p-2',
                  'rounded-md',
                  'my-1',
                  'bg-gray-100',
                  errorMessage.nameErr
                    ? 'border-red-500'
                    : registerName.length > 0
                    ? 'border-green-500'
                    : 'border-gray-300',
                ]"
              />
              <p
                class="px-3 py-1 rounded-md text-red-500"
                v-if="errorMessage.nameErr && registerName.length > 0"
              >
                {{ errorMessage.nameErr }}
              </p>
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
                :class="[
                  'border',
                  'w-full',
                  'p-2',
                  'rounded-md',
                  'my-1',
                  'bg-gray-100',
                  errorMessage.emailErr
                    ? 'border-red-500'
                    : registerEmail.length > 0
                    ? 'border-green-500'
                    : 'border-gray-300',
                ]"
              />
              <p
                class="px-3 py-1 rounded-md text-red-500"
                v-if="errorMessage.emailErr && registerEmail.length > 0"
              >
                {{ errorMessage.emailErr }}
              </p>
            </div>

            <div class="relative mt-2 gap-2" style="position: relative">
              <label for="password" class="font-semibold">
                Password <span class="text-red-500">*</span>
              </label>
              <div style="position: relative">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  id="password"
                  v-model="registerPassword"
                  placeholder="Password"
                  required
                  class="border w-full p-2 rounded-md my-1 bg-gray-100 pr-10"
                  :class="{
                    'border-red-500': errorMessage.passwordErr,
                    'border-green-500': registerPassword.length > 0,
                    'border-gray-300':
                      !errorMessage.passwordErr &&
                      registerPassword.length === 0,
                  }"
                  style="padding-right: 2.5rem"
                />
                <button
                  type="button"
                  class="absolute inset-y-0 right-0 flex items-center password-toggle-button"
                  style="top: 50%; transform: translateY(-50%); right: 0.75rem"
                >
                  <icon
                    :icon="showPassword ? 'mdi:eye-off' : 'mdi:eye'"
                    class="text-lg cursor-pointer"
                    @click.stop="toggleShowPassword"
                  />
                </button>
              </div>
              <p
                class="px-3 py-1 rounded-md text-red-500"
                v-if="errorMessage.passwordErr && registerPassword.length > 0"
              >
                {{ errorMessage.passwordErr }}
              </p>
            </div>
            <div class="gap-2 mt-2">
              <label for="number" class="font-semibold">
                Contact Number <span class="text-red-500">*</span>
              </label>
              <div
                class="flex border w-full p-2 rounded-md my-1 bg-gray-100 items-center"
              >
                <span class="bg-gray-200 px-2">+63</span>
                <input
                  type="tel"
                  id="number"
                  v-model="contactNumber"
                  placeholder="8123456789 or 9123456789"
                  required
                  :class="[
                    'flex-1',
                    'border-none',
                    'outline-none',
                    'bg-gray-100',
                    errorMessage.contactNumberErr && contactNumber.length > 0
                      ? 'border-red-500'
                      : contactNumber.length > 0
                      ? 'border-green-500'
                      : 'border-gray-300',
                  ]"
                />
              </div>
              <p
                v-if="errorMessage.contactNumberErr && contactNumber.length > 0"
                class="text-red-500"
              >
                {{ errorMessage.contactNumberErr }}
              </p>
            </div>

            <div>
              <button
                type="submit"
                class="flex w-full justify-center rounded-md bg-sky-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
              >
                Register
              </button>
            </div>
            <div class="flex justify-center items-center gap-2">
              Already have an account?
              <button
                class="text-blue-500 hover:text-blue-700"
                @click="showLoginModal"
              >
                Sign In
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { Icon } from "@iconify/vue";
import { onMounted, ref, reactive, computed, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { API_URL } from "@/config";
import { debounce } from "lodash";
import { RouterLink } from "vue-router";
export default {
  components: {
    Icon,
    RouterLink,
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
      showPassword: false,
    };
  },
  methods: {
    toggleShowPassword() {
      this.showPassword = !this.showPassword;
    },
    showRegisterModal() {
      this.showRegister = true;
      this.showLogin = false;
    },
    showLoginModal() {
      this.showRegister = false;
      this.showLogin = true;
    },
    mounted() {
      this.showLogin = true;
      this.showRegister = false;
    },

    close() {
      this.$emit("update:isVisible", false);
      this.showLogin = true;
      this.showRegister = false;
    },
  },
  setup(props, { emit }) {
    const url = API_URL;

    // Define a ref to hold the active button
    const activeOption = ref("Customer");

    const setActive = (option) => {
      if (option === "Customer") {
        activeOption.value = "Customer";
      } else if (option === "Store") {
        activeOption.value = "Store";
      } else if (option === "Rider") {
        activeOption.value = "Rider";
      }
    };

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
        // Clear previous error messages
        errorMessage.emailErr = "";
        errorMessage.passwordErr = "";

        // Customer Login
        const urliCustomer = `${url}/Ecommerce/vue-project/src/backend/auth.php?action=login`;
        const resCustomer = await axios.post(
          urliCustomer,
          {
            email: loginEmail.value,
            password: loginPassword.value,
          },
          { headers: { "Content-Type": "application/json" } }
        );

        if (resCustomer.data.success) {
          // Handle successful customer login
          errorMessage.emailErr = ""; // Clear email error message
          errorMessage.passwordErr = ""; // Clear password error message
          const nameCustomer = resCustomer.data.customer;
          localStorage.setItem("user", JSON.stringify(nameCustomer));
          refreshPage();
          const roleCustomer = resCustomer.data.role;
          if (roleCustomer === "admin") {
            router.push("/admin_dashboard");
          } else {
            router.push("/home");
            emit("update:isVisible", false);
            emit("login-completed", nameCustomer);
          }
          return; // Exit if customer login is successful
        } else {
          // Handle customer login error
          if (resCustomer.data.messageEmail) {
            errorMessage.emailErr = "Checking";
          } else {
            // If the email is found but the password is incorrect
            errorMessage.passwordErr = "Incorrect password";
            errorMessage.emailErr = "";
            return; // Stop the process if email is found but password is incorrect
          }
        }

        // Rider Login
        const urliRider = `${url}/Ecommerce/vue-project/src/backend/rider/riderAuth.php?action=login`;
        const resRider = await axios.post(
          urliRider,
          {
            email: loginEmail.value,
            password: loginPassword.value,
          },
          { headers: { "Content-Type": "application/json" } }
        );

        if (resRider.data.success) {
          // Handle successful rider login
          errorMessage.emailErr = ""; // Clear email error message
          errorMessage.passwordErr = ""; // Clear password error message
          const nameRider = resRider.data.store;
          localStorage.setItem("rider", JSON.stringify(nameRider));
          const roleRider = resRider.data.rider_role;
          if (roleRider === "rider") {
            router.push("/rider_index");
          } else {
            router.push("/rider_start");
          }
          return; // Exit if rider login is successful
        } else {
          // Handle rider login error
          if (resRider.data.messageEmail) {
            errorMessage.emailErr = "Checking";
          } else if (resRider.data.message) {
            errorMessage.passwordErr = resRider.data.message;
            errorMessage.emailErr = "";
          } else {
            // Handle other errors or set a default error message
            errorMessage.emailErr = "An error occurred during login";
            errorMessage.passwordErr = "An error occurred during login";
          }
        }

        // Seller/Admin Login
        const urliSeller = `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=login`;
        const resSeller = await axios.post(
          urliSeller,
          {
            email: loginEmail.value,
            password: loginPassword.value,
          },
          { headers: { "Content-Type": "application/json" } }
        );

        if (resSeller.data.success) {
          // Handle successful seller/admin login
          errorMessage.emailErr = ""; // Clear email error message
          errorMessage.passwordErr = ""; // Clear password error message
          const nameSeller = resSeller.data.store;
          const roleSeller = resSeller.data.store_role;
          if (roleSeller === "seller") {
            localStorage.setItem("seller", JSON.stringify(nameSeller));
            router.push("/seller_dashboard");
          } else if (roleSeller === "admin") {
            localStorage.setItem("admin", JSON.stringify(nameSeller));
            router.push("/admin_dashboard");
          }
          return; // Exit if seller/admin login is successful
        } else {
          // Handle seller/admin login error
          if (resSeller.data.messageEmail) {
            errorMessage.emailErr = "Checking";
          } else if (resSeller.data.message) {
            errorMessage.passwordErr = resSeller.data.message;
            errorMessage.emailErr = "";
          } else {
            // Handle other errors or set a default error message
            errorMessage.emailErr = "An error occurred during login";
            errorMessage.passwordErr = "An error occurred during login";
          }
        }
      } catch (error) {
        console.error(error);
        // Handle network or other errors
        errorMessage.emailErr = "An error occurred during login";
        errorMessage.passwordErr = "An error occurred during login";
      }
      if (errorMessage.emailErr) {
        errorMessage.emailErr = "email not found";
        errorMessage.passwordErr = "check email first";
      }
      console.log(errorMessage.passwordErr);
      console.log(errorMessage.emailErr);
    };

    const registerZone = ref("");
    const registerHouseno = ref("");
    const registerEmail = ref("");
    const registerName = ref("");
    const registerPassword = ref("");
    const contactNumber = ref("");
    const customerRole = ref("customer");
    const address = ref("Legazpi City");
    const registerResponseMessage = ref("");
    const errorMessage = reactive({
      nameErr: null,
      emailErr: null,
      passwordErr: null,
      contactNumberErr: null, // New error field for house number validation
    });

    const checkNameExists = debounce(async (name) => {
      try {
        const endpoints = [
          `${url}/Ecommerce/vue-project/src/backend/rider/riderAuth.php?action=checkName`,
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=checkName`,
          `${url}/Ecommerce/vue-project/src/backend/auth.php?action=checkName`,
        ];

        let nameExists = false;

        for (const endpoint of endpoints) {
          const response = await axios.post(endpoint, { name });
          if (response.data.exists) {
            nameExists = true;
            break;
          }
        }

        if (nameExists) {
          errorMessage.nameErr = "This name already exists.";
        } else {
          errorMessage.nameErr = nameValidation.value; // Continue with other validations
        }
      } catch (error) {
        console.error("Error checking name:", error);
      }
    }, 500); // 500ms debounce delay

    const nameValidation = computed(() => {
      const pattern = /^[\p{L}'\- \p{M}]*$/u;
      if (!pattern.test(registerName.value.trim())) {
        return "Please enter a valid name.";
      }
      return null;
    });

    const checkEmailExists = debounce(async (email) => {
      try {
        const endpoints = [
          `${url}/Ecommerce/vue-project/src/backend/rider/riderAuth.php?action=checkEmail`,
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=checkEmail`,
          `${url}/Ecommerce/vue-project/src/backend/auth.php?action=checkEmail`,
        ];

        let emailExists = false;

        for (const endpoint of endpoints) {
          const response = await axios.post(endpoint, { email });
          if (response.data.exists) {
            emailExists = true;
            break;
          }
        }

        if (emailExists) {
          errorMessage.emailErr = "This email is already registered.";
        } else {
          errorMessage.emailErr = emailValidation.value; // continue with other validations
        }
      } catch (error) {
        console.error("Error checking email:", error);
      }
    }, 500); // 500ms debounce delay

    const emailValidation = computed(() => {
      if (!registerEmail.value.endsWith("@gmail.com")) {
        return "Email must be a Gmail address (@gmail.com).";
      }
      return null;
    });

    const passwordValidation = computed(() => {
      if (registerPassword.value.length < 8) {
        return "Password must be at least 8 characters long.";
      }
      if (!/[A-Z]/.test(registerPassword.value)) {
        return "Password must include at least one uppercase letter.";
      }
      if (!/[a-z]/.test(registerPassword.value)) {
        return "Password must include at least one lowercase letter.";
      }
      if (!/[0-9]/.test(registerPassword.value)) {
        return "Password must include at least one number.";
      }
      if (!/[\!\@\#\$\%\^\&\*\(\)\_\+\.\,\;\:]/.test(registerPassword.value)) {
        return "Password must include at least one special character (e.g., !@#$%^&*).";
      }
      return null;
    });

    const contactNumberValidation = computed(() => {
      // This pattern checks for numbers starting with '8' or '9' after the '+63' prefix and ensures they are 10 digits in total.
      const pattern = /^[89]\d{9}$/;
      if (!pattern.test(contactNumber.value)) {
        return "Contact number must start with '8' or '9' after the '+63' prefix and be exactly 10 digits long.";
      }
      return null;
    });

    // Watch for changes in the name and email fields to validate them

    watch(
      registerName,
      () => {
        if (registerName.value) {
          checkNameExists(registerName.value); // Trigger the debounced email existence check
        } else {
          errorMessage.nameErr = nameValidation.value;
        }
      },
      { immediate: false }
    );

    watch(
      registerEmail,
      () => {
        if (registerEmail.value.includes("@gmail.com")) {
          checkEmailExists(registerEmail.value); // Trigger the debounced email existence check
        } else {
          errorMessage.emailErr = emailValidation.value;
        }
      },
      { immediate: false }
    );

    watch(
      registerPassword,
      () => {
        errorMessage.passwordErr = passwordValidation.value;
      },
      { immediate: false }
    );

    watch(
      contactNumber,
      () => {
        errorMessage.contactNumberErr = contactNumberValidation.value;
      },
      { immediate: true }
    );

    const showRegister = ref(false);
    const showLogin = ref(true);

    const signUp = async () => {
      // Perform a final validation check on form submission
      errorMessage.nameErr = nameValidation.value;
      errorMessage.emailErr = emailValidation.value;
      errorMessage.passwordErr = passwordValidation.value;
      errorMessage.contactNumberErr = contactNumberValidation.value;
      if (
        errorMessage.nameErr ||
        errorMessage.emailErr ||
        errorMessage.passwordErr ||
        errorMessage.contactNumberErr
      ) {
        console.log(
          errorMessage.nameErr,
          errorMessage.emailErr,
          errorMessage.passwordErr,
          errorMessage.contactNumberErr
        );
        return;
      }

      try {
        // console.log("Registration Data:");
        // console.log("Name:", registerName.value);
        // console.log("Email:", registerEmail.value);
        // console.log("Password:", registerPassword.value); // Note: Be cautious logging sensitive data like passwords
        // console.log("Contact Number:", contactNumber.value);
        // console.log("Role:", customerRole.value);
        // console.log("Address:", address.value);
        // console.log("Barangay:", selectedBarangay.value);
        // console.log("Zone:", registerZone.value);
        // console.log("House Number:", registerHouseno.value);

        const urli = `${url}/Ecommerce/vue-project/src/backend/auth.php?action=register`;
        const res = await axios.post(
          urli,
          {
            name: registerName.value,
            email: registerEmail.value,
            password: registerPassword.value,
            contact_number: contactNumber.value,
            role: customerRole.value,
            address: address.value,
            barangay: selectedBarangay.value,
            zone: registerZone.value,
            house_no: registerHouseno.value,
          },
          { headers: { "Content-Type": "application/json" } }
        );
        registerResponseMessage.value = res.data.message;
        console.log("Im here", res.data);
        if (res.data.success) {
          showRegister.value = false;
          showLogin.value = true;
        }
      } catch (error) {
        console.error("Error during registration:", error);
        if (error.response) {
          // Server responded with an error
          registerResponseMessage.value =
            error.response.data.message ||
            "Registration failed. Please try again.";
        } else if (error.request) {
          // The request was made but no response was received
          registerResponseMessage.value =
            "No response from the server. Please check your internet connection.";
        } else {
          // Something happened in setting up the request that triggered an error
          registerResponseMessage.value =
            "An error occurred while registering. Please try again later.";
        }
      }

      registerName.value = "";
      registerEmail.value = "";
      registerPassword.value = "";
      contactNumber.value = "";
      registerZone.value = "";
      registerHouseno.value = "";
      address.value = "";
      selectedBarangay.value = "";
      // errorMessage.emailErr = "";
      // errorMessage.passwordErr = "";
    };

    const registerAddress = ref("Legazpi City");
    const role = ref(1);
    const signUpStore = async () => {
      // Perform a final validation check on form submission
      errorMessage.nameErr = nameValidation.value;
      errorMessage.emailErr = emailValidation.value;
      errorMessage.passwordErr = passwordValidation.value;
      errorMessage.contactNumberErr = contactNumberValidation.value;
      if (
        errorMessage.nameErr ||
        errorMessage.emailErr ||
        errorMessage.passwordErr ||
        errorMessage.contactNumberErr
      ) {
        console.log(
          errorMessage.nameErr,
          errorMessage.emailErr,
          errorMessage.passwordErr,
          errorMessage.contactNumberErr
        );
        return;
      }
      try {
        const urli = `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=register`;
        const res = await axios.post(
          urli,
          {
            name: registerName.value,
            email: registerEmail.value,
            password: registerPassword.value,
            address: registerAddress.value,
            contact_number: contactNumber.value,
            role: role.value,
          },
          { headers: { "Content-Type": "application/json" } }
        );
        registerResponseMessage.value = res.data.message;
        if (res.data.success) {
          showRegister.value = false;
          showLogin.value = true;
        }
      } catch (res) {
        // console.log(res.data.success);
      }
      // console.log("Register Name:", registerName.value);
      // console.log("Register Email:", registerEmail.value);
      // console.log("Register Password:", registerPassword.value);
      // console.log("Contact Number:", contactNumber.value);
      // console.log("Role:", role.value);

      registerName.value = "";
      registerEmail.value = "";
      registerPassword.value = "";
      contactNumber.value = "";
      role.value = "";
      registerAddress.value = "";
    };

    const signUpRider = async () => {
      // Perform a final validation check on form submission
      errorMessage.nameErr = nameValidation.value;
      errorMessage.emailErr = emailValidation.value;
      errorMessage.passwordErr = passwordValidation.value;
      errorMessage.contactNumberErr = contactNumberValidation.value;
      if (
        errorMessage.nameErr ||
        errorMessage.emailErr ||
        errorMessage.passwordErr ||
        errorMessage.contactNumberErr
      ) {
        console.log(
          errorMessage.nameErr,
          errorMessage.emailErr,
          errorMessage.passwordErr,
          errorMessage.contactNumberErr
        );
        return;
      }
      try {
        const urli = `${url}/Ecommerce/vue-project/src/backend/rider/riderAuth.php?action=register`;
        const res = await axios.post(
          urli,
          {
            name: registerName.value,
            email: registerEmail.value,
            password: registerPassword.value,
            contact_number: contactNumber.value,
            role: role.value,
          },
          { headers: { "Content-Type": "application/json" } }
        );
        registerResponseMessage.value = res.data.message;
        if (res.data.success) {
          showRegister.value = false;
          showLogin.value = true;
        }
      } catch (res) {
        console.log(res.data.success);
      }
      console.log("Register Name:", registerName.value);
      console.log("Register Email:", registerEmail.value);
      console.log("Register Password:", registerPassword.value);
      console.log("Contact Number:", contactNumber.value);
      console.log("Role:", role.value);

      registerName.value = "";
      registerEmail.value = "";
      registerPassword.value = "";
      contactNumber.value = "";
    };

    return {
      signUpRider,
      registerAddress,
      signUpStore,
      setActive,
      activeOption,
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
      showRegister,
      showLogin,
      signUp,
      registerZone,
      registerHouseno,

      registerResponseMessage,
      name,
      address,
      errorMessage,
    };
  },
};
</script>
<style scoped>
@media (max-width: 640px) {
  .password-toggle-button {
    padding-right: 0px; /* Slightly less padding on small screens */
  }
  #password {
    font-size: 12px; /* Smaller font size for inputs on small screens */
  }
}
</style>
