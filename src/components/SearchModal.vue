<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur overflow-y-auto h-full w-full z-50"
    @click="close"
  >
    <div
      class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
      @click.stop
    >
      <div class="mt-3 text-center">
        <div class="mt-2">
          <form @submit.prevent="send" method="get">
            <input
              type="text"
              class="border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              v-model="searchQuery"
              placeholder="Search..."
              required
              v-focus="isVisible"
              @keyup.enter="closeModalOnEnter"
            />
          </form>
        </div>
        <div class="items-center px-4 py-3 flex justify-between"></div>
      </div>
      <div>
        <h1 class="font-semibold text-base text-sky-900">Recent searches</h1>
        <div v-for="item in searchrecent">
          <p
            class="px-3 text-sm py-1 my-1 border border-slate-700/10 rounded-md"
          >
            {{ item.productName }}
          </p>
        </div>
      </div>
      <div class="my-3">
        <h1 class="font-semibold text-base text-sky-900">Recommendations</h1>
        <div v-for="item in searchrecent">
          <p class="px-3 text-sm py-1 my-1 border border-sky-700/20 rounded-md">
            {{ item.productName }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import Fuse from "fuse.js";

export default {
  props: {
    isVisible: {
      type: Boolean,
      required: true,
    },
  },
  emits: ["update:isVisible", "search-completed"], // Declare emitted events here
  directives: {
    focus: {
      // Custom directive to autofocus input field
      mounted(el) {
        el.focus();
      },
    },
  },
  setup(props, { emit }) {
    const searchQuery = ref("");
    const products = ref([]);

    const searchrecent = ref([
      { productName: "intel i9 13gen" },
      { productName: "intel i9 13gen" },
    ]);

    const options = {
      keys: ["product_name", "product_description"],
      threshold: 0.2,
    };

    const fetchAllProducts = async () => {
      try {
        const url =
          "http://localhost/Ecommerce/vue-project/src/backend/search.php";
        const response = await axios.post(url);
        products.value = response.data;
      } catch (error) {
        console.error(error);
      }
    };

    onMounted(() => {
      fetchAllProducts();
    });

    const fuse = computed(() => new Fuse(products.value, options));

    const performSearch = () => {
      if (!searchQuery.value.trim()) {
        return products.value;
      }
      const results = fuse.value.search(searchQuery.value);
      emit(
        "search-completed",
        results.map((result) => result.item)
      );
      return results.map((result) => result.item);
    };

    watch(searchQuery, (newValue, oldValue) => {
      if (newValue.trim() !== oldValue.trim()) {
        performSearch();
      }
    });

    const closeModalOnEnter = () => {
      close();
    };

    const close = () => {
      emit("update:isVisible", false);
    };

    return {
      searchQuery,
      searchrecent,
      performSearch, // Now using performSearch as a method instead of a computed property
      close,
      closeModalOnEnter,
    };
  },
};
</script>

<style>
.backdrop-blur {
  backdrop-filter: blur(2px);
}
</style>
