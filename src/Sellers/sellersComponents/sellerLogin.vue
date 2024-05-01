<template>
  <div class="w-full">
    <div>
      <h1 class="text-center font-bold text-2xl text-sky-500">Login</h1>
    </div>
    <form class="space-y-6 text-sm" @submit.prevent="signIn">
      <div class="gap-2 mt-2">
        <p class="font-semibold">Email <span class="text-red-500">*</span></p>

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
            'bg-gray-200',
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
            class="border w-full p-2 rounded-md my-1 bg-gray-200 pr-10"
            :class="{
              'border-red-500': errorMessage.passwordErr,
              'border-green-500': loginPassword.length > 0,
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
          v-if="errorMessage.passwordErr && loginPassword.length > 0"
        >
          {{ errorMessage.passwordErr }}
        </p>
      </div>

      <div>
        <button
          type="submit"
          class="flex w-full justify-center rounded-md bg-sky-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
        >
          Sign in
        </button>
      </div>
      <div class="flex justify-center items-center gap-2">
        Don't have an account?
        <RouterLink to="/seller_register" class="text-blue-500"
          >Sign up</RouterLink
        >
      </div>
    </form>
  </div>
</template>
<script>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { API_URL } from "@/config";
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

    const loginEmail = ref("");
    const loginPassword = ref("");
    const router = useRouter();
    const errorMessage = reactive({
      passwordErr: null,
      emailErr: null,
    });
    let name = ref("");
    const signIn = async () => {
      try {
        const urli = `${url}/Ecommerce/vue-project/src/backend/seller/sellerAuth.php?action=login`;
        const res = await axios.post(
          urli,
          {
            email: loginEmail.value,
            password: loginPassword.value,
          },
          { headers: { "Content-Type": "application/json" } }
        );
        errorMessage.emailErr = res.data.messageEmail;
        errorMessage.passwordErr = res.data.message;
        console.log("res data: ", res.data.store);
        name.value = res.data.store;

        if (res.data.success) {
          const role = res.data.store_role;
          if (role === "seller") {
            localStorage.setItem("seller", JSON.stringify(name.value));
            router.push("/seller_dashboard");
          } else if (role === "admin") {
            localStorage.setItem("admin", JSON.stringify(name.value));
            router.push("/admin_dashboard");
          }
        }
      } catch {}
    };
    return {
      loginEmail,
      loginPassword,
      signIn,
      errorMessage,
    };
  },
};
</script>
