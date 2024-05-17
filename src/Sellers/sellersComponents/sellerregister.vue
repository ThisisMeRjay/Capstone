<template>
  <div class="w-full">
    <div>
      <h1 class="text-center font-bold text-2xl text-sky-500">Register</h1>
    </div>
    <p
      v-if="registerResponseMessage"
      class="text-green-600 px-4 shadow-sm mt-2 py-3 rounded-md bg-green-400/10"
    >
      {{ registerResponseMessage }}
    </p>
    <form class="space-y-6 text-sm" @submit.prevent="signUp">
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
                !errorMessage.passwordErr && registerPassword.length === 0,
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
        <RouterLink to="/seller_Login" class="text-blue-500"
          >Sign in</RouterLink
        >
      </div>
    </form>
  </div>
</template>
<script>
import axios from "axios";
import { ref, watch, reactive, computed } from "vue";
import { API_URL } from "@/config";
import { debounce } from "lodash";
import { Icon } from "@iconify/vue";
export default {
  components: {
    Icon,
  },
  data() {
    return {
      showPassword: false,
    };
  },
  methods: {
    toggleShowPassword() {
      this.showPassword = !this.showPassword;
    },
  },
  setup() {
    const url = API_URL;

    const registerEmail = ref("");
    const registerAddress = ref("Legazpi City");
    const registerName = ref("");
    const registerPassword = ref("");
    const contactNumber = ref("");
    const role = ref(1);
    const registerResponseMessage = ref("");
    const errorMessage = reactive({
      nameErr: null,
      emailErr: null,
      passwordErr: null,
      contactNumberErr: null,
    });

    const checkNameExists = debounce(async (name) => {
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=checkName`,
          {
            name: name,
          }
        );
        if (response.data.exists) {
          errorMessage.nameErr = "This name is already exists.";
        } else {
          errorMessage.nameErr = nameValidation.value; // continue with other validations
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
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=checkEmail`,
          {
            email: email,
          }
        );
        if (response.data.exists) {
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
    return {
      registerAddress,
      registerName,
      registerEmail,
      registerPassword,
      contactNumber,
      signUp,
      registerResponseMessage,
      errorMessage,
    };
  },
};
</script>
