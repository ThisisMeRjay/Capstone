<template>
  <div class="bg-sky-900 text-white py-5 flex justify-start items-center gap-3">
    <h1 class="text-lg pl-5">Admin</h1>
  </div>
  <div class="flex relative">
    <div
      class="text-base min-h-full w-72 shadow absolute z-10 sm:relative font-medium bg-gray-50 border border-r-slate-700/10"
    >
      <div class="h-screen">
        <RouterLink to="/admin_dashboard_home">
          <div
            class="w-full bg-gray-100 p-2 mt-2 rounded-xl"
            :class="{
              ' text-sky-700': $route.name === 'admin_dashboard_home',
              'text-sky-900': $route.name !== 'admin_dashboard_home',
              ' font-semibold text-base': true,
            }"
          >
            Dashboard
          </div>
        </RouterLink>
        <RouterLink to="/users">
          <div
            class="w-full bg-gray-100 p-2 mt-3 rounded-xl"
            :class="{
              'text-sky-700':
                $route.name === 'admin_dashboard_customers' ||
                $route.name === 'admin_dashboard_sellers',
              'text-sky-900':
                $route.name !== 'admin_dashboard_customers' &&
                $route.name !== 'admin_dashboard_sellers',
            }"
          >
            User Management
          </div>
        </RouterLink>
        <RouterLink to="/manage_request">
          <div
            class="w-full bg-gray-100 p-2 mt-3 rounded-xl"
            :class="{
              'text-sky-700':
                $route.name === 'manage_request_seller' ||
                $route.name === 'manage_request_rider',
              'text-sky-900':
                $route.name !== 'manage_request_seller' &&
                $route.name !== 'manage_request_rider',
            }"
          >
            Manage Request
          </div>
        </RouterLink>
        <RouterLink to="/admin_delivery">
          <div
            class="w-full bg-gray-100 p-2 mt-3 rounded-xl"
            :class="{
              'text-sky-700': $route.name === 'admin_delivery',
              'text-sky-900': $route.name !== 'admin_delivery',
              ' font-semibold text-base': true,
            }"
          >
            Delivery Management
          </div>
        </RouterLink>
        <RouterLink to="/admin_refund">
          <div
            class="w-full bg-gray-100 p-2 mt-3 rounded-xl"
            :class="{
              'text-sky-700': $route.name === 'admin_refund',
              'text-sky-900': $route.name !== 'admin_refund',
              ' font-semibold text-base': true,
            }"
          >
            Refund Management
          </div>
        </RouterLink>
        <RouterLink to="/admin_shipping">
          <div
            class="w-full bg-gray-100 p-2 mt-3 rounded-xl"
            :class="{
              'text-sky-700': $route.name === 'admin_shipping',
              'text-sky-900': $route.name !== 'admin_shipping',
              ' font-semibold text-base': true,
            }"
          >
            Shipping Management
          </div>
        </RouterLink>
      </div>
      <div class="ml-2">
        <button
          @click="logout"
          class="flex justify-start items-center font-semibold hover:bg-slate-400/20 rounded-md text-slate-700 w-full py-2"
        >
          <Icon icon="solar:logout-line-duotone" class="text-2xl" />
          Logout
        </button>
      </div>
    </div>

    <div class="bg-white cursor-pointer">
      <div class="w-full px-4 sm:pt-5">
        <div>
          <!--  -->
          <router-view> </router-view>
          <!--  -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";
import { Icon } from "@iconify/vue";
import { useRouter } from "vue-router";
export default {
  components: {
    Icon,
  },
  setup() {
    const router = useRouter();
    const logout = () => {
      localStorage.removeItem("admin");
      router.push("/seller_index");
    };
    var userLogin = ref([]);
    const getUserFromLocalStorage = () => {
      const userData = localStorage.getItem("admin");
      if (userData) {
        userLogin.value = JSON.parse(userData);
      } else {
        router.push("/seller_index");
      }
    };
    getUserFromLocalStorage();
    return {
      logout,
      userLogin,
    };
  },
};
</script>
