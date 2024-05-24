<template>
  <div class="mx-4">
    <div class="py-3 px-10 font-bold text-2xl text-slate-700">
      <h1>Product Lists</h1>
    </div>
    <div class="flex justify-between mb-4">
      <div class="flex items-center">
        <input
          type="text"
          v-model="searchQuery"
          @input="filterProducts"
          placeholder="Search products..."
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        />
      </div>
    </div>
    <div class="">
      <div class="relative overflow-x-auto shadow rounded-md">
        <table
          class="w-full text-sm text-left rtl:text-right text-gray-900 rounded-md"
        >
          <thead
            class="text-xs text-slate-800 bg-slate-100/20 uppercase rounded-md"
          >
            <tr class="bg-gray-100/10 border-b border-gray-600/50 sticky top-0">
              <th scope="col" class="px-6 py-3">Product ID</th>
              <th scope="col" class="px-6 py-3">Product Name</th>
              <th scope="col" class="px-6 py-3">Price</th>
              <th scope="col" class="px-6 py-3">Ratings</th>
              <th scope="col" class="px-6 py-3">Stock</th>
              <th colspan="2" scope="col" class="px-6 py-3">Action</th>
            </tr>
          </thead>
          <tbody class="">
            <tr
              v-for="item in paginatedProducts"
              :key="item.id"
              class="bg-gray-100/10 border-b border-gray-600/50"
            >
              <td scope="col" class="px-6 py-3">{{ item.product_id }}</td>
              <td
                scope="row"
                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
              >
                {{ item.product_name }}
              </td>
              <td class="px-6 py-4">{{ item.price }}</td>
              <td class="px-6 py-4">{{ item.ratings }}</td>
              <td class="px-6 py-4">{{ item.quantity }}</td>
              <td class="px-6 py-4 flex gap-5">
                <button @click="deleteProduct(item.product_id)">
                  <Icon
                    icon="material-symbols:delete"
                    class="text-lg text-red-500"
                  />
                </button>
                <button @click="editProduct(item.product_id)">
                  <Icon
                    icon="material-symbols:edit"
                    class="text-lg text-green-500"
                  />
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
                :class="{
                  'px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700':
                    currentPage !== 1,
                  'px-3 py-2 ml-0 leading-tight text-blue-600 bg-blue-50 border border-gray-300 rounded-l-lg cursor-default':
                    currentPage === 1,
                }"
                >First</a
              >
            </li>
            <li
              v-for="page in pages"
              :key="page"
              :class="{
                'px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700':
                  page !== currentPage,
                'px-3 py-2 leading-tight text-blue-600 bg-blue-50 border border-gray-300 cursor-default':
                  page === currentPage,
              }"
            >
              <a href="#" @click.prevent="currentPage = page">{{ page }}</a>
            </li>
            <li class="mt-2">
              <a
                href="#"
                @click.prevent="currentPage = pages.length"
                :class="{
                  'px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700':
                    currentPage !== pages.length,
                  'px-3 py-2 leading-tight text-blue-600 bg-blue-50 border border-gray-300 rounded-r-lg cursor-default':
                    currentPage === pages.length,
                }"
                >Last</a
              >
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
  <!-- edit modal -->
  <div
    v-if="showEditModal"
    @click="showEditModal = false"
    class="fixed inset-0 bg-gray-400/50 z-40 flex justify-center items-center"
  >
    <div
      class="bg-slate-200 w-full max-w-md mx-auto my-8 overflow-auto rounded-lg shadow-lg"
      style="max-height: 90vh"
      @click.stop
    >
      <div class="py-2 px-5">
        <h1 class="py-5 text-2xl font-semibold text-gray-700">Edit Product</h1>

        <!-- Form Content -->
        <div class="space-y-2">
          <div class="my-4">
            <h1 class="text-sm font-medium">Category:</h1>
            <select
              class="w-full p-2 rounded-md my-1 border outline-none"
              v-model="selectedCategory"
            >
              <option
                v-for="category in categories"
                :key="category.category_id"
                :value="category.category_id"
              >
                {{ category.category_name }}
              </option>
            </select>
          </div>
          <!-- Product Name -->
          <div>
            <label for="productName" class="text-sm">Product Name:</label>
            <input
              id="productName"
              type="text"
              v-model="product_name"
              class="p-2 rounded-md w-full"
            />
          </div>
          <!-- Image Upload -->
          <div class="py-2">
            <label for="productImage" class="block text-sm"
              >Product Image:</label
            >
            <input
              id="productImage"
              type="file"
              @change="handleImageChange"
              class="p-2 rounded-md w-full"
            />
            <!-- Optionally display the selected image -->
            <div v-if="showimage" class="mt-2">
              <img
                :src="image"
                class="max-h-40 max-w-full rounded-md shadow"
                :alt="product_name"
              />
            </div>
            <div v-else class="mt-2">
              <img
                :src="'data:image/png;base64,' + image"
                class="max-h-40 max-w-full rounded-md shadow"
                :alt="product_name"
              />
            </div>
          </div>
          <div class="py-2">
            <p for="" class="text-sm">Product Description:</p>
            <input
              type="text"
              v-model="product_description"
              class="p-2 rounded-md w-full"
            />
          </div>
          <div class="py-2">
            <p for="" class="text-sm">Price:</p>
            <input
              type="number"
              v-model="product_price"
              class="p-2 rounded-md w-full"
            />
          </div>

          <div>
            <h1 class="text-sm font-medium">Product location:</h1>
            <select
              class="w-full p-2 rounded-md my-1 border outline-none"
              v-model="selectedBarangay"
              required
            >
              <option
                v-for="brgy in barangay"
                :key="brgy.barangay_id"
                :value="brgy.barangay_id"
              >
                {{ brgy.name }}
              </option>
            </select>
          </div>

          <div class="gap-5 flex items-end">
            <div>
              <h1 class="text-xs font-medium">Weight (kg):</h1>
              <div class="flex items-center">
                <input
                  type="number"
                  class="w-full p-2 rounded-md my-1 border outline-none"
                  v-model="Weight"
                />
              </div>
            </div>
            <div>
              <h1 class="text-xs font-medium">Height (cm):</h1>
              <div class="flex items-center">
                <input
                  type="number"
                  class="w-full p-2 rounded-md my-1 border outline-none"
                  v-model="Height"
                />
              </div>
            </div>
            <div>
              <h1 class="text-xs font-medium">Length (cm):</h1>
              <div class="flex items-center">
                <input
                  type="number"
                  class="w-full p-2 rounded-md my-1 border outline-none"
                  v-model="Length"
                />
              </div>
            </div>
            <div>
              <h1 class="text-xs font-medium">Width (cm):</h1>
              <div class="flex items-center">
                <input
                  type="number"
                  class="w-full p-2 rounded-md my-1 border outline-none"
                  v-model="Width"
                />
              </div>
            </div>
          </div>

          <div class="py-2">
            <p for="" class="text-sm">Stocks:</p>
            <input
              type="number"
              v-model="quantity"
              class="p-2 rounded-md w-full"
            />
          </div>
          <h1>Specifications</h1>
          <div
            v-for="(spec, index) in specifications"
            :key="index"
            class="flex flex-col md:flex-row md:items-center gap-4 mb-4"
          >
            <div class="flex-1">
              <label :for="'specKey-' + index" class="block text-sm"
                >Spec Key:</label
              >
              <input
                type="text"
                v-model="spec.spec_key"
                class="p-2 rounded-md w-full"
                :id="'specKey-' + index"
              />
            </div>
            <div class="flex-1">
              <label :for="'specValue-' + index" class="block text-sm"
                >Spec Value:</label
              >
              <input
                type="text"
                v-model="spec.spec_value"
                class="p-2 rounded-md w-full"
                :id="'specValue-' + index"
              />
            </div>
            <button
              @click="removeSpecification(index)"
              class="bg-red-500 text-white p-2 rounded"
            >
              Remove
            </button>
          </div>

          <!-- Add Specification Button -->
          <button
            @click="addSpecification"
            class="px-4 py-2 bg-blue-400 text-white w-full rounded-md shadow my-4"
          >
            Add Specification
          </button>
        </div>

        <!-- Modal Actions -->
        <div class="flex justify-evenly my-5 gap-5 items-center">
          <button
            @click="closeEditModal()"
            class="px-4 py-2 bg-gray-400/20 text-slate-700 w-full rounded-md shadow"
          >
            Cancel
          </button>
          <button
            @click="handleEditProduct"
            class="px-4 py-2 bg-green-400/20 text-green-700 hover:bg-green-500/25 w-full rounded-md shadow"
          >
            Edit
          </button>
        </div>
        <!-- Reviews Section -->
        <div class="py-4">
          <h1 class="text-lg font-medium text-gray-800">Reviews</h1>
          <div v-if="reviews.length">
            <div
              v-for="(review, index) in reviews"
              :key="index"
              class="mt-2 p-2 border rounded-md"
            >
              <h2 class="font-semibold">{{ review.username }}</h2>
              <p>{{ review.comment }}</p>
              <p>Rating: {{ review.rating }} stars</p>
              <div class="text-sm text-gray-600">{{ review.created_at }}</div>
              <hr class="border-t-2 border-gray-300 mt-3" />
            </div>
          </div>
          <div v-else>
            <p>No reviews yet.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
// YourComponent.vue <script> part
import { ref, onMounted, watch, computed } from "vue";
import { Icon } from "@iconify/vue";
import axios from "axios";
import { userLogin, getUserFromLocalStorage } from "@/scripts/Seller"; // Adjust the path as necessary
import { getDistance } from "geolib";
import { API_URL } from "@/config";
export default {
  components: {
    Icon,
  },
  setup() {
    const url = API_URL;

    const refreshPage = () => {
      location.reload(true);
    };

    const Products = ref([]);
    const filteredProducts = ref([]);
    const paginatedProducts = ref([]);
    const searchQuery = ref("");
    const currentPage = ref(1);
    const itemsPerPage = 10;

    const filterProducts = () => {
      const searchString = searchQuery.value.toLowerCase();
      filteredProducts.value = Products.value.filter((product) => {
        return (
          (product.product_id &&
            product.product_id.toString().includes(searchString)) ||
          (product.product_name &&
            product.product_name.toLowerCase().includes(searchString)) ||
          (product.price && product.price.toString().includes(searchString)) ||
          (product.shipping_fee &&
            product.shipping_fee.toString().includes(searchString)) ||
          (product.ratings &&
            product.ratings.toString().includes(searchString)) ||
          (product.quantity &&
            product.quantity.toString().includes(searchString))
        );
      });
      currentPage.value = 1;
      updatePaginatedProducts();
    };

    const updatePaginatedProducts = () => {
      console.log("pagenated:", filteredProducts.value);
      const startIndex = (currentPage.value - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      paginatedProducts.value = filteredProducts.value.slice(
        startIndex,
        endIndex
      );
    };

    const pages = computed(() => {
      const totalPages = Math.ceil(
        filteredProducts.value.length / itemsPerPage
      );
      return Array.from({ length: totalPages }, (_, index) => index + 1);
    });

    watch(currentPage, updatePaginatedProducts);
    watch(filteredProducts, updatePaginatedProducts);
    watch(searchQuery, filterProducts);

    const deleteProduct = async (deleteId) => {
      console.log(deleteId);
      if (confirm("Are you sure you want to delete this product?")) {
        console.log(deleteId);
        try {
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=deleteProduct`,
            {
              id: deleteId,
            }
          );
          console.log("delet message:", response.data);
        } catch (error) {
          console.error("Error deleting:", error);
        }
        fetchProducts();
      } else {
        console.log("delete canceled");
      }
    };

    // get product is for editing

    const showEditModal = ref(false);

    const closeEditModal = () => {
      showEditModal.value = false;
    };
    const productEditable = ref({});

    //info to update
    const product_name = ref("");
    const product_description = ref("");
    const product_price = ref("");
    const shipping_fee = ref("");
    const quantity = ref("");
    const image = ref("");
    const showimage = ref(false);

    const handleImageChange = (event) => {
      const file = event.target.files[0];
      showimage.value = true;
      if (!file) {
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        image.value = e.target.result;
      };
      reader.readAsDataURL(file);
    };

    const specifications = ref([{ spec_key: "", spec_value: "" }]);
    const editProductId = ref("");
    const reviews = ref([]);

    const getReviews = async () => {
      console.log("productID", editProductId.value);
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=getReviews`,
          {
            product_id: editProductId.value,
          }
        );
        reviews.value = response.data;
        console.log("Reviews ", reviews.value);
      } catch (error) {
        console.error("Error fetching Specs:", error);
      }
    };

    // Method to add a new specification entry
    const addSpecification = () => {
      specifications.value.push({ spec_key: "", spec_value: "" });
    };

    // Method to remove a specification entry by index
    const removeSpecification = (index) => {
      specifications.value.splice(index, 1);
    };
    const editProduct = async (editId) => {
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=getSpecs`,
          {
            id: editId,
          }
        );
        specifications.value = response.data;
        console.log("specs ", specifications.value);
      } catch (error) {
        console.error("Error fetching Specs:", error);
      }

      editProductId.value = editId;
      const productToEdit = Products.value.find(
        (product) => product.product_id === editId
      );
      if (productToEdit) {
        // Set the found product to the productEditable ref
        productEditable.value = { ...productToEdit };
        // Show the edit modal
        showEditModal.value = true;
        console.log("edit products:", productEditable.value);
        product_name.value = productEditable.value.product_name;
        product_price.value = productEditable.value.price;
        product_description.value = productEditable.value.product_description;
        shipping_fee.value = productEditable.value.shipping_fee;
        quantity.value = productEditable.value.quantity;
        image.value = productEditable.value.image;
        selectedBarangay.value = productEditable.value.location;
        selectedCategory.value = productEditable.value.category_id;
        Weight.value = productEditable.value.weight;
        Height.value = productEditable.value.height;
        Length.value = productEditable.value.length;
        Width.value = productEditable.value.width;
      }
      getReviews();
    };

    const Weight = ref(null);
    const Height = ref(null);
    const Length = ref(null);
    const Width = ref(null);

    const handleEditProduct = async () => {
      // Check if any of the required fields are empty
      if (!editProductId.value) {
        alert("Please enter the product ID.");
        return;
      }
      if (!product_name.value) {
        alert("Please enter the product name.");
        return;
      }
      if (!product_price.value) {
        alert("Please enter the product price.");
        return;
      }
      if (!product_description.value) {
        alert("Please enter the product description.");
        return;
      }
      // Add alert messages for other required fields here
      if (!quantity.value) {
        alert("Please enter the stocks.");
        return;
      }
      if (!image.value) {
        alert("Please select an image.");
        return;
      }
      if (!selectedBarangay.value) {
        alert("Please select a barangay.");
        return;
      }
      if (!selectedCategory.value) {
        alert("Please select a category.");
        return;
      }
      if (!Weight.value) {
        alert("Please enter the weight.");
        return;
      }
      if (!Height.value) {
        alert("Please enter the height.");
        return;
      }
      if (!Length.value) {
        alert("Please enter the length.");
        return;
      }
      if (!Width.value) {
        alert("Please enter the width.");
        return;
      }
      for (const spec of specifications.value) {
        if (spec.spec_key === "" || spec.spec_value === "") {
          alert("Please enter the complete specifications.");
          return;
        }
      }

      try {
        const response = await axios.put(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=editProductsInfo`,
          {
            product_id: editProductId.value,
            product_name: product_name.value,
            product_price: product_price.value,
            product_description: product_description.value,
            // shipping_fee: shipping_fee.value,
            quantity: quantity.value,
            image: image.value,
            specifications: specifications.value,
            barangay_id: selectedBarangay.value,
            category_id: selectedCategory.value,
            weight: Weight.value,
            height: Height.value,
            length: Length.value,
            width: Width.value,
          }
        );
        console.log("response after edit ", response.data);
      } catch (error) {
        console.error("Error fetching Specs:", error);
      }
      refreshPage();
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

    const selectedCategory = ref("");
    const categories = ref([]);
    const message = ref({ content: "", type: "success" });

    const getCategories = async () => {
      try {
        const response = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=fetchcategories`
        );
        categories.value = response.data;
        console.log("categories ", categories.value);
      } catch (error) {
        console.error("Error fetching categories:", error);
      }
    };

    // Now userLogin is directly accessible here, and it's reactive
    onMounted(() => {
      getUserFromLocalStorage(); // Initialize userLogin from localStorage when component mounts
      fetchProducts(); // Then fetch orders
      GetBarangays();
      getCategories();
    });

    const fetchProducts = async () => {
      console.log("seller ", userLogin.value.store_id);
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=getProducts`,
          {
            store_id: userLogin.value.store_id,
          }
        );
        Products.value = response.data;
        filteredProducts.value = response.data;
        console.log("Products ", Products.value);
      } catch (error) {
        console.error("Error fetching orders:", error);
      }
    };

    const editData = (id) => {
      console.log(id);
    };

    const showBaseShippingModal = ref(false);
    const subrgy = ref(null); // Assuming you want to store a single object, not an array
    const customerbarangay = ref(null);
    const shippingtestResult = ref(null);

    // Define your watcher at the top level
    watch(selectedBarangay, (newVal) => {
      // This watcher will update `subrgy` whenever `selectedBarangay` changes
      subrgy.value =
        barangay.value.find((brgy) => brgy.barangay_id === newVal) || null;
    });

    const triggertesting = () => {
      // Your method can now simply focus on logic unrelated to watching `selectedBarangay`
      showBaseShippingModal.value = !showBaseShippingModal.value;
    };

    const fetchShippingFee = (Height, Weight, Length, Width, shipping_fee) => {
      console.log("test", subrgy.value);
      console.log("test2", shipping_fee);
      if (!subrgy.value || !customerbarangay.value) {
        console.error("subrgy or customerbarangay is not defined");
        return;
      }

      try {
        // Assuming you have `product` and `customer` objects available
        // You need to ensure they are correctly populated from `subrgy` or other sources
        const productLocation = {
          latitude: parseFloat(subrgy.value.lat),
          longitude: parseFloat(subrgy.value.lon),
        };
        // Similar approach for `customerLocation`
        const customerLocation = {
          latitude: parseFloat(customerbarangay.value.lat), // This is likely incorrect; adjust according to your data structure
          longitude: parseFloat(customerbarangay.value.lon), // Adjust as necessary
        };

        // Calculate distance (ensure your getDistance function returns meters for more accuracy)
        const distanceMeters = getDistance(productLocation, customerLocation);
        console.log("Distance (meters):", distanceMeters);

        // Parse additional values as floats to ensure numerical operations
        const baseShippingFee = parseFloat(shipping_fee); // Base fee could include handling, smallest package fee, etc.
        const weightKg = parseFloat(Weight); // Assuming weight is in kilograms
        const dimensionsCm = {
          length: parseFloat(Length),
          width: parseFloat(Width),
          height: parseFloat(Height),
        }; // Assuming dimensions are in centimeters

        // Constants for calculation
        const weightFactor = 1; // Cost per kilogram
        const volumeFactor = 0.005; // Cost per cubic centimeter (for more granularity)
        const distanceFactor = 0.001; // Cost per meter

        // Calculate volume in cubic centimeters (for more granularity)
        const volumeCm3 =
          dimensionsCm.length * dimensionsCm.width * dimensionsCm.height;

        // Compute the shipping fee
        const shippingFee =
          baseShippingFee +
          distanceMeters * distanceFactor +
          weightKg * weightFactor +
          volumeCm3 * volumeFactor;

        console.log("Shipping Fee:", shippingFee.toFixed(2));
        shippingtestResult.value = shippingFee.toFixed(2);
      } catch (error) {
        console.error("Error calculating shipping fee:", error);
        throw error;
      }
    };

    return {
      filteredProducts,
      paginatedProducts,
      searchQuery,
      currentPage,
      pages,
      filterProducts,
      updatePaginatedProducts,
      shippingtestResult,
      customerbarangay,
      subrgy,
      fetchShippingFee,
      triggertesting,
      showBaseShippingModal,
      reviews,
      Weight,
      Height,
      Length,
      Width,

      selectedCategory,
      categories,
      message,
      barangay,
      selectedBarangay,
      Products,
      editData,

      editProduct,
      deleteProduct,

      showEditModal,
      closeEditModal,
      handleEditProduct,
      specifications,
      addSpecification,
      removeSpecification,
      handleImageChange,
      showimage,

      product_name,
      product_price,
      product_description,
      shipping_fee,
      quantity,
      image,
    };
  },
};
</script>
