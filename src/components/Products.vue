<template>
  <div>
    <Header @search-completed="handleSearchCompleted"></Header>
  </div>
  <div
    class="flex py-2 pl-2 sm:pl-8 bg-gradient-to-r from-blue-500/20 from-10% to-blue-500/0 to-100% gap-2 sm:text-sm text-xs"
  >
    <button
      @click="handleButtonClick('category')"
      :class="[
        'py-2 px-4 rounded-full font-semibold shadow-lg border',
        catButton === 'category'
          ? 'bg-slate-700/30 text-white'
          : 'bg-slate-700/10 text-slate-800 hover:bg-slate-700/30 hover:border-slate-300',
      ]"
    >
      Category
    </button>

    <button
      @click="handleButtonClick('home')"
      :class="[
        'py-2 px-4 rounded-full font-semibold shadow-lg border',
        activeButton === 'category' ||
        activeButton === 'home' ||
        activeButton === null
          ? 'bg-slate-700/30 text-white'
          : 'bg-slate-700/10 text-slate-800 hover:bg-slate-700/30 hover:border-slate-300',
      ]"
    >
      Home
    </button>

    <button
      @click="handleButtonClick('store')"
      :class="[
        'py-2 px-4 rounded-full font-semibold shadow-lg border',
        selectedStore && activeButton === 'store'
          ? 'bg-slate-700/30 text-white'
          : 'bg-slate-700/10 text-slate-800 hover:bg-slate-700/30 hover:border-slate-300',
      ]"
    >
      {{ selectedStore ? "Selected Store" : "View All Stores" }}
    </button>
    <div v-if="selectedStore" class="flex items-center gap-2">
      <img
        :src="'data:image/png;base64,' + selectedStore.logo"
        :alt="selectedStore.store_name"
        class="h-8 w-8 rounded-full"
      />
      <span class="text-gray-800 font-semibold">{{
        selectedStore.store_name
      }}</span>
    </div>
  </div>

  <!-- Modal for viewing all stores -->
  <div
    v-if="showStoresModal"
    class="fixed z-50 inset-0 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
  >
    <div
      class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        aria-hidden="true"
      ></div>

      <span
        class="hidden sm:inline-block sm:align-middle sm:h-screen"
        aria-hidden="true"
        >&#8203;</span
      >

      <div
        class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full sm:p-6"
      >
        <div class="sm:flex sm:items-start">
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              id="modal-title"
            >
              All Stores
            </h3>
            <div class="mt-2">
              <div
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
              >
                <div
                  v-for="store in storeName"
                  :key="store.store_id"
                  class="bg-white rounded-lg shadow-md hover:shadow-lg cursor-pointer transition-shadow"
                  @click="selectStore(store.store_id)"
                >
                  <div class="flex justify-center items-center h-32">
                    <img
                      :src="'data:image/png;base64,' + store.logo"
                      :alt="store.store_name"
                      class="h-24"
                    />
                  </div>
                  <div class="px-4 py-2 text-center">
                    <p class="text-gray-800 font-semibold">
                      {{ store.store_name }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
            @click="showStoresModal = false"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

  <div :class="showCategory ? 'flex' : ''">
    <div
      v-if="showCategory"
      class="text-base min-h-full w-72 shadow absolute z-10 sm:relative font-medium bg-gray-50 border border-r-slate-700/10"
    >
      <div class="min-h-screen">
        <p class="px-3 pt-5 pb-3 text-sm text-sky-800">Catergories</p>
        <div v-for="item in categories" :key="item.category_id">
          <div
            v-for="(cat, index) in item"
            :key="index"
            class="mx-3 hover:bg-slate-700/10 rounded-md transition cursor-pointer"
            @click="filterByCategory(cat.category_id, cat.category_name)"
          >
            <button class="py-1 text-xs my-1 px-2 rounded-md">
              {{ cat.category_name }}
            </button>
          </div>
        </div>
        <hr class="my-2" />

        <!-- Price Range -->
        <div class="px-3 py-2">
          <h1 class="text-sm text-sky-800 mb-2">Price Range</h1>
          <div class="flex gap-2 mb-3">
            <input
              v-model="minPrice"
              type="number"
              placeholder="Min"
              class="border p-1 rounded text-sm w-full"
            />
            <input
              v-model="maxPrice"
              type="number"
              placeholder="Max"
              class="border p-1 rounded text-sm w-full"
            />
          </div>
          <button
            @click="filterByPrice"
            class="bg-blue-500/95 py-2 text-slate-100 px-4 rounded-md font-semibold shadow w-full"
          >
            Apply
          </button>
        </div>
        <hr class="my-2" />

        <!-- Sidebar Rating Filters -->
        <div>
          <h1 class="text-sm px-3 text-sky-800">Ratings</h1>
          <div class="mx-3 text-xs">
            <!-- Loop from 6 to 1 (6 - rating gives 0 to 5) -->
            <div
              v-for="rating in 6"
              :key="`rating-${6 - rating}`"
              @click="filterByRating(6 - rating)"
              class="py-2 px-3 hover:bg-slate-700/10 rounded-md transition flex items-center cursor-pointer"
            >
              <div class="flex items-center">
                <!-- Generate filled stars based on 6 - rating -->
                <div
                  v-for="star in 6 - rating"
                  :key="`star-${star}`"
                  class="star-colored"
                >
                  &#9733;
                  <!-- Star icon -->
                </div>
                <!-- Generate empty stars based on rating - 1 (handles 0 case as well) -->
                <div
                  v-for="emptyStar in rating - 1"
                  :key="`empty-star-${emptyStar}`"
                  class="star-grey"
                >
                  &#9733;
                  <!-- Star icon -->
                </div>
              </div>
              <!-- Display "And Up" for ratings less than 5 -->
              <span v-if="6 - rating < 5"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-white w-full">
      <div class="px-10 py-10">
        <div>
          <h2
            class="md:text-2xl text-lg font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r drop-shadow-lg from-blue-600 from-10% to-violet-500"
          >
            {{ selectedCategoryName || "Products" }}
          </h2>
        </div>

        <div
          class="mt-6 grid grid-cols-2 gap-x-4 gap-y-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 xl:gap-x-4"
        >
          <div
            @click="showModal(product)"
            v-for="product in products"
            :key="product.product_id"
            class="cursor-pointer group relative bg-gradient-to-tr from-blue-500 via-violet-500 to-orange-500 rounded-xl p-[1px] overflow-hidden hover:shadow-lg hover:shadow-blue-500/50 transition"
          >
            <div class="bg-slate-100 w-full h-full rounded-xl">
              <div
                class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-xl bg-sky-900 lg:aspect-none group-hover:opacity-75 lg:h-44"
              >
                <img
                  :src="'data:image/png;base64,' + product.image"
                  :alt="product.imageAlt"
                  class="h-32 w-full object-center lg:h-44 lg:w-full"
                />
                <Icon
                  icon="ph:heart-light"
                  class="heart-icon"
                  @click="onHeartClick(product)"
                />
              </div>
              <div class="mt-24 text-xs sm:text-sm">
                <div
                  class="absolute bottom-0 w-full inset-x-0 rounded-b-md p-2"
                >
                  <div class="flex flex-col space-y-2 pl-2">
                    <h3 class="text-sky-900 font-bold">
                      <a :href="product.href">
                        {{ product.product_name }}
                      </a>
                    </h3>
                    <p class="font-medium" v-html="formatPrice(product)"></p>
                    <div class="mt-1">
                      <span
                        v-for="star in getStars(product.ratings)"
                        :key="star.id"
                        :class="{
                          'star-colored': star.colored,
                          'star-grey': !star.colored,
                        }"
                      >
                        <template v-if="!star.half">&#9733;</template>
                        <!-- Full star -->
                        <template v-else>&#9734;</template>
                        <!-- Assuming &#9734; is your half star or modify as needed -->
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <product-modal
            :is-visible="isModalVisible"
            :product="selectedProduct"
            @update:isVisible="isModalVisible = $event"
          ></product-modal>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Header from "@/components/Header.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import { Icon } from "@iconify/vue";
import ProductModal from "@/components/ProductModal.vue";
import { API_URL } from "@/config";
export default {
  components: {
    Icon,
    ProductModal,
    Header,
  },
  methods: {
    formatPrice(product) {
      const numericPrice = parseFloat(product.price);
      const numericNewPrice = parseFloat(product.new_price);

      if (isNaN(numericPrice)) {
        return product.price; // Return the original price if it's not a valid number
      }

      const formattedOldPrice = numericPrice
        .toFixed(2)
        .replace(/\d(?=(\d{3})+\.)/g, "$&,");

      if (!isNaN(numericNewPrice)) {
        const formattedNewPrice = numericNewPrice
          .toFixed(2)
          .replace(/\d(?=(\d{3})+\.)/g, "$&,");
        return `<div>
                <span class="line-through">₱${formattedOldPrice}</span>
                <br>
                <span class="text-red-500">₱${formattedNewPrice}</span>
              </div>`;
      }

      return `₱${formattedOldPrice}`;
    },
  },
  name: "home",
  props: ["products"],

  setup(props) {
    const url = API_URL;

    const minPrice = ref(0);
    const maxPrice = ref(0);

    const showStoresModal = ref(false);

    const fetchAllStores = () => {
      showStoresModal.value = true;
    };

    const activeButton = ref(null);
    const selectedStore = ref(null);
    const catButton = ref(null);
    const sidebutton = ref(null);

    const handleButtonClick = (buttonType) => {
      // if (activeButton.value === buttonType) {
      //   activeButton.value = null;
      // } else {
      //   activeButton.value = buttonType;
      // }

      switch (buttonType) {
        case "category":
          if (catButton.value === buttonType) {
            catButton.value = null;
          } else {
            catButton.value = "category";
          }
          handleSidebarCategory();
          break;
        case "home":
          activeButton.value = buttonType;
          fetchProducts();
          break;
        case "store":
          activeButton.value = buttonType;
          fetchAllStores();
        case "side":
          sidebutton.value = buttonType;
          activeButton.value = 'store';
          break;
      }
      console.log("cat button status: ", catButton.value);
      console.log("act button status: ", activeButton.value);
      console.log("side button status: ", sidebutton.value);
    };
    console.log("cat button status start: ", catButton.value);
    console.log("act button status start: ", activeButton.value);
    const products = ref(props.products || []);
    const isModalVisible = ref(false);
    const selectedProduct = ref(null);
    const showCategory = ref(true);
    const categories = ref([]);
    const selectedCategoryName = ref("");
    const storeName = ref([]);
    const temp_data = ref([]);

    const selectStore = (storeId) => {
      if (storeId) {
        filterbyStoreName(storeId);
        const selectedStoreData = storeName.value.find(
          (store) => store.store_id === storeId
        );
        selectedStore.value = selectedStoreData;
      } else {
        selectedStore.value = null;
        filterbyStoreName(null); // Call filterbyStoreName with null to reset products
      }
      showStoresModal.value = false;
      selectedCategoryName.value = null;
    };

    const filterbyStoreName = async (storeID) => {
      if (temp_data.value.length === 0) {
        temp_data.value = [...products.value]; // Create a new copy of the original products array
      }

      if (storeID) {
        // Filter based on the selected store
        const filtered = temp_data.value.filter((product) => {
          const isMatch = product.store_id === storeID;
          return isMatch;
        });
        products.value = filtered;
      } else {
        // No store selected, show all products
        products.value = [...temp_data.value]; // Reset products to the original array
      }
    };

    //Get stores
    const GetStorename = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=getStorename`
        );
        storeName.value = response.data;
        console.log("stores", response.data);
      } catch (error) {
        console.error("Error fetching storenames: ", error);
      }
    };

    const filterByRating = (minRatingValue) => {
      handleButtonClick("side");
      if (temp_data.value.length === 0) {
        temp_data.value = products.value;
      } else {
        products.value = temp_data.value;
      }

      if (selectedStore.value) {
        // Filter by rating for the selected store
        const filtered = products.value.filter((product) => {
          const roundedRating = Math.round(product.ratings);
          const isRatingMatch = roundedRating === minRatingValue;
          const isStoreMatch =
            product.store_id === selectedStore.value.store_id;
          return isRatingMatch && isStoreMatch;
        });
        products.value = filtered;
      } else {
        // No store selected, filter all products by rating
        const filtered = products.value.filter((product) => {
          const roundedRating = Math.round(product.ratings);
          return roundedRating === minRatingValue;
        });
        products.value = filtered;
      }
    };

    const filterByPrice = () => {
      // Clear any previously set temporary data for other filters
      handleButtonClick("side");
      if (temp_data.value.length === 0) {
        temp_data.value = products.value;
      } else {
        products.value = temp_data.value;
      }

      // Check if both minPrice and maxPrice are zero
      if (minPrice.value === 0 && maxPrice.value === 0) {
        if (selectedStore.value) {
          // Filter by store when no price range is specified
          const filtered = products.value.filter((product) => {
            const isStoreMatch =
              product.store_id === selectedStore.value.store_id;
            return isStoreMatch;
          });
          products.value = filtered;
        } else {
          products.value = temp_data.value;
          console.log("No price filtering applied, returning all products.");
        }
        return;
      }

      if (selectedStore.value) {
        // Filter by price range for the selected store
        const filtered = products.value.filter((product) => {
          const price = parseFloat(product.price);
          const isPriceMatch =
            price >= minPrice.value && price <= maxPrice.value;
          const isStoreMatch =
            product.store_id === selectedStore.value.store_id;
          return isPriceMatch && isStoreMatch;
        });
        products.value = filtered;
      } else {
        // No store selected, filter all products by price range
        const filtered = products.value.filter((product) => {
          const price = parseFloat(product.price);
          return price >= minPrice.value && price <= maxPrice.value;
        });
        products.value = filtered;
      }

      console.log("Filtered range", products.value);
    };

    const fetchSpecifications = async (productId) => {
      console.log("specs id", productId);
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=getProductSpecifications&id=${productId}`
        );
        return response.data;
      } catch (error) {
        console.error("Error fetching specifications: ", error);
        return null;
      }
    };

    const handleSearchCompleted = (product) => {
      products.value = product;
      activeButton.value = "none";
    };
    // get categories
    const getCategories = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=fetchcategories`
        );
        categories.value = response.data;

        //    console.log(categories.value);
      } catch (error) {
        console.error("Error fetching categories: ", error);
      }
    };

    const handleSidebarCategory = () => {
      showCategory.value = !showCategory.value;
    };

    const showModal = async (product) => {
      const specifications = await fetchSpecifications(product.product_id);
      //   console.log("specs result in query", specifications);
      selectedProduct.value = { ...product, specifications };
      //   console.log("s afeifabsb", selectedProduct); // Combine product and specifications
      isModalVisible.value = true;
      //console.log(selectedProduct.value);
    };

    const fetchProducts = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=getProducts`
        );
        //  console.log("API Response Data:", response.data);
        products.value = response.data;

        selectedStore.value = "";

        selectedCategoryName.value = "";
      } catch (error) {
        //     console.error("Error fetching products: ", error);
      }
    };

    const getStars = (rating) => {
      const fullStars = Math.floor(rating);
      const halfStar = rating % 1 >= 0.5 ? 1 : 0;
      const emptyStars = 5 - fullStars - halfStar;
      const stars = [];

      // Push full stars
      for (let i = 0; i < fullStars; i++) {
        stars.push({ id: `full${i}`, colored: true, half: false });
      }

      // Push half star
      if (halfStar) {
        stars.push({ id: "half", colored: true, half: true });
      }

      // Push empty stars
      for (let i = 0; i < emptyStars; i++) {
        stars.push({ id: `empty${i}`, colored: false, half: false });
      }

      return stars;
    };

    // Use axios.post instead of axios.get, and pass data in the request body
    const filterByCategory = async (id, name) => {
      handleButtonClick("side");
      if (temp_data.value.length === 0) {
        temp_data.value = products.value;
      } else {
        products.value = temp_data.value;
      }

      if (selectedStore.value) {
        // Filter by category for the selected store
        const filtered = products.value.filter((product) => {
          const isCategoryMatch = product.category_id === id;
          const isStoreMatch =
            product.store_id === selectedStore.value.store_id;
          return isCategoryMatch && isStoreMatch;
        });
        products.value = filtered;
      } else {
        // No store selected, filter all products by category
        const filtered = products.value.filter((product) => {
          const isMatch = product.category_id === id;
          return isMatch;
        });
        products.value = filtered;
      }

      selectedCategoryName.value = name;
      handleResize();
    };

    onMounted(() => {
      GetStorename();
      getCategories();
      fetchProducts();
      handleResize(); // Call the handler right away to set the initial state
      window.addEventListener("resize", handleResize); // Add event listener on mount
    });

    const onHeartClick = (product) => {
      // Handle the heart icon click event
      console.log("Heart clicked for product:", product.id);
    }; // Create a reactive reference

    const handleResize = () => {
      if (window.innerWidth < 768) {
        // Define your breakpoint for 'small screens'
        showCategory.value = false;
      } else {
        showCategory.value = true;
        catButton.value = "category";
      }
    };

    return {
      sidebutton,
      catButton,
      activeButton,
      handleButtonClick,
      selectedStore,
      handleResize,
      products,
      getStars,
      onHeartClick,
      isModalVisible,
      selectedProduct,
      showModal,
      fetchProducts,

      handleSidebarCategory,
      showCategory,
      categories,

      filterByCategory,
      selectedCategoryName,
      filterbyStoreName,

      handleSearchCompleted,

      minPrice,
      maxPrice,
      filterByPrice,
      filterByRating,

      fetchSpecifications,
      GetStorename,
      temp_data,
      storeName,
      showStoresModal,
      fetchAllStores,
      selectStore,
    };
  },
};
</script>

<style scoped>
.star-colored {
  color: gold;
}
.star-grey {
  color: grey;
}
.heart-icon {
  position: absolute;
  top: 8px;
  right: 8px;
  font-size: 24px;
  color: black; /* Color of the heart icon */
  cursor: pointer;
  user-select: none;
  background-color: #e2e8f0; /* bg-slate-200 color */
  border-radius: 50%;
  padding: 4px;
}
.star-rating-filter {
  background-color: #e2e8f0; /* Light blue background */
  border: none;
  cursor: pointer;
  padding: 5px 10px;
  border-radius: 5px;
  font-size: 16px;
}
.star-rating-filter:hover {
  background-color: #cbd5e1; /* Darker shade for hover */
}
.line-through {
  text-decoration: line-through;
}

.text-red-500 {
  color: #ef4444;
}
</style>
