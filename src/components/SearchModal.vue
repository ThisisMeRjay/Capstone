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
import { ref } from "vue";

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
    const searchProduct = ref([]);

    const close = () => {
      emit("update:isVisible", false);
    };

    const searchrecent = ref([
      {
        productName: "intel i9 13gen",
      },
      {
        productName: "intel i9 13gen",
      },
    ]);
    const send = async () => {
      try {
        const url =
          "http://localhost/Ecommerce/vue-project/src/backend/search.php";
        const response = await axios.post(url, { query: searchQuery.value });

        searchProduct.value = response.data;
        emit("search-completed", searchProduct.value); // Emitting the event
        console.log("Response:", searchProduct.value);
      } catch (error) {
        console.log(error);
      }

      searchQuery.value = "";
      close();
    };

    return {
      searchQuery,
      searchrecent,
      send,
      close,
    };
  },
};
</script>

<style>
/* Define the blur effect */
.backdrop-blur {
  backdrop-filter: blur(
    2px
  ); /* Adjust the pixel value to increase or decrease the blur effect */
}
</style>
