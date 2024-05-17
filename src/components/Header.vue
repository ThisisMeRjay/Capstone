<template>
  <!-- Top -->
  <div
    class="sm:flex justify-between py-4 px-2 sm:px-12 text-xs sm:text-sm cursor-pointer hidden"
  >
    <div class="flex place-items-center">
      <p class="text-slate-600">
        Need help? Call us:
        <span class="text-blue-500 text-base"> 09123456789</span>
      </p>
    </div>

    <div
      class="flex items-center text-violet-600 underline hover:text-violet-800"
      v-if="userLogin.length === 0"
    >
      <RouterLink
        to="/seller_dashboard"
        :class="$route.name === 'seller_dashboard'"
        >Sign in as Seller</RouterLink
      >
    </div>
    <div
      class="flex items-center text-violet-600 underline hover:text-violet-800"
      v-if="userLogin.length === 0"
    >
      <RouterLink to="/rider_home" :class="$route.name === 'rider_home'"
        >Sign in as Rider</RouterLink
      >
    </div>
    <div
      class="flex items-center gap-2 px-3 py-2 bg-blue-400/10 rounded-full shadow text-blue-500 hover:font-semibold transition"
      @click="orderTracking()"
      :class="userLogin.length === 0 ? 'text-blue-500 pointer-events-none' : ''"
    >
      <Icon icon="fluent:vehicle-bus-24-regular" class="text-lg" />
      <span>Track your order</span>
    </div>
  </div>

  <!-- payment popup -->
  <div
    v-if="showOrderTracking"
    class="justify-center items-center flex w-full h-full overflow-scroll"
  >
    <div
      @click="closeOrderTracking()"
      class="fixed z-40 w-full top-0 left-0 h-full backdrop-blur bg-slate-900 bg-opacity-50"
    ></div>
    <div
      class="fixed z-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 justify-center items-center flex"
    >
      <div
        class="overflow-scroll bg-slate-100 h-[400px] sm:h-[500px] rounded-md"
      >
        <div
          class="p-5 bg-slate-100 rounded-md h-full w-[330px] sm:w-[600px] text-slate-800 text-xs"
        >
          <div class="h-full">
            <div class="flex justify-between">
              <h1 class="font-semibold text-lg">Order Tracking</h1>
              <div
                @click="closeOrderTracking"
                class="bg-slate-600/20 rounded-full text-red-500 shadow p-2"
              >
                <Icon icon="iconamoon:close-bold" />
              </div>
            </div>
            <hr class="my-2" />
            <div v-if="orderData.length !== 0">
              <h1 class="text-base font-semibold py-1">
                Your Orders (<span class="text-blue-500">{{
                  orderData.length
                }}</span
                >)
              </h1>
              <hr class="my-2" />
              <div
                class="p-2 rounded-md bg-slate-400/10 my-2 w-full"
                v-for="(items, index) in orderData"
                :key="index"
              >
                <div>
                  <div class="flex justify-between items-center">
                    <span class="font-semibold text-slate-800"
                      >#{{ items.order_number }}</span
                    >
                    <button
                      v-if="items.status === 1"
                      class="bg-gray-500/10 px-4 py-1 rounded-md text-red-500 shadow hover:bg-gray-500/20"
                      @click="cancelOrder(items.order_detail_id)"
                    >
                      Cancel
                    </button>
                  </div>
                  <div class="my-2 flex gap-2 justify-start items-center">
                    <div>
                      <img
                        :src="'data:image/png;base64,' + items.image"
                        :alt="items.imageAlt"
                        class="h-20 w-20 object-center"
                      />
                    </div>
                    <div class="w-full">
                      <div class="flex justify-between">
                        <div>
                          <h1 class="text-base font-semibold">
                            {{ items.product_name }}
                          </h1>
                        </div>
                        <div>
                          <span
                            class="text-sm font-semibold bg-slate-400/30 rounded p-1 cursor-pointer hover:bg-slate-400/50"
                            @click="toggleStoreModal2(items.store_id)"
                            >{{ items.store_name }}</span
                          >
                        </div>
                      </div>
                      <!-- Modal Element -->
                      <div
                        v-if="openModalId === items.store_id"
                        class="modal absolute inset-x-0 mx-3 md:right-0 md:mt-1 md:w-64 md:translate-x-full bg-white shadow-lg rounded-lg p-4 transition-transform"
                      >
                        <div class="flex justify-between">
                          <h3
                            class="font-semibold text-lg flex justify-center text-blue-500"
                          >
                            {{ selectedItem.store_name }}
                          </h3>
                          <button
                            @click="closeStoreModal"
                            class="rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300"
                          >
                            <Icon icon="iconamoon:close-bold" />
                          </button>
                        </div>
                        <div class="mt-2 font-semibold">
                          <p class="text-sm mt-1">
                            Address: {{ selectedItem.store_address }}
                          </p>
                          <p class="text-sm mt-1">
                            Contact no:
                            {{ selectedItem.store_contact_number }}
                          </p>
                        </div>
                      </div>
                      <p class="font-semibold pt-1">
                        Quantity:
                        <span
                          class="text-red-500 py-1 px-2 bg-slate-500/10 rounded-md"
                          >{{ items.quantity }}</span
                        >
                      </p>
                      <p class="font-semibold pt-3">
                        Total:
                        <span
                          class="text-red-500 py-1 px-2 bg-slate-500/10 rounded-md"
                          >₱{{ items.priceTrack }}</span
                        >
                      </p>
                    </div>
                  </div>
                  <div>
                    <div v-if="items.status === 8" class="my-4">
                      <span
                        class="text-red-500 text-sm bg-red-400/10 p-2 rounded"
                        >Order Cancelled.</span
                      >
                    </div>
                    <div v-if="items.status !== 8">
                      <div
                        class="mb-5 flex justify-between items-center bg-blue-300/20 rounded-md p-1"
                      >
                        <div>
                          <p class="text-xs font-light">Date purchased</p>
                          <p class="text-sm font-semibold">
                            {{ items.date_purchased }}
                          </p>
                        </div>
                        <Icon
                          icon="iconamoon:arrow-right-2-light"
                          class="text-xl"
                        />
                        <div>
                          <p class="text-xs font-light">Estemated Delivery</p>
                          <p class="text-sm font-semibold">
                            {{ items.estimated_delivery }}
                          </p>
                        </div>
                      </div>
                      <ol
                        class="flex flex-col sm:flex-row gap-1 sm:items-center w-full font-medium text-center text-gray-500"
                      >
                        <li
                          class="flex md:w-full items-center sm:after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-2 dark:after:border-gray-600"
                        >
                          <span
                            class="flex items-center text-custom-size after:content-['/'] sm:after:hidden after:mx-1 after:text-gray-200"
                          >
                            <div v-if="items.status >= 1">
                              <Icon
                                icon="lets-icons:check-fill"
                                class="sm:text-lg text-blue-600 text-xs"
                              />
                            </div>
                            Pending
                          </span>
                        </li>
                        <li
                          class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-2 dark:after:border-gray-600"
                        >
                          <span
                            class="flex text-xs items-center text-custom-size after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200"
                          >
                            <div v-if="items.status >= 2">
                              <Icon
                                icon="lets-icons:check-fill"
                                class="sm:text-lg text-blue-600 text-xs"
                              />
                            </div>
                            Confirmed
                          </span>
                        </li>
                        <li
                          class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-2 dark:after:border-gray-600"
                        >
                          <span
                            class="flex text-xs items-center text-custom-size after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200"
                          >
                            <div v-if="items.status >= 3">
                              <Icon
                                icon="lets-icons:check-fill"
                                class="sm:text-lg text-blue-600 text-xs"
                              />
                            </div>
                            Processing
                          </span>
                        </li>
                        <li
                          class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-2 dark:after:border-gray-600"
                        >
                          <span
                            class="flex text-xs items-center text-custom-size after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200"
                          >
                            <div v-if="items.status >= 6">
                              <Icon
                                icon="lets-icons:check-fill"
                                class="sm:text-lg text-blue-600 text-xs"
                              />
                            </div>
                            Out for delivery
                          </span>
                        </li>
                        <li class="flex items-center text-custom-size text-xs">
                          <div v-if="items.status >= 7">
                            <Icon
                              icon="lets-icons:check-fill"
                              class="sm:text-lg text-blue-600 text-xs"
                            />
                          </div>
                          Delivered
                        </li>
                      </ol>
                      <div
                        class="border border-blue-500/50 rounded-md p-2 my-5"
                      >
                        <div
                          v-if="items.status >= 1"
                          class="bg-blue-500/10 rounded-md p-1"
                        >
                          <p class="text-xs font-light">
                            {{ items.created_at }}
                          </p>
                          <p class="text-sm font-light text-blue-600">
                            Thank you for your order! It is currently pending.
                            We're getting it ready to be processed. Stay tuned
                            for more updates.
                          </p>
                        </div>
                        <div
                          v-if="items.status >= 2"
                          class="bg-blue-500/10 rounded-md p-1 my-1"
                        >
                          <p class="text-xs font-light">
                            {{ items.confirmed_date }}
                          </p>
                          <p class="text-sm font-light text-blue-600">
                            Your order has been confirmed! We are preparing your
                            items for shipment.
                          </p>
                        </div>
                        <div
                          v-if="items.status >= 3"
                          class="bg-blue-500/10 rounded-md p-1 my-1"
                        >
                          <p class="text-xs font-light">
                            {{ items.processing_date }}
                          </p>
                          <p class="text-sm font-light text-blue-600">
                            Your order is currently being processed. We'll
                            update you once it's on its way!
                          </p>
                        </div>
                        <div
                          v-if="items.status >= 6"
                          class="bg-blue-500/10 rounded-md p-1 my-1"
                        >
                          <p class="text-xs font-light">
                            {{ items.delivery_date }}
                          </p>
                          <p class="text-sm font-light text-blue-600">
                            Great news! Your order is out for delivery. It'll be
                            with you soon.
                          </p>
                        </div>
                        <div
                          v-if="items.status >= 7"
                          class="bg-blue-500/10 rounded-md p-1 my-1"
                        >
                          <p class="text-xs font-light">
                            {{ items.delivered_date }}
                          </p>
                          <p class="text-sm font-light text-blue-600">
                            Your order has been delivered. We hope you enjoy
                            your purchase!
                          </p>
                        </div>
                      </div>

                      <div
                        v-if="items.status >= 7 && items.status !== 8"
                        v-for="(item, index) in ComentandReview"
                      >
                        <!-- Refund Process -->
                        <div
                          v-if="items.order_detail_id === item.order_detail_id"
                        >
                          <div
                            v-if="
                              isRefundAvailable(items) &&
                              items.video_evidence === null
                            "
                          >
                            <p class="text-xs text-gray-500 pb-2">
                              You have {{ getRemainingDays(items) }} days left
                              to request a refund.
                            </p>
                            <button
                              class="px-3 py-1 bg-red-500 text-white rounded mb-2"
                              @click="openRefundModal(item)"
                            >
                              Request Refund
                            </button>
                          </div>
                          <div
                            v-else-if="
                              items.video_evidence !== null &&
                              items.status !== 14
                            "
                            class="mb-3 flex items-center gap-2"
                          >
                            <p
                              class="bg-green-300/20 w-fit p-1 rounded text-md font-semibold text-green-400 shadow"
                            >
                              Refund Resquest sent
                            </p>
                            <p class="font-bold ml-4">Status:</p>
                            <div class="font-semibold bg-gray-300 p-1 rounded">
                              <p v-if="items.status === 9">Pending</p>
                              <p v-else-if="items.status === 10">Approved</p>
                              <p v-else-if="items.status === 11">In progress</p>
                              <p v-else-if="items.status === 12">Completed</p>
                              <p v-else-if="items.status === 13">Declined</p>
                            </div>
                          </div>
                          <div v-if="refundDetailModal">
                            <p class="text-sm font-semibold mb-2">
                              Refund Process:
                            </p>
                            <div class="mb-2">
                              <label for="refund-video" class="block mb-1"
                                >Upload Video Evidence:</label
                              >
                              <input
                                id="refund-video"
                                type="file"
                                accept="video/*"
                                @change="handleVideoUpload($event)"
                              />
                            </div>
                            <div class="mb-2">
                              <label for="refund-reason" class="block mb-1"
                                >Reason for Refund:</label
                              >
                              <textarea
                                id="refund-reason"
                                v-model="item.reason"
                                placeholder="Enter the reason for refund"
                                class="w-full p-2 rounded"
                              ></textarea>
                            </div>
                            <div class="flex justify-end">
                              <button
                                class="px-3 py-1 bg-blue-500 text-white rounded mr-2"
                                @click="submitRefundRequest(item)"
                              >
                                Submit
                              </button>
                              <button
                                class="px-3 py-1 bg-red-500 text-white rounded"
                                @click="cancelRefundProcess(item)"
                              >
                                Cancel
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- if review sent -->
                      <div v-if="items.comment !== null">
                        <div v-if="!isEditingReview">
                          <div class="ratings flex justify-center">
                            <span
                              v-for="number in 5"
                              :key="`rating-${number}-${index}`"
                              :class="{ active: items.rating >= number }"
                            >
                              <span v-if="items.rating >= number">★</span>
                              <span v-else>☆</span>
                            </span>
                          </div>
                          <p
                            class="text-base font-semibold text-gray-500 px-5 py-2 bg-gray-300 rounded border my-2"
                          >
                            Review Sent: {{ items.comment }}
                          </p>
                          <button
                            class="px-3 py-1 bg-blue-500 text-white rounded"
                            @click="
                              isEditingReview = true;
                              editedRating = items.rating;
                              editedComment = items.comment;
                            "
                          >
                            Edit Review
                          </button>
                        </div>
                        <div v-else>
                          <div class="ratings flex justify-center">
                            <button
                              v-for="number in 5"
                              :key="`rating-${number}-${index}`"
                              @click="editedRating = number"
                              :class="{ active: editedRating >= number }"
                            >
                              <span v-if="editedRating >= number">★</span>
                              <span v-else>☆</span>
                            </button>
                          </div>
                          <textarea
                            v-model="editedComment"
                            placeholder="Edit your comment"
                            class="my-4 p-2 rounded"
                          ></textarea>
                          <div class="flex justify-end">
                            <button
                              class="px-3 py-1 bg-blue-500 text-white rounded mr-2"
                              @click="saveEditedReview(items)"
                            >
                              Save
                            </button>
                            <button
                              class="px-3 py-1 bg-red-500 text-white rounded"
                              @click="isEditingReview = false"
                            >
                              Cancel
                            </button>
                          </div>
                        </div>
                      </div>

                      <div v-if="items.status >= 7 && items.comment === null">
                        <!-- Rating and Comment -->
                        <div
                          class="rating-and-comment mt-4"
                          v-for="(item, index) in ComentandReview"
                        >
                          <div
                            v-if="
                              items.order_detail_id === item.order_detail_id
                            "
                          >
                            <div class="ratings flex justify-center">
                              <button
                                v-for="number in 5"
                                :key="`rating-${number}-${index}`"
                                @click="item.userRating = number"
                                :class="{ active: item.userRating >= number }"
                              >
                                <span v-if="item.userRating >= number">★</span>
                                <span v-else>☆</span>
                              </button>
                            </div>
                            <textarea
                              v-model="item.userComment"
                              placeholder="Leave a comment"
                              class="my-4 p-2 rounded"
                            ></textarea>
                            <button
                              class="px-3 py-1 bg-blue-500 text-white rounded"
                              @click="submitComment(item, index)"
                            >
                              Submit
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="orderData.length === 0">
              <h1
                class="text-base font-semibold text-red-500 px-5 py-2 bg-red-300/10 rounded-full border my-2"
              >
                You have no orders
              </h1>
            </div>

            <hr class="border" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Navigator -->
  <div
    class="text-xs sm:text-sm flex container-fluid shadow-lg bg-gradient-to-r from-blue-600/75 via-violet-500/50 to-orange-500/10 p-2 sm:p-4 pl-2 sm:pl-12 place-items-center cursor-pointer"
  >
    <!-- kulang ng logo dito -->
    <div class="flex sm:ml-10 ml-0">
      <img src="@/assets/logo.jpg" alt="" class="w-12 h-12 rounded-full mr-2" />
    </div>
    <!-- Search bar -->
    <div class="sm:ml-5">
      <div
        class="flex justify-between bg-none lg:bg-slate-100 border shadow-lg rounded-full overflow-hidden"
      >
        <button
          @click="showSearch = true"
          class="w-50 lg:w-80 p-2 hover:bg-gray-300"
        >
          <div class="flex items-center text-white lg:text-black">
            <Icon
              class="text-2xl lg:ml-4"
              icon="material-symbols-light:search"
            />
            <span class="pl-10 hidden lg:flex"> Search any things... </span>
          </div>
        </button>

        <SearchModal
          :is-visible="showSearch"
          @update:isVisible="showSearch = $event"
          @search-completed="handleSearchCompleted"
        ></SearchModal>
      </div>
    </div>
    <!-- right nav -->
    <div class="hidden sm:flex ml-auto items-center sm:mr-12 cursor-pointer">
      <!-- sign in -->
      <div
        v-if="user"
        class="flex items-center gap-2 rounded-full w-full text-white"
        @click="showLogin = true"
      >
        <div
          v-if="userLogin.length === 0"
          class="flex gap-1 bg-blue-600 shadow-xl hover:bg-blue-500 py-2 px-3 rounded-full"
        >
          <Icon icon="bi:person" class="text-lg" />
          <span>Sign in</span>
        </div>
      </div>
      <!-- login modal -->

      <!-- Cart -->
      <div
        :class="
          userLogin.length === 0 ? 'text-slate-800  pointer-events-none' : ''
        "
        @click="showCartFunction"
        class="flex items-center gap-2 text-slate-800 relative hover:bg-slate-500/20 rounded-full py-1 px-2"
      >
        <div v-if="cartItemsValue.length !== 0">
          <Icon
            icon="radix-icons:dot-filled"
            class="text-xl text-red-500 absolute -top-2 left-0"
          />
        </div>
        <Icon icon="ion:cart-outline" class="text-xl" />
        <p class="text-base">Cart</p>
      </div>
      <div
        class="flex gap-2 justify-start items-center relative"
        v-if="userLogin.length !== 0"
      >
        <button
          @click="showCustomerSettings"
          class="flex gap-2 justify-start mx-5 items-center"
        >
          <img
            v-if="profile"
            :src="'data:image/png;base64,' + profile"
            class="w-12 h-12 rounded-full mr-2"
            :alt="userLogin.username"
          />
          <img
            v-else
            src="../assets/profile.jpg"
            class="w-12 h-12 rounded-full mr-4"
            :alt="userLogin.username"
          />

          <div>
            <h1 class="text-slate-800 truncate font-bold">
              {{ userLogin.username }}
            </h1>
          </div>
        </button>

        <div v-if="showSettings" class="absolute show top-11 z-50">
          <div
            class="bg-slate-700 text-slate-100 p-2 rounded-md shadow-xl shadow-slate-800/50 font-semibold"
          >
            <button
              @click="ShowProfile()"
              class="py-2 px-4 w-full bg-slate-900/50 hover:bg-slate-900/75 rounded-md"
            >
              Profile
            </button>
            <button
              @click="Logout()"
              class="py-2 px-4 w-full bg-slate-900/50 hover:bg-slate-900/75 rounded-md my-1"
            >
              Logout
            </button>
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
    <!-- Profile Modal -->
    <div
      v-if="ShowProfileModal"
      class="fixed inset-0 z-50 flex items-center justify-center"
    >
      <div class="absolute inset-0 bg-black opacity-50"></div>
      <div
        class="bg-white p-4 sm:max-w-lg max-w-80 mx-auto rounded-lg shadow-xl z-10"
      >
        <div class="flex justify-between items-start">
          <!-- Close button -->
          <button
            @click="ShowProfileModal = false"
            class="text-black hover:text-gray-700 rounded-full px-3 py-1 bg-slate-300"
          >
            <span class="text-lg">&times;</span>
          </button>
          <!-- Edit button/icon -->
          <button
            @click="toggleEdit"
            class="text-black hover:text-gray-700 rounded px-2 py-1"
          >
            <span
              v-if="isEditing"
              class="text-md hover:text-blue-500 cursor-pointer"
              >Back</span
            >
            <span v-else class="text-md hover:text-blue-500 cursor-pointer"
              >Edit</span
            >
            <!-- Use an icon here if preferred -->
          </button>
        </div>
        <div class="text-center mt-4 m-10">
          <p class="font-semibold text-lg mb-4">Profile Settings</p>
          <div v-if="isEditing" class="space-y-4">
            <!-- Image Upload -->
            <div class="py-2">
              <input
                id="userprofile"
                type="file"
                @change="handleImageChange"
                class="hidden"
                ref="fileInput"
              />
              <!-- Parent div for relative positioning -->
              <div class="mb-6 flex justify-center">
                <div
                  class="relative inline-block"
                  style="width: 60px; height: 60px"
                >
                  <img
                    v-if="showuserprofile"
                    :src="profile"
                    class="object-cover rounded-full shadow"
                    :alt="userLogin.username"
                    style="width: 100%; height: 100%"
                    @click="triggerFileInput"
                  />
                  <img
                    v-else
                    :src="'data:image/png;base64,' + profile"
                    class="object-cover rounded-full shadow"
                    :alt="userLogin.username"
                    style="width: 100%; height: 100%"
                    @click="triggerFileInput"
                  />
                  <!-- Iconify edit icon positioned absolutely within the relative parent -->
                  <div
                    class="absolute bottom-0 right-0 bg-gray-300 rounded-full p-1 cursor-pointer transform translate-x-1/2 -translate-y-1/2"
                    @click="triggerFileInput"
                  >
                    <Icon icon="lucide:edit" />
                  </div>
                </div>
              </div>
            </div>
            <div class="relative">
              <div class="flex justify-end gap-5 items-center">
                <label for="username" class="">Name:</label>
                <input
                  type="text"
                  id="name"
                  v-model="userLogin.username"
                  placeholder="Name"
                  required
                  :class="[
                    'border',
                    'w-full',
                    'p-2',
                    'rounded-md',
                    'my-1',
                    'bg-gray-100',
                    errorMessage.nameErr && userLogin.contact_number.lenght > 0
                      ? 'border-red-500'
                      : userLogin.username.length > 0
                      ? 'border-green-500'
                      : 'border-gray-300',
                  ]"
                />
              </div>
              <p
                class="px-3 py-1 rounded-md text-red-500"
                v-if="errorMessage.nameErr && userLogin.username.length > 0"
              >
                {{ errorMessage.nameErr }}
              </p>
            </div>

            <div class="relative">
              <div class="flex justify-end gap-5 items-center">
                <label for="username" class="">Contact no:</label>
                <div
                  class="flex border w-full p-2 rounded-md my-1 bg-gray-100 items-center"
                >
                  <span class="bg-gray-200 px-2">+63</span>
                  <input
                    type="tel"
                    id="number"
                    v-model="userLogin.contact_number"
                    placeholder="8123456789 or 9123456789"
                    required
                    :class="[
                      'border',
                      'w-full',
                      'p-2',
                      'rounded-md',
                      'my-1',
                      'bg-gray-100',
                      errorMessage.contactNumberErr &&
                      userLogin.contact_number.length > 0
                        ? 'border-red-500'
                        : userLogin.contact_number.length > 0
                        ? 'border-green-500'
                        : 'border-gray-300',
                    ]"
                  />
                </div>
              </div>
              <p
                v-if="
                  errorMessage.contactNumberErr &&
                  userLogin.contact_number.length > 0
                "
                class="text-red-500"
              >
                {{ errorMessage.contactNumberErr }}
              </p>
            </div>

            <div class="flex justify-end gap-5 items-center">
              <label for="address" class="">City:</label>
              <input
                id="address"
                v-model="userLogin.address"
                placeholder="Address"
                class="input border-2 rounded-lg border-gray-300 p-2 w-3/4 focus:outline-none focus:border-blue-500"
              />
            </div>

            <div class="flex justify-end gap-5 items-center">
              <label for="barangay" class="">Barangay:</label>
              <select
                id="barangay"
                class="input border-2 rounded-lg border-gray-300 p-2 w-3/4 focus:outline-none focus:border-blue-500"
                v-model="selectedBarangay"
                required
              >
                <option value="" disabled selected>Select Barangay</option>
                <option
                  v-for="brgy in barangay"
                  :key="brgy.barangay_id"
                  :value="brgy.barangay_id"
                >
                  {{ brgy.name }}
                </option>
              </select>
            </div>

            <div class="flex justify-end gap-5 items-center">
              <label for="address" class="">Street:</label>
              <select
                id="zone"
                v-model="userLogin.Zone"
                required
                class="input border-2 rounded-lg border-gray-300 p-2 w-3/4 focus:outline-none focus:border-blue-500"
              >
                <option value="" disabled selected>Select your street</option>
                <option v-for="zone in 7" :key="zone" :value="'Zone ' + zone">
                  Zone {{ zone }}
                </option>
              </select>
            </div>

            <div class="relative">
              <div class="flex justify-end gap-5 items-center">
                <label for="address" class="">House no:</label>
                <input
                  type="text"
                  id="houseno"
                  v-model="userLogin.House_no"
                  placeholder="House no."
                  required
                  :class="[
                    'border',
                    'w-full',
                    'p-2',
                    'rounded-md',
                    'my-1',
                    'bg-gray-100',
                    errorMessage.houseNumberErr && userLogin.House_no.length > 0
                      ? 'border-red-500'
                      : userLogin.House_no.length > 0
                      ? 'border-green-500'
                      : 'border-gray-300',
                  ]"
                />
              </div>
              <p
                v-if="
                  errorMessage.houseNumberErr && userLogin.House_no.length > 0
                "
                class="text-red-500"
              >
                {{ errorMessage.houseNumberErr }}
              </p>
            </div>

            <div class="text-right mt-4">
              <button
                @click="saveProfile"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                Save
              </button>
            </div>
          </div>

          <div v-else class="space-y-4">
            <!-- Image Upload -->
            <div class="py-2">
              <!-- Display the selected or default image -->
              <div class="mb-6 flex justify-center">
                <img
                  v-if="profile"
                  :src="'data:image/png;base64,' + profile"
                  class="object-cover rounded-full shadow"
                  :alt="userLogin.username"
                  style="width: 60px; height: 60px"
                />
                <img
                  v-else
                  src="../assets/profile.jpg"
                  class="object-cover rounded-full shadow"
                  :alt="userLogin.username"
                  style="width: 60px; height: 60px"
                />
              </div>
            </div>
            <div class="flex items-center justify-between">
              <span class="mr-2">Name:</span>
              <p class="border-2 rounded-lg border-gray-300 p-2 w-3/4">
                {{ userLogin.username }}
              </p>
            </div>

            <div class="flex items-center justify-between">
              <span class="mr-2">Contact No:</span>
              <p class="border-2 rounded-lg border-gray-300 p-2 w-3/4">
                {{ userLogin.contact_number }}
              </p>
            </div>

            <div class="flex items-center justify-between">
              <span class="mr-2">City:</span>
              <p class="border-2 rounded-lg border-gray-300 p-2 w-3/4">
                {{ userLogin.address }}
              </p>
            </div>

            <div class="flex items-center justify-between">
              <span class="mr-2">Barangay:</span>
              <p class="border-2 rounded-lg border-gray-300 p-2 w-52">
                {{ userLogin.name }}
              </p>
            </div>

            <div class="flex items-center justify-between">
              <span class="mr-2">Street:</span>
              <p class="border-2 rounded-lg border-gray-300 p-2 w-52">
                {{ userLogin.Zone }}
              </p>
            </div>

            <div class="flex items-center justify-between">
              <span class="mr-2">House no:</span>
              <p class="border-2 rounded-lg border-gray-300 p-2 w-52">
                {{ userLogin.House_no }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--  -->
    <!-- cart modal -->

    <!-- Hamburger Button for Small Screens -->
    <div
      class="sm:hidden flex items-center text-white cursor-pointer ml-auto"
      @click="toggleSidebar"
    >
      <Icon icon="heroicons-solid:menu" class="text-4xl" />
    </div>

    <!-- Sidebar for Small Screens -->
    <div
      :class="{
        'translate-x-0': isSidebarOpen,
        'translate-x-full': !isSidebarOpen,
      }"
      class="sm:hidden fixed w-72 inset-y-0 right-0 bg-sky-800 z-40 transition-transform duration-300 ease-in-out"
    >
      <div class="flex justify-between items-center p-4">
        <div v-if="userLogin.length !== 0">
          <button
            @click="ShowProfile()"
            class="flex gap-2 justify-start mx-5 items-center"
          >
            <img
              v-if="profile"
              :src="'data:image/png;base64,' + profile"
              class="w-12 h-12 rounded-full mr-2"
              :alt="userLogin.username"
            />
            <img
              v-else
              src="../assets/profile.jpg"
              class="w-12 h-12 rounded-full mr-4"
              :alt="userLogin.username"
            />
          </button>
        </div>
        <!-- Close Button -->
        <div
          class="cursor-pointer text-red-400 bg-red-500/20 rounded-full p-2"
          @click="toggleSidebar"
        >
          <Icon icon="heroicons-solid:x" class="text-2xl" />
        </div>
      </div>
      <div class="px-4">
        <!-- Sidebar Content -->
        <div class="flex flex-col items-start">
          <!-- track your order -->
          <div
            @click="orderTracking"
            :class="
              userLogin.length === 0 ? 'text-blue-500 pointer-events-none' : ''
            "
            class="flex items-center gap-2 p-2 rounded-md hover:bg-slate-800/50 w-full text-white hover:font-bold"
          >
            <Icon icon="fluent:vehicle-bus-24-regular" class="text-xl" />
            <h1 class="text-base font-medium">Track your order</h1>
          </div>

          <!-- Cart -->
          <div
            @click="showCartFunction"
            :class="
              userLogin.length === 0 ? 'text-blue-500 pointer-events-none' : ''
            "
            class="flex items-center gap-2 p-2 rounded-md hover:bg-slate-800/50 w-full text-white hover:font-bold"
          >
            <Icon icon="ion:cart-outline" class="text-xl" />
            <h1 class="text-base font-medium">Cart</h1>
          </div>

          <div>
            <!-- logout -->
            <div
              class="flex items-center gap-2 p-2 rounded-md hover:bg-slate-800/50 w-full text-white hover:font-bold"
              @click="Logout()"
              v-if="userLogin.length !== 0"
            >
              <Icon icon="bi:person" class="text-xl" />
              <h1 class="text-base font-medium">Logout</h1>
            </div>
            <!-- Sign In -->
            <div
              class="flex items-center gap-2 p-2 rounded-md hover:bg-slate-800/50 w-full text-white hover:font-bold"
              @click="HamburgerSignin"
              v-else
            >
              <Icon icon="bi:person" class="text-xl" />
              <h1 class="text-base font-medium">Sign in</h1>
            </div>
            <div class="flex justify-center ml-4 gap-4 mt-10">
              <div
                class="flex items-center text-slate-100 underline hover:text-violet-500"
                v-if="userLogin.length === 0"
              >
                <RouterLink
                  to="/seller_dashboard"
                  :class="$route.name === 'seller_dashboard'"
                  >Sign in as Seller</RouterLink
                >
              </div>
              <div
                class="flex items-center text-slate-100 underline hover:text-violet-500"
                v-if="userLogin.length === 0"
              >
                <RouterLink
                  to="/rider_home"
                  :class="$route.name === 'rider_home'"
                  >Sign in as Rider</RouterLink
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- cart modal -->
  <div
    v-if="showCart"
    class="flex justify-center items-center"
    @click="closeCart"
  >
    <div class="cart-modal z-30" @click.stop>
      <div
        class="bg-slate-300 border border-slate-900/20 shadow-lg px-3 py-2 rounded-lg"
      >
        <div class="flex justify-end">
          <div
            @click="closeCart"
            class="bg-slate-600/20 rounded-full text-red-500 shadow p-2"
          >
            <Icon icon="iconamoon:close-bold" />
          </div>
        </div>
        <div>
          <h1 class="font-semibold text-lg">Cart</h1>
        </div>
        <div
          v-if="cartItemsValue.length !== 0"
          class="p-2 bg-slate-500/10 h-96 overflow-scroll overflow-x-hidden rounded-md shadow-sm"
        >
          <div class="flex gap-2 font-semibold mb-2">
            <input type="checkbox" @change="checkAll" />Select All
          </div>
          <div v-for="items in cartItemsValue" :key="items">
            <div class="my-1 relative">
              <div class="flex justify-start items-center gap-2">
                <input
                  type="checkbox"
                  class="cursor-pointer"
                  :checked="isChecked(items.product_id)"
                  @click.stop="toggleCheckbox(items.product_id)"
                  @pointerup.stop="toggleCheckbox(items.product_id)"
                />

                <button
                  @click.stop="deleteCartItems(items.cart_item_id)"
                  @pointerup.stop="deleteCartItems(items.cart_item_id)"
                  class="flex my-1 absolute top-0 right-0 text-red-500 p-1 rounded-full bg-slate-400/75 shadow-sm"
                >
                  <Icon icon="ic:round-delete" class="text-lg text-red-500" />
                </button>

                <div
                  @click.stop="showModal(items)"
                  @pointerup.stop="showModal(items)"
                >
                  <img
                    :src="'data:image/png;base64,' + items.image"
                    alt=""
                    class="w-16 h-16 rounded-md"
                  />
                </div>
                <div>
                  <h1 class="font-bold text-xs">
                    {{ items.product_name }}
                  </h1>
                  <p class="text-xs font-semibold">{{ items.totalPrice }}</p>

                  <div class="flex gap-2">
                    <div
                      class="flex justify-center items-center gap-2 font-semibold text-slate-800"
                    >
                      <p>Qtty:</p>
                      <p
                        class="p-0.5 flex justify-center items-center w-7 rounded-md border-blue-500/50 border"
                      >
                        {{ items.quantity }}
                      </p>
                    </div>
                    <button
                      @click.stop="decrement(items.product_id)"
                      @pointerup.stop="decrement(items.product_id)"
                      :disabled="items.quantity === 1"
                      class="p-0.5 flex justify-center items-center w-7 rounded-full border"
                    >
                      <Icon icon="tabler:minus" />
                    </button>
                    <button
                      @click.stop="increment(items.product_id)"
                      @pointerup.stop="increment(items.product_id)"
                      :disabled="items.quantity === items.stock"
                      class="p-0.5 flex justify-center items-center w-7 rounded-full border"
                    >
                      <Icon icon="mingcute:add-line" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <hr />
          </div>
          <!--  -->
          <div
            v-if="atLeastOneItemChecked || allItemsChecked"
            class="gap-2 my-2 shadow justify-center w-full items-center"
          >
            <button
              @click="checkout"
              class="flex justify-center w-full items-center gap-2 bg-blue-500 text-gray-100 p-2 rounded-md"
            >
              <Icon
                icon="ic:outline-shopping-cart-checkout"
                class="text-lg"
              />Checkout
            </button>
          </div>
          <!--  -->
        </div>
        <div v-if="cartItemsValue.length === 0">
          <p class="px-5 py-2 text-red-500 bg-red-400/10 rounded-full shadow">
            No items
          </p>
        </div>
      </div>
    </div>
  </div>
  <product-modal
    :is-visible="isModalVisible"
    :product="selectedProduct"
    @update:isVisible="isModalVisible = $event"
  ></product-modal>

  <!-- payment popup -->
  <div
    v-if="showPayment"
    class="justify-center items-center flex w-full h-full overflow-scroll"
  >
    <div
      @click="closePayment()"
      class="fixed z-40 w-full top-0 left-0 h-full backdrop-blur bg-slate-900 bg-opacity-50"
    ></div>
    <div
      class="fixed z-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 justify-center items-center flex"
    >
      <div class="overflow-scroll bg-slate-100 h-[500px] rounded-md">
        <div class="p-5 bg-slate-100 rounded-md h-full w-96">
          <div class="h-full">
            <div class="flex justify-end">
              <div
                @click="closePayment"
                class="bg-slate-600/20 rounded-full text-red-500 shadow p-2"
              >
                <Icon icon="iconamoon:close-bold" />
              </div>
            </div>
            <h1 class="font-semibold text-lg">Checkout</h1>
            <div class="bg-slate-200 rounded-md p-2 my-1">
              <span class="text-slate-900 text-sm">Customer Information</span>
              <div v-if="userLogin" class="text-xs text-slate-800">
                <p>Name: {{ userLogin.username }}</p>
                <p>Contact No: {{ userLogin.contact_number }}</p>
                <p>Address: {{ userLogin.address }}, {{ userLogin.name }}</p>
              </div>
            </div>
            <div class="bg-slate-200 rounded-md p-2">
              <div
                class="my-1"
                v-for="(items, index) in itemsToCheckout"
                :key="index"
              >
                <div>
                  <div class="flex gap-2 justify-start items-center">
                    <div>
                      <img
                        :src="'data:image/png;base64,' + items.image"
                        alt=""
                        class="w-10 h-10 rounded-md"
                      />
                    </div>
                    <div class="w-full">
                      <div class="flex justify-between">
                        <span class="text-sm font-semibold">{{
                          items.product_name
                        }}</span>
                        <span
                          class="text-sm font-semibold bg-slate-600/30 rounded p-1 cursor-pointer"
                          @click="toggleStoreModal(items.store_id)"
                          >{{ items.store_name }}</span
                        >
                        <!-- Modal Element -->
                        <div
                          v-if="openModalId === items.store_id"
                          class="modal absolute inset-x-0 mx-3 md:right-0 md:mt-1 md:w-64 md:translate-x-full bg-white shadow-lg rounded-lg p-4 transition-transform"
                        >
                          <div class="flex justify-between">
                            <h3
                              class="font-semibold text-lg flex justify-center text-blue-500"
                            >
                              {{ selectedItem.store_name }}
                            </h3>
                            <button
                              @click="closeStoreModal"
                              class="rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300"
                            >
                              <Icon icon="iconamoon:close-bold" />
                            </button>
                          </div>
                          <div class="mt-2 font-semibold">
                            <p class="text-sm mt-1">
                              Address: {{ selectedItem.store_address }}
                            </p>
                            <p class="text-sm mt-1">
                              Contact no:
                              {{ selectedItem.store_contact_number }}
                            </p>
                          </div>
                        </div>
                      </div>
                      <p class="text-xs">{{ items.price }}</p>
                      <p class="text-xs">x{{ items.quantity }}</p>
                    </div>
                  </div>
                  <div class="my-2">
                    <div
                      class="flex gap-2 justify-between items-center border-y p-2 border-cyan-500/50 bg-cyan-300/10"
                    >
                      <span class="text-sm font-medium">Shipping Fee</span>
                      <p class="text-xs">
                        {{ items.shippingFee.toFixed(2) }}
                      </p>
                    </div>
                  </div>
                </div>
                <hr class="border my-2 border-gray-700/10" />
              </div>
            </div>
            <div class="my-1">
              <div
                class="flex gap-2 justify-between items-center p-2 rounded-md bg-blue-300/10"
              >
                <span class="text-xs"
                  >Order Total <span>({{ totalQuantity }})</span> Item</span
                >
                <p class="text-sm font-medium text-red-500">
                  ₱{{ priceTotalAll }}
                </p>
              </div>
            </div>
            <div class="my-1">
              <div
                class="flex gap-2 justify-between items-center p-2 rounded-md bg-blue-500/10"
              >
                <div class="w-full">
                  <span class="text-sm font-semibold"> Payment Option</span>
                  <div class="block w-full my-2">
                    <button
                      @click="onDelivery"
                      :class="{
                        ' shadow-md shadow-green-500/50 border-green-500':
                          selectedPayment === 'cash on delivery',
                      }"
                      class="p-2 bg-green-500/10 flex justify-center items-center gap-1 rounded-full my-1 border border-gray-600/50 w-full text-green-600 font-medium text-sm"
                    >
                      <Icon icon="iconoir:cash-solid" class="text-lg" /> Cash on
                      Delivery
                    </button>
                    <button
                      @click="onPyment"
                      disabled
                      :class="{
                        ' shadow-md shadow-blue-500/50 border-blue-500':
                          selectedPayment === 'payment',
                      }"
                      class="p-2 bg-green-blue/10 rounded-full flex justify-center items-center gap-1 my-1 border border-gray-600/50 w-full text-gray-600 font-medium text-sm"
                    >
                      <Icon icon="material-symbols:wallet" class="text-lg" />
                      Payment Center/E-wallet
                    </button>
                    <button
                      @click="onCredit"
                      disabled
                      :class="{
                        ' shadow-md shadow-orange-500/50 border-orange-500':
                          selectedPayment === 'credit',
                      }"
                      class="p-2 bg-green-orange/10 rounded-full flex justify-center items-center gap-1 my-1 border border-gray-600/50 w-full text-gray-600 font-medium text-sm"
                    >
                      <Icon
                        icon="material-symbols:credit-card"
                        class="text-lg"
                      />
                      Credit Card
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="my-3">
              <button
                :disabled="selectedPayment === ''"
                :class="{
                  ' cursor-not-allowed opacity-75': selectedPayment === '',
                }"
                @click="submitOrder"
                class="w-full rounded-full bg-blue-500 p-2 text-slate-100 text-lg font-semibold shadow-md shadow-blue-500/50"
              >
                Place Order
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Header from "../scripts/Header";
import { computed } from "vue";
export default {
  ...Header,
};
</script>
<style scoped>
.ratings button {
  margin: 0 5px;
  background-color: transparent;
  border: none;
  cursor: pointer;
  font-size: 24px;
}

.ratings button.active {
  color: #ffcc00; /* Gold color for active stars */
}

textarea {
  display: block;
  margin-top: 20px;
  width: 100%;
}
.text-custom-size {
  font-size: 0.7rem; /* default size */
}

@media (min-width: 640px) {
  /* Tailwind's 'sm' breakpoint */
  .text-custom-size {
    font-size: 0.75rem; /* smaller size for 'sm' screens and up */
  }
}
@media (min-width: 768px) {
  .cart-modal {
    position: absolute;
    top: 8rem; /* equivalent to tailwind top-32 */
    right: 0.75rem; /* equivalent to tailwind right-3 */
    width: 24rem; /* equivalent to tailwind w-96 */
  }
}

/* Mobile and small devices (below 768px width) */
@media (max-width: 767px) {
  .cart-modal {
    position: fixed; /* Fixed to make sure it stays in view on scroll */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%; /* Responsive width */
    z-index: 30; /* High z-index to keep it above other content */
  }
}
/* Base styles */
.modal {
  transition: transform 0.3s ease-in-out;
}

@media (max-width: 768px) {
  .modal {
    /* On small screens, make the modal take up most of the screen width */
    width: calc(100% - 1.5rem); /* 1.5rem accounts for the margin */
    transform: translateY(
      -50%
    ); /* Adjust translate for better centering on small screens */
    top: 50%; /* Center vertically */
    right: 50%;
    translate: translateX(50%);
  }
}

/* Desktop specific styles */
@media (min-width: 769px) {
  .modal {
    width: 256px; /* Fixed width on larger screens */
    transform: translateX(100%);
    top: 1rem; /* Adjust top margin for desktop */
    right: 1rem; /* Align to the right on desktop */
  }
}
</style>
