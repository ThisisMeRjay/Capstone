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
            <th scope="col" class="px-6 py-3 flex justify-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(user, index) in paginatedRiders"
            :key="index"
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
            <td class="px-6 py-4 flex justify-between gap-3 cursor-pointer">
              <button
                class="bg-green-500 font-bold p-2 text-xs text-white shadow rounded hover:bg-green-500/60"
                @click="approved(user.rider_id)"
              >
                Approve
              </button>
              <button
                class="bg-red-500 font-bold p-2 text-xs text-white shadow rounded hover:bg-red-500/60"
                @click="rejected(user.rider_id)"
              >
                Reject
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
</template>

<script>
import { Icon } from "@iconify/vue";
import { onMounted, ref, computed, watch } from "vue";
import { API_URL } from "@/config";
import axios from "axios";

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

    const fetchRiders = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/admin/adminApi.php?action=fetchRiders`
        );
        riders.value = response.data;
        filterBySearch(); // Apply search filter after fetching data
      } catch (error) {
        console.error("Failed to fetch riders:", error);
      }
    };

    onMounted(fetchRiders);

    const approved = async (ID) => {
      // Your existing approved function code...
    };

    const rejected = async (ID) => {
      // Your existing rejected function code...
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

    return {
      searchQuery,
      paginatedRiders,
      currentPage,
      pages,
      approved,
      rejected,
      paginationClass,
    };
  },
};
</script>
