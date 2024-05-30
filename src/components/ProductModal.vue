<template>
  <div
    v-if="isVisible"
    @click="closeModal()"
    class="fixed inset-0 flex items-center justify-center bg-gray-500 border-2 border-zinc-300 bg-opacity-75 z-20"
  >
    <div
      class="bg-white rounded-lg shadow-xl h-80 sm:h-auto w-3/4 sm:max-w-lg overflow-y-auto p-2 sm:p-6 relative max-h-full"
      @click.stop
    >
      <button
        @click="closeModal()"
        class="bg-slate-700/20 absolute right-3 top-3 p-2 rounded-full"
      >
        <Icon
          icon="gravity-ui:xmark"
          class="text-xs sm:text-lg hover:text-red-500"
        />
      </button>
      <div
        class="w-full flex gap-2 capitalize justify-start items-center font-medium text-black-700"
      >
        <span
          class="px-4 py-1 bg-blue-500/10 text-sm sm:text-base shadow text-blue-500 font-semibold rounded-md"
          >{{ product.store_name }}</span
        >
      </div>

      <div class="flex">
        <div class="flex-none w-32 sm:w-48 relative h-40">
          <img
            :src="'data:image/png;base64,' + product.image"
            :alt="product.name"
            class="absolute inset-0 w-32 sm:w-full h-auto object-cover"
            loading="lazy"
          />
        </div>
        <form class="flex-auto p-1 sm:p-6">
          <div class="flex flex-wrap">
            <div>
              <h1
                class="flex-auto text-sm sm:text-xl font-semibold text-gray-900"
              >
                {{ product.product_name }}
              </h1>
              <div>
                <span class="flex text-xs sm:text-base font-medium">{{
                  product.product_description
                }}</span>
              </div>
            </div>
            <div
              class="text-md sm:text-lg font-semibold text-black-500"
              v-html="finalQuantity || formatPrice(product)"
            ></div>
            <div
              class="w-full flex gap-2 justify-start items-center text-sm font-medium text-black-700"
            >
              <span>In stock</span>
              <span class="text-blue-500">{{ product.stock || "0" }}</span>
            </div>
          </div>

          <div>
            <div>
              <form class="max-w-xs mx-auto mb-0 sm:my-2">
                <div class="relative flex items-center max-w-[8rem] sm:pl-8">
                  <button
                    type="button"
                    @click="decrement"
                    :disabled="quantity === 1"
                    class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg px-2 h-8 focus:ring-gray-100 focus:ring-2 focus:outline-none"
                  >
                    <Icon icon="ic:baseline-minus" />
                  </button>
                  <input
                    type="text"
                    id="quantity-input"
                    v-model="quantity"
                    readonly
                    aria-describedby="helper-text-explanation"
                    class="bg-gray-50 border-x-0 border-gray-300 h-8 w-10 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block p-2"
                    placeholder="0"
                    required
                  />
                  <button
                    type="button"
                    @click="increment"
                    :disabled="quantity === product.stock"
                    class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg px-2 h-8 focus:ring-gray-100 focus:ring-2 focus:outline-none"
                  >
                    <Icon icon="tabler:plus" />
                  </button>
                </div>
              </form>
            </div>
            <div class="flex text-sm justify-start items-center font-medium">
              <div class="flex-auto my-1 mx-1 sm:mx-2 flex space-x-4">
                <button
                  :disabled="product.stock === 0"
                  class="h-10 sm:w-40 px-1 sm:px-6 hover:bg-slate-500/10 font-semibold rounded-md border-2 text-gray-900 text-xs"
                  type="button"
                  @click="addToCart(product.product_name, product.product_id)"
                >
                  <span class="text-xs sm:text-sm"> Add to cart</span>
                </button>
              </div>
              <button
                class="flex-none hover:text-red-400 transition flex items-center justify-center w-9 h-9 rounded-md border border-slate-200"
                type="button"
                aria-label="Favorites"
                @click="heart(product.product_id)"
                :class="
                  isHeartRed[product.product_id]
                    ? 'text-red-400'
                    : 'text-slate-400'
                "
              >
                <Icon icon="ph:heart-fill" class="text-lg" />
              </button>
            </div>
          </div>
        </form>
      </div>
      <div class="">
        <div>
          <h1
            class="p-2 bg-slate-800/10 text-blue-500 rounded-md text-sm sm:text-base"
          >
            Specifications :
          </h1>
        </div>
        <ul class="py-3">
          <li
            v-for="(spec, index) in product.specifications.specifications"
            :key="index"
            class="flex gap-2 justify-start items-center"
          >
            <span class="font-medium text-xs sm:text-base">{{
              spec.spec_key
            }}</span
            >: <span class="text-xs sm:text-sm">{{ spec.spec_value }}</span>
          </li>
        </ul>
      </div>
      <!-- Reviews Section -->
      <div class="py-4">
        <div class="flex">
          <p
            @click="getReviews(product.product_id)"
            class="text-xs sm:text-md font-medium text-blue-800 hover:text-blue-600 cursor-pointer"
          >
            View Reviews...
          </p>
        </div>
        <div v-if="reviewActivte">
          <div
            v-for="(review, index) in reviews"
            :key="index"
            class="mt-2 p-2 border rounded-md"
          >
            <h2 class="font-semibold">{{ review.username }}</h2>
            <p>{{ review.comment }}</p>
            <p>Rating: {{ review.rating }} stars</p>
            <div class="text-sm text-gray-600">{{ review.created_at }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- login modal -->
  <LoginModal
    :is-visible="showLogin"
    @update:isVisible="showLogin = $event"
    @login-completed="HandleSignIn"
  ></LoginModal>
</template>

<script>
import LoginModal from "@/components/LoginModal.vue";
import { Icon } from "@iconify/vue";
import axios from "axios";
import { ref, watch, onMounted, reactive, toRefs } from "vue";
import { API_URL } from "@/config";
export default {
  props: {
    isVisible: Boolean,
    product: Object,
  },
  components: {
    Icon,
    LoginModal,
  },
  setup(props, { emit }) {
    const url = API_URL;
    const showLogin = ref(false);

    const refreshPage = () => {
      location.reload(true);
    };
    // Initialize with product.quantity if product exists, or fallback to 1
    const finalQuantity = ref('');
    const isHeartRed = reactive([]);

    const closeModal = () => {
      emit("update:isVisible", false);
      reviews.value = [];
    };

    const { product } = toRefs(props);
    const quantity = ref(1); // Default initial quantity
    const backup = ref(null);

    const resetProduct = () => {
      if (!backup.value) {
        backup.value = JSON.parse(JSON.stringify(product.value));
      }
      return JSON.parse(JSON.stringify(backup.value));
    };

    const formatPrice = (product) => {
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
                <span class="text-blue-500">₱${formattedNewPrice}</span>
              </div>`;
      }

      return `₱${formattedOldPrice}`;
    };

    const increment = () => {
      let productCopy = resetProduct();

      if (productCopy) {
        console.log("increment", productCopy);

        // Ensure the quantity is incremented by 1 and remains a decimal
        quantity.value = Math.min(
          parseFloat((Number(quantity.value) + 1).toFixed(2)), // Adding 1 and fixing to two decimal places
          productCopy.stock
        );

        // Calculate final quantity in decimal
        if (productCopy.new_price) {
          productCopy.new_price = parseFloat(
            (quantity.value * productCopy.new_price).toFixed(2)
          );
        } else {
          productCopy.price = parseFloat(
            (quantity.value * productCopy.price).toFixed(2)
          );
        }

        // Update the product price formatting
        finalQuantity.value = formatPrice(productCopy);

        // Emit an event to update the product object
        emit("update:product", productCopy);
        console.log("increment after", productCopy);
      }
    };

    const decrement = () => {
      let productCopy = resetProduct();

      if (productCopy) {
        console.log("decrement", productCopy);
        console.log("current quantity:", quantity.value);

        // Ensure the quantity is decremented by 1 and remains a decimal
        quantity.value = Math.max(
          parseFloat((Number(quantity.value) - 1).toFixed(2)),
          1 // Minimum quantity is 1
        );

        console.log("new quantity after decrement:", quantity.value);

        // Calculate final quantity in decimal
        if (productCopy.new_price) {
          productCopy.new_price = parseFloat(
            (quantity.value * productCopy.new_price).toFixed(2)
          );
        } else {
          productCopy.price = parseFloat(
            (quantity.value * productCopy.price).toFixed(2)
          );
        }

        // Update the product price formatting
        finalQuantity.value = formatPrice(productCopy);

        // Emit an event to update the product object
        emit("update:product", productCopy);
        console.log("decrement after", productCopy);
      }
    };

    watch(
      () => quantity.value,
      (newQuantity) => {
        if (newQuantity !== undefined) {
          quantity.value = newQuantity;
        }
      },
      { immediate: true }
    );

    const userLogin = ref([]);
    // get user data from local storage
    const getUserFromLocalStorage = () => {
      try {
        const userData = localStorage.getItem("user");

        if (userData !== null) {
          userLogin.value = JSON.parse(userData);
        }

        return null;
      } catch (error) {
        console.error(
          "Error while getting user data from local storage:",
          error
        );
        return error;
      }
    };
    getUserFromLocalStorage();
    const cart_id = userLogin.value.user_id;

    const addToCart = async (name, id) => {
      try {
        if (cart_id === null || cart_id === undefined) {
          console.log("You have to login first");
          closeModal();
          showLogin.value = true;
          return;
        }

        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=addCart`,
          {
            product_id: id,
            quantity: quantity.value,
            cart_id: cart_id,
          }
        );

        // console.log(response.data);
        emit("update:isVisible", false);
        refreshPage();
      } catch (error) {
        console.error("Error adding to cart:", error);
      }
    };

    const heart = (productId) => {
      // Toggle the heart state for the clicked product
      isHeartRed[productId] = !isHeartRed[productId];

      console.log("heart for product", productId);
      console.log(isHeartRed[productId]);
    };

    const reviews = ref([]);

    const reviewActivte = ref(false);

    const getReviews = async (productID) => {
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=getReviews`,
          {
            product_id: productID,
          }
        );
        reviews.value = response.data;
        console.log("Reviews ", reviews.value);
      } catch (error) {
        console.error("Error fetching Specs:", error);
      }
      reviewActivte.value = !reviewActivte.value;
    };

    onMounted(() => {
      heart();
    });

    return {
      formatPrice,
      reviewActivte,
      showLogin,
      getReviews,
      reviews,
      quantity,
      increment,
      decrement,
      finalQuantity,
      addToCart,
      heart,
      isHeartRed,
      closeModal,
    };
  },
};
</script>
<style>
.line-through {
  text-decoration: line-through;
}

.text-blue-500 {
  color: #3b82f6;
}
</style>
