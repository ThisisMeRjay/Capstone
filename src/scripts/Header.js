import SearchModal from "@/components/SearchModal.vue";
import LoginModal from "@/components/LoginModal.vue";
import { Icon } from "@iconify/vue";
import { onMounted, ref, computed, toRefs, watch, reactive } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { getDistance } from "geolib";
import { API_URL } from "@/config";
import moment from "moment-timezone";
import ProductModal from "@/components/ProductModalCart.vue";
import { debounce } from "lodash";
export default {
  components: {
    Icon,
    SearchModal,
    LoginModal,
    ProductModal,
  },
  emits: ["search-completed"],

  data() {
    return {
      showSearch: false,
      showLogin: false,
      user: [],
    };
  },
  methods: {
    formatPrice(value) {
      const numericValue = parseFloat(value);
      if (isNaN(numericValue)) {
        return value; // Return the original value if it's not a valid number
      }
      return numericValue.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
    },
    HamburgerSignin() {
      this.isSidebarOpen = !this.isSidebarOpen;
      this.showLogin = !this.showLogin;
    },
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },
    handleSearchCompleted(data) {
      this.$emit("search-completed", data);
    },
    HandleSignIn(name) {
      this.user = name;
      this.$emit("login-completed", name);
    },
    isRefundAvailable(item) {
      // Check if the order was delivered within the last 7 days
      const deliveredDate = new Date(item.delivered_date);
      // console.log("delivered date", deliveredDate);
      const currentDate = new Date();
      const timeDiff = currentDate.getTime() - deliveredDate.getTime();
      const daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
      return daysDiff <= 7;
    },
    getRemainingDays(item) {
      // Calculate the remaining days to request a refund
      const deliveredDate = new Date(item.delivered_date);
      const currentDate = new Date();
      const timeDiff = currentDate.getTime() - deliveredDate.getTime();
      const daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
      return 7 - daysDiff;
    },
    startRefundProcess(item) {
      // Start the refund process for the given order item
      // You can implement the refund process logic here
    },
    // Other methods...
  },
  setup(props) {
    const url = API_URL;

    const showCart = ref(false);
    const isSidebarOpen = ref(false);
    const cartItemsValue = ref([]);
    const router = useRouter();

    const refundDetailModal = ref(false);
    const selectedFile = ref(null);

    const handleVideoUpload = (event) => {
      const files = event.target.files;
      if (files && files.length > 0) {
        selectedFile.value = files[0];
      } else {
        selectedFile.value = null;
      }
    };

    const openRefundModal = (item) => {
      refundDetailModal.value = !refundDetailModal.value;
      item.reason = "";
    };

    const cancelRefundProcess = (item) => {
      refundDetailModal.value = false;
      selectedFile.value = null;
      item.reason = "";
    };

    const submitRefundRequest = async (item) => {
      try {
        const requestData = {
          order_id: item.order_detail_id,
          reason: item.reason,
        };

        if (selectedFile.value) {
          const fileReader = new FileReader();
          fileReader.readAsDataURL(selectedFile.value);

          fileReader.onload = () => {
            requestData.video_evidence = fileReader.result.split(",")[1];

            axios
              .post(
                `${url}/Ecommerce/vue-project/src/backend/api.php?action=submitRefundRequest`,
                requestData,
                {
                  headers: {
                    "Content-Type": "application/json",
                  },
                }
              )
              .then((response) => {
                refundDetailModal.value = false;
                selectedFile.value = null;
                item.reason = "";
                console.log(response.data);
                // Handle the successful response here
              })
              .catch((error) => {
                console.error("Error submitting refund request:", error);
                // Handle the error here
              });
          };
        } else {
          axios
            .post(
              `${url}/Ecommerce/vue-project/src/backend/api.php?action=submitRefundRequest`,
              requestData,
              {
                headers: {
                  "Content-Type": "application/json",
                },
              }
            )
            .then((response) => {
              refundDetailModal.value = false;
              selectedFile.value = null;
              item.reason = "";
              console.log(response.data);
              // Handle the successful response here
            })
            .catch((error) => {
              console.error("Error submitting refund request:", error);
              // Handle the error here
            });
        }
      } catch (error) {
        console.error("Error submitting refund request:", error);
        // Handle the error here
      }
    };

    const toggleSidebar = () => {
      isSidebarOpen.value = !isSidebarOpen.value;
    };

    const showCartFunction = () => {
      cartItems();
      showCart.value = !showCart.value;
      isSidebarOpen.value = false;
    };
    const closeCart = () => {
      showCart.value = false;
    };

    var userLogin = ref([]);
    // get user data from local storage
    const getUserFromLocalStorage = () => {
      try {
        const userData = localStorage.getItem("user");

        if (userData !== null) {
          userLogin.value = JSON.parse(userData);
        } else {
          router.push("/home");
        }

        console.log("user", userLogin.value);
        return null;
      } catch (error) {
        console.error(
          "Error while getting user data from local storage:",
          error
        );
        return error;
      }
    };

    const refreshPage = () => {
      location.reload(true);
    };
    const Logout = () => {
      localStorage.removeItem("user");
      console.log("click");
      refreshPage();
      //  router.push("/admin_dashboard");
    };
    const ShowProfileModal = ref(false);
    const ShowProfile = () => {
      ShowProfileModal.value = !ShowProfileModal.value;
      isSidebarOpen.value = !isSidebarOpen.value;
      getUserprofile();
    };

    const showSettings = ref(false);
    const showCustomerSettings = () => {
      showSettings.value = !showSettings.value;
      isSidebarOpen.value = !isSidebarOpen.value;
      //console.log("click");
    };
    const checkedItems = ref({});
    const isChecked = (productId) => !!checkedItems.value[productId];

    const toggleCheckbox = (productId) => {
      checkedItems.value = {
        ...checkedItems.value,
        [productId]: !isChecked(productId),
      };
      // console.log(checkedItems.value);
    };
    const checkAll = () => {
      const allProductIds = cartItemsValue.value.map((item) => item.product_id);
      const isAllChecked = allProductIds.every((productId) =>
        isChecked(productId)
      );

      const updatedCheckedItems = {};

      allProductIds.forEach((productId) => {
        updatedCheckedItems[productId] = !isAllChecked;
      });

      checkedItems.value = updatedCheckedItems;
      //console.log(checkedItems.value);
    };

    const atLeastOneItemChecked = computed(() => {
      return Object.values(checkedItems.value).some((value) => value);
    });

    // Computed property to check if all items are checked
    const allItemsChecked = computed(() => {
      const allProductIds = cartItemsValue.value.map((item) => item.product_id);
      return allProductIds.every((productId) => isChecked(productId));
    });

    // set  initial quantities from local storage or default values

    //  Update the quantity in local storage when a user changes it
    const increment = async (productId) => {
      const itemIndex = cartItemsValue.value.findIndex(
        (item) => item.product_id === productId
      );
      if (itemIndex !== -1) {
        const currentItem = cartItemsValue.value[itemIndex];
        const updatedQuantity = Math.min(
          currentItem.quantity + 1,
          currentItem.stock // Use the stock as the maximum limit
        );
        console.log("cart ID ", userLogin.value.user_id);

        try {
          // Make an API call to update the cart quantity
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/api.php?action=updateCart`,
            {
              product_id: productId,
              quantity: updatedQuantity,
              cart_id: userLogin.value.user_id,
            }
          );
          console.log("response update ", response.data);

          // Update the quantity of the specific product
          cartItemsValue.value[itemIndex].quantity = updatedQuantity;
          if (selectedProduct.value.product_id === productId) {
            selectedProduct.value.quantity = updatedQuantity;
            console.log("cart item with new quantity ", selectedProduct.value);
          }
        } catch (error) {
          console.error("Error updating cart quantity:", error);
        }
        cartItems();
      }
    };

    const updatedPrices = ref({});
    let isUpdatingFromFirstWatch = false;
    const updatePrices = debounce(() => {
      const updatedPriceData = {};
      cartItemsValue.value.forEach((item) => {
        const numericPrice = parseFloat(item.price); // Ensure the price is a number
        updatedPriceData[item.product_id] = parseFloat(numericPrice.toFixed(2));
      });
      updatedPrices.value = updatedPriceData;
      cartItemsValue.value = cartItemsValue.value.map((item) => {
        const numericPrice = parseFloat(item.price); // Ensure the price is a number
        const newPrice = updatedPrices.value[item.product_id] || numericPrice;
        const formattedNewPrice = parseFloat(newPrice.toFixed(2));
        const newTotalPrice = newPrice
          ? parseFloat((item.quantity * formattedNewPrice).toFixed(2))
          : 0;
        return { ...item, price: formattedNewPrice, totalPrice: newTotalPrice };
      });
    }, 100);

    watch(
      () =>
        cartItemsValue.value.map((item) => ({
          productId: item.product_id,
          price: item.price,
        })),
      () => {
        updatePrices();
      },
      { deep: true }
    );

    watch(
      () => updatedPrices.value,
      () => {
        if (!isUpdatingFromFirstWatch) {
          isUpdatingFromFirstWatch = true;
          updatePrices();
          isUpdatingFromFirstWatch = false;
        }
      },
      { deep: true }
    );

    const decrement = async (productId) => {
      const itemIndex = cartItemsValue.value.findIndex(
        (item) => item.product_id === productId
      );
      if (itemIndex !== -1) {
        const currentItem = cartItemsValue.value[itemIndex];
        const updatedQuantity = Math.max(
          currentItem.quantity - 1,
          1 // Ensure the quantity does not go below 1
        );

        try {
          // Make an API call to update the cart quantity
          const response = await axios.post(
            `${url}/Ecommerce/vue-project/src/backend/api.php?action=updateCart`,
            {
              product_id: productId,
              quantity: updatedQuantity,
              cart_id: userLogin.value.user_id,
            }
          );

          // Update the quantity of the specific product
          cartItemsValue.value[itemIndex].quantity = updatedQuantity;
          if (selectedProduct.value.product_id === productId) {
            selectedProduct.value.quantity = updatedQuantity;
          }
        } catch (error) {
          console.error("Error updating cart quantity:", error);
        }
        cartItems();
      }
    };

    const deleteCartItems = async (cart_id) => {
      let id = cart_id;
      // console.log(id);
      try {
        const res = await axios.delete(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=deleteCartItem`,
          {
            data: { id },
            headers: {
              "Content-Type": "application/json",
            },
          }
        );
        if (res.data.success) {
          cartItems();
        }
        //  console.log(res);
      } catch (error) {
        console.error(error);
      }
    };

    const cartItems = async () => {
      try {
        const res = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=fetchCartItems`,
          {
            cart_id: userLogin.value.user_id,
          }
        );
        console.log("cart value before", res.data);

        // Merge and update cart items with totalPrice calculation
        const updatedCartItems = [];
        for (const item of res.data) {
          // Calculate the total price for the item
          item.totalPrice = item.price * item.quantity;

          const existingItemIndex = updatedCartItems.findIndex(
            (cartItem) => cartItem.product_id === item.product_id
          );

          if (existingItemIndex !== -1) {
            // Product already in cart, update quantity and total price
            const existingItem = updatedCartItems[existingItemIndex];
            const newQuantity = existingItem.quantity + item.quantity;
            const maxQuantity = Math.min(newQuantity, item.stock); // Limit quantity to available stock
            existingItem.quantity = maxQuantity;
            existingItem.totalPrice = existingItem.totalPrice * newQuantity; // Update total price based on new quantity
          } else {
            // New product, add to cart
            updatedCartItems.push(item);
          }
        }

        cartItemsValue.value = updatedCartItems;
        console.log("cart value: ", cartItemsValue.value);
      } catch (error) {
        console.error("Error fetching cart items:", error);
      }
    };

    // select payment method
    const showPayment = ref(false);

    const checkoutItems = computed(() => {
      const checkedItemIds = Object.keys(checkedItems.value).filter(
        (productId) => checkedItems.value[productId]
      );

      return cartItemsValue.value.filter((item) =>
        checkedItemIds.includes(item.product_id.toString())
      );
    });

    const itemsToCheckout = ref({});
    let priceTotalAll = ref(0);
    let priceTotalPerItem = ref(0);
    // shipping fee must come from db
    const selectedPayment = ref("");

    const fetchShippingFee = (item) => {
      try {
        // Ensure coordinates are parsed as floats
        const productLocation = {
          latitude: parseFloat(item.lat),
          longitude: parseFloat(item.lon),
        };
        const customerLocation = {
          latitude: parseFloat(userLogin.value.lat),
          longitude: parseFloat(userLogin.value.lon),
        };

        // Calculate distance (ensure your getDistance function returns meters for more accuracy)
        const distanceMeters = getDistance(productLocation, customerLocation);
        console.log("Distance (meters):", distanceMeters);

        // Parse additional values as floats to ensure numerical operations
        const baseShippingFee = parseFloat(item.shipping_fee); // Base fee could include handling, smallest package fee, etc.
        const weightKg = parseFloat(item.weight); // Assuming weight is in kilograms
        const dimensionsCm = {
          length: parseFloat(item.length),
          width: parseFloat(item.width),
          height: parseFloat(item.height),
        }; // Assuming dimensions are in centimeters
        const quantity = parseInt(item.quantity); // Parse quantity of the item

        // Constants for calculation
        const weightFactor = 5; // Cost per kilogram
        const volumeFactor = 0.001; // Cost per cubic centimeter (for more granularity)
        const distanceFactor = 0.006; // Cost per meter

        // Calculate volume in cubic centimeters (for more granularity)
        const volumeCm3 =
          dimensionsCm.length * dimensionsCm.width * dimensionsCm.height;

        // Compute the shipping fee, adjust weight and volume based on quantity
        const shippingFee =
          baseShippingFee +
          distanceMeters * distanceFactor +
          weightKg * quantity * weightFactor +
          volumeCm3 * quantity * volumeFactor;

        const dis = baseShippingFee + distanceMeters * distanceFactor;
        const vol = volumeCm3 * quantity * volumeFactor;
        const wei = weightKg * quantity * weightFactor;
        console.log("Shipping Fee:", shippingFee.toFixed(2));
        console.log("Shipping Fee + distance:", dis.toFixed(2));
        console.log("volumeCm3:", vol.toFixed(2));
        console.log("wieght:", wei.toFixed(2));
        return shippingFee.toFixed(2); // Return the shipping fee formatted as a string with two decimal places
      } catch (error) {
        console.error("Error calculating shipping fee:", error);
        throw error; // Rethrow to ensure that calling functions are aware of the error
      }
    };

    const checkout = async () => {
      showPayment.value = true;
      console.log("checked out items value", checkoutItems.value);

      // Reset the total price before calculation
      priceTotalPerItem.value = 0;

      // Use Promise.all to wait for all shipping fee fetches to complete
      const itemsWithShipping = await Promise.all(
        checkoutItems.value.map(async (item) => {
          console.log("product loc", item);
          try {
            // Fetch shipping fee for each item
            const shippingFeeData = await fetchShippingFee(item);
            const shippingFee = parseFloat(shippingFeeData); // Ensure it's a number
            computedshippingFee.value = shippingFeeData;
            console.log("shipping", shippingFeeData);

            // Calculate price per item including shipping fee
            const pricePerItem =
              item.quantity * parseFloat(item.price) + shippingFee;

            // Add price per item to the total
            priceTotalPerItem.value += pricePerItem;

            // Return item with added shipping fee for further processing/display
            return { ...item, shippingFee }; // Use spread operator to include original item properties
          } catch (error) {
            console.error("Error fetching shipping fee for item:", item, error);
            // Handle the error as needed (e.g., set a default shipping fee, notify the user, etc.)
            // Return the item without shipping fee or with default fee
            return { ...item, shippingFee: 0 }; // Assuming default shipping fee is 0
          }
        })
      );

      // After calculating shipping for all items, assign them to itemsToCheckout
      itemsToCheckout.value = itemsWithShipping;

      // Assuming itemsToCheckout.value is an array of objects, each with a 'quantity' property
      totalQuantity.value = itemsToCheckout.value.reduce(
        (accumulator, currentItem) => {
          return accumulator + currentItem.quantity;
        },
        0
      ); // Initial value of the

      // Assign the total price to priceTotalAll
      priceTotalAll.value = priceTotalPerItem.value.toFixed(2);

      console.log("Items to checkout:", itemsToCheckout.value);
    };

    const totalQuantity = ref("");

    const onDelivery = () => {
      selectedPayment.value = "cash on delivery";
    };
    const onPyment = () => {
      selectedPayment.value = "payment";
    };
    const onCredit = () => {
      selectedPayment.value = "credit";
    };

    const closePayment = () => {
      showPayment.value = false;

      priceTotalAll.value = 0;
      priceTotalPerItem.value = 0;
      console.log("click");
    };

    const computedshippingFee = ref(null);
    const submitOrder = async () => {
      console.log(priceTotalAll.value); // Assuming priceTotalAll is a reactive reference
      console.log(selectedPayment.value); // Assuming selectedPayment is a reactive reference
      console.log(userLogin.value.user_id); // Assuming userLogin is a reactive reference
      console.log("checkoutItems length", checkoutItems.value.length);

      // Directly use the value for the API call
      let userID = userLogin.value.user_id;
      let totalPrice = priceTotalAll.value;
      let orderStatus = "pending";
      let numofItems = checkoutItems.value.length;
      let payment = selectedPayment.value;

      // This is for Order details
      let ids = checkoutItems.value.map((item) => item.product_id);
      console.log("product IDs", ids);
      let quantities = checkoutItems.value.map((item) => item.quantity);
      console.log("Quantities", quantities);
      let eachshipping = itemsToCheckout.value.map((item) => item.shippingFee);
      let eachproduct = itemsToCheckout.value.map((item) =>
        parseFloat((parseFloat(item.price) * item.quantity).toFixed(2))
      );
      console.log("each product", eachproduct);
      console.log("each shipping", eachshipping);
      console.log("price to all", priceTotalAll.value);

      //API call
      try {
        const res = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=CheckoutOrder`,
          {
            user_id: userID,
            total_price: totalPrice,
            status: orderStatus,
            item: numofItems,
            product_id: ids,
            quantity: quantities,
            price: eachproduct,
            revenue: eachshipping,
            payment_method: payment,
          },
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        );
        console.log("Response from server", res.data);
      } catch (error) {
        console.error(error);
      }
      refreshPage();
    };

    getUserFromLocalStorage();
    cartItems();

    const orderData = ref([]);

    const getTrackingOrder = async () => {
      try {
        const res = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=getTrackOrder`,
          {
            id: userLogin.value.user_id, // get the user id
          }
        );

        // Assuming res.data.order_records is an array
        const transformedData = res.data.order_records.map((item) => ({
          ...item,
          userRating: 0, // Default user rating
          userComment: "", // Default user comment
          status: statusMapping[item.status.toLowerCase().replace(/\s+/g, "_")], // transform the status
        }));

        orderData.value = transformedData; // Update the reactive variable with the transformed data
        // console.log("order value: ", orderData.value);
      } catch (error) {
        // console.error("Error fetching orders :", error); // return an error
      }
    };

    const ComentandReview = ref([]);

    const ThisIsForCommentAndReview = async () => {
      try {
        const res = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=getTrackOrder`,
          {
            id: userLogin.value.user_id, // get the user id
          }
        );

        // Assuming res.data.order_records is an array
        const transformedData = res.data.order_records.map((item) => ({
          ...item,
          userRating: 0, // Default user rating
          userComment: "", // Default user comment
          status: statusMapping[item.status.toLowerCase().replace(/\s+/g, "_")], // transform the status
        }));

        ComentandReview.value = transformedData; // Update the reactive variable with the transformed data
        // console.log("order value: ", orderData.value);
      } catch (error) {
        console.error("Error fetching orders :", error); // return an error
      }
    };
    setInterval(getTrackingOrder, 50);

    ThisIsForCommentAndReview();

    const statusMapping = {
      pending: 1,
      confirmed: 2,
      processing: 3,
      ready_to_pickup: 4,
      reserved_for_rider: 5,
      out_for_delivery: 6,
      delivered: 7,
      cancelled: 8,
      return_requested: 9,
      return_approved: 10,
      return_in_progress: 11,
      return_completed: 12,
      return_declined: 13,
      closed: 14,
    };

    const showOrderTracking = ref(false);
    const orderTracking = () => {
      toggleSidebar();
      showOrderTracking.value = !showOrderTracking.value;
      if ((showOrderTracking.value = true)) {
      }
    };
    const closeOrderTracking = () => {
      showOrderTracking.value = false;
    };

    const submitComment = async (items, index) => {
      console.log("product id:", items.product_id);
      console.log(`Rating for order ${index}:`, items.userRating);
      console.log(`Comment for order ${index}:`, items.userComment);
      console.log(`User ID:`, userLogin.value.user_id);
      console.log(`order_number:`, items.order_number);

      // Adjust the URL to your comment submission endpoint and ensure the body contains all necessary data
      try {
        const response = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=submitReviews`, // Update this URL to your actual comment submission endpoint
          {
            userId: userLogin.value.user_id,
            productId: items.product_id,
            rating: items.userRating,
            comment: items.userComment,
            order_number: items.order_number,
          },
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        );
        getTrackingOrder();
        // Handle response here, e.g., showing a success message
        console.log(response.data);
      } catch (error) {
        console.error("Error submitting rating and review: ", error);
      }
    };

    const editedRating = ref(null);
    const editedComment = ref(null);

    const saveEditedReview = async (item) => {
      try {
        // Make an API call to update the rating and comment
        const response = await axios.put(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=updateReview`,
          {
            rating: editedRating.value,
            comment: editedComment.value,
            orderId: item.order_number,
          }
        );

        if (response.status === 200) {
          // Update the rating and comment locally
          item.rating = editedRating.value;
          item.comment = editedComment.value;
          isEditingReview.value = false;
          editedRating.value = null;
          editedComment.value = "";
          console.log("Review updated successfully");
        } else {
          console.error("Failed to update review");
        }
      } catch (error) {
        console.error("Error updating review:", error);
      }
    };

    const isEditingReview = ref(false);

    const isEditing = ref(false);
    const toggleEdit = () => {
      isEditing.value = !isEditing.value;
      GetBarangays();
    };

    const selectedBarangay = ref("");
    const barangay = ref([]);

    const GetBarangays = async () => {
      try {
        const res = await axios.get(
          `${url}/Ecommerce/vue-project/src/backend/auth.php?action=getBrgy`
        );
        barangay.value = res.data;
        selectedBarangay.value = userLogin.value.barangay_id;
        console.log("barangaysss: ", res.data);
      } catch (err) {
        console.log(err);
      }
    };

    const profile = ref("");
    const showuserprofile = ref(false);

    const handleImageChange = (event) => {
      const file = event.target.files[0];
      showuserprofile.value = true;
      if (!file) {
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        profile.value = e.target.result;
      };
      reader.readAsDataURL(file);
    };
    const fileInput = ref(null);
    const triggerFileInput = () => {
      // Programmatically click the file input
      if (fileInput.value) {
        fileInput.value.click();
      }
    };

    const getUserprofile = async () => {
      try {
        console.log("id", userLogin.value.user_id);
        console.log("IP", url);
        const res = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/auth.php?action=getProfile`,
          {
            user_id: userLogin.value.user_id,
          }
        );
        profile.value = res.data.user_profile;
        console.log("profile: ", res.data);
      } catch (err) {
        console.log(err);
      }
    };

    onMounted(getUserprofile);

    const errorMessage = reactive({
      nameErr: null,
      contactNumberErr: null,
      houseNumberErr: null,
    });

    const nameValidation = computed(() => {
      const pattern = /^[\p{L}'\- ]+$/u;
      if (!pattern.test(userLogin.value.username.trim())) {
        return "Please enter a valid name.";
      }
      return null;
    });

    const contactNumberValidation = computed(() => {
      // This pattern checks for numbers starting with '8' or '9' after the '+63' prefix and ensures they are 10 digits in total.
      const pattern = /^[89]\d{9}$/;
      if (!pattern.test(userLogin.value.contact_number)) {
        return "Contact number must start with '8' or '9' after the '+63' prefix and be exactly 10 digits long.";
      }
      return null;
    });

    const houseNumberValidation = computed(() => {
      const pattern = /^\d+$/; // Ensures only digits are entered
      if (!pattern.test(userLogin.value.House_no)) {
        return "House number must be numeric.";
      }
      return null;
    });

    const saveProfile = async () => {
      // Perform a final validation check on form submission
      errorMessage.nameErr = nameValidation.value;
      errorMessage.contactNumberErr = contactNumberValidation.value;
      errorMessage.houseNumberErr = houseNumberValidation.value;
      if (
        errorMessage.nameErr ||
        errorMessage.contactNumberErr ||
        errorMessage.houseNumberErr
      ) {
        console.log(
          errorMessage.nameErr,
          errorMessage.contactNumberErr,
          errorMessage.houseNumberErr
        );
        return;
      }
      try {
        const res = await axios.put(
          `${url}/Ecommerce/vue-project/src/backend/auth.php?action=SaveEditprofile`,
          {
            username: userLogin.value.username,
            contact_number: userLogin.value.contact_number,
            address: userLogin.value.address,
            zone: userLogin.value.Zone,
            houseno: userLogin.value.House_no,
            barangay_id: selectedBarangay.value,
            user_id: userLogin.value.user_id,
            profile: profile.value,
          }
        );
        console.log("edit feedback: ", res.data);
        // Assuming you want to show the alert right after logging the response
        alert(
          "Profile updated successfully. Please log in again to see the updates."
        );
      } catch (err) {
        console.error(err);
        alert("Failed to update profile.");
      }
    };

    const cancelOrder = async (ID) => {
      // Add a confirmation dialog before proceeding with the cancellation
      if (!confirm("Are you sure you want to cancel this order?")) {
        console.log("Order cancellation aborted by user.");
        return; // Stop execution if the user does not confirm
      }

      // Get the current date and time in "Asia/Manila" timezone
      const dateToUpdate = moment()
        .tz("Asia/Manila")
        .format("YYYY-MM-DD HH:mm:ss");
      console.log("Date of cancellation: ", dateToUpdate);

      try {
        const Cstatus = "cancelled"; // Define the new status as 'cancelled'
        // Make an HTTP PUT request using axios to update the order status
        const response = await axios.put(
          `${url}/Ecommerce/vue-project/src/backend/seller/sellerApi.php?action=EditStatus`,
          {
            id: ID,
            status: Cstatus,
            estimated_delivery: 0,
            date: dateToUpdate,
          }
        );

        console.log("Response from server:", response.data);
        if (response.status === 200) {
          alert("Order has been successfully cancelled.");
        } else {
          alert("Failed to cancel the order. Please try again.");
        }
      } catch (error) {
        console.error("Error editing status:", error);
        alert(
          "An error occurred while cancelling the order. Please try again."
        );
      }

      // Refresh the page to reflect the changes
      refreshPage();
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

    const selectedProduct = ref([]);
    const isModalVisible = ref(false);

    const showModal = async (product) => {
      console.log("click");
      if (selectedProduct.value !== product) {
        showCart.value = false;
        console.log("modal good", product);
        const specifications = await fetchSpecifications(product.product_id);
        selectedProduct.value = { ...product, specifications };
        console.log("selectedProduct good", selectedProduct.value);
        isModalVisible.value = true;
      }
    };
    const selectedItem = ref(null);
    const openModalId = ref(null);

    function toggleStoreModal(id) {
      console.log("id", id);
      if (openModalId.value === id) {
        // If the modal for this ID is already open, close it
        openModalId.value = null;
      } else {
        // Otherwise, open the modal for this ID and find the corresponding item
        selectedItem.value = itemsToCheckout.value.find(
          (item) => item.store_id === id
        );

        openModalId.value = id;
      }
      console.log("selected item", selectedItem.value);
    }

    function toggleStoreModal2(id) {
      console.log("id", id);
      if (openModalId.value === id) {
        // If the modal for this ID is already open, close it
        openModalId.value = null;
      } else {
        // Otherwise, open the modal for this ID and find the corresponding item
        selectedItem.value = orderData.value.find(
          (item) => item.store_id === id
        );
        openModalId.value = id;
      }
      console.log("selected item 2", selectedItem.value);
    }

    function closeStoreModal() {
      openModalId.value = null;
    }

    return {
      errorMessage,
      nameValidation,
      contactNumberValidation,
      houseNumberValidation,

      selectedFile,
      handleVideoUpload,
      openRefundModal,
      cancelRefundProcess,
      submitRefundRequest,
      refundDetailModal,
      isEditingReview,
      editedRating,
      editedComment,
      saveEditedReview,
      toggleStoreModal2,
      openModalId,
      selectedItem,
      toggleStoreModal,
      closeStoreModal,
      selectedProduct,
      isModalVisible,
      fetchSpecifications,
      showModal,
      cancelOrder,
      saveProfile,
      triggerFileInput,
      fileInput,
      handleImageChange,
      profile,
      showuserprofile,
      GetBarangays,
      selectedBarangay,
      barangay,
      isEditing,
      toggleEdit,
      ShowProfile,
      ShowProfileModal,
      submitComment,
      //tracking
      showOrderTracking,
      orderTracking,
      closeOrderTracking,
      orderData,
      //----------
      showCartFunction,
      showCart,
      closeCart,

      toggleSidebar,

      isSidebarOpen,
      cartItemsValue,

      userLogin,
      Logout,

      showCustomerSettings,
      showSettings,

      isChecked,
      toggleCheckbox,

      checkAll,
      atLeastOneItemChecked,
      allItemsChecked,
      checkout,

      // edit quantity
      increment,
      decrement,

      //
      deleteCartItems,
      //
      showPayment,
      closePayment,

      itemsToCheckout,
      priceTotalAll,

      onDelivery,
      onCredit,
      onPyment,
      submitOrder,
      selectedPayment,
      updatedPrices,
      isUpdatingFromFirstWatch,
      updatePrices,
      totalQuantity,
      ComentandReview,
      ThisIsForCommentAndReview,
    };
  },
};
