import SearchModal from "@/components/SearchModal.vue";
import LoginModal from "@/components/LoginModal.vue";
import { Icon } from "@iconify/vue";
import { onMounted, ref, computed, toRefs } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { getDistance } from "geolib";
import { API_URL } from "@/config";
import moment from "moment-timezone";
export default {
  components: {
    Icon,
    SearchModal,
    LoginModal,
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
  },
  setup(props) {
    const url = API_URL;

    const showCart = ref(false);
    const isSidebarOpen = ref(false);
    const cartItemsValue = ref([]);
    const router = useRouter();

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

    const cartItems = async () => {
      try {
        const res = await axios.post(
          `${url}/Ecommerce/vue-project/src/backend/api.php?action=fetchCartItems`,
          {
            cart_id: userLogin.value.user_id,
          }
        );
        cartItemsValue.value = res.data;
        // console.log(userLogin.value.user_id);
        console.log("cart value: ", cartItemsValue.value);
      } catch (error) {
        console.error("Error fetching cart items:", error);
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
    const increment = (productId) => {
      const itemIndex = cartItemsValue.value.findIndex(
        (item) => item.product_id === productId
      );
      if (itemIndex !== -1) {
        const updatedQuantity = Math.min(
          Number(cartItemsValue.value[itemIndex].quantity || 1) + 1,
          3
        );
        // Update the quantity of the specific product
        cartItemsValue.value[itemIndex].quantity = updatedQuantity;
      }
    };

    //  update the quantity in local storage
    const decrement = (productId) => {
      const itemIndex = cartItemsValue.value.findIndex(
        (item) => item.product_id === productId
      );
      if (itemIndex !== -1) {
        const updatedQuantity = Math.min(
          Number(cartItemsValue.value[itemIndex].quantity || 1) - 1,
          3
        );
        // Update the quantity of the specific product
        cartItemsValue.value[itemIndex].quantity = updatedQuantity;
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

      // Assign the total price to priceTotalAll
      priceTotalAll.value = priceTotalPerItem.value.toFixed(2);

      console.log("Items to checkout:", itemsToCheckout.value);
    };

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
      let productsTotalIncludingShipping = checkoutItems.value.map((item) =>
        parseFloat(
          (
            parseFloat(item.price) * item.quantity +
            parseFloat(computedshippingFee.value)
          ).toFixed(2)
        )
      );
      console.log(
        "Total Prices including shipping",
        productsTotalIncludingShipping
      );

      // API call
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
            price: productsTotalIncludingShipping,
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
        console.log("order value: ", orderData.value);
      } catch (error) {
        console.error("Error fetching orders :", error); // return an error
      }
    };

    getTrackingOrder();

    const statusMapping = {
      pending: 1,
      confirmed: 2,
      processing: 3,
      out_for_delivery: 4,
      delivered: 5,
      cancelled: 6,
      return_requested: 7,
      return_approved: 8,
      return_in_progress: 9,
      return_completed: 10,
      return_declined: 11,
    };

    const showOrderTracking = ref(false);
    const orderTracking = (e) => {
      showOrderTracking.value = !showOrderTracking.value;
    };
    const closeOrderTracking = (e) => {
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

    const saveProfile = async () => {
      try {
        const res = await axios.put(
          `${url}/Ecommerce/vue-project/src/backend/auth.php?action=SaveEditprofile`,
          {
            username: userLogin.value.username,
            contact_number: userLogin.value.contact_number,
            address: userLogin.value.address,
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

    return {
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
    };
  },
};
