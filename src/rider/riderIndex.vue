<template>
    <div class="text-gray-700 bg-blue-50 h-screen">
      <header class="p-2 flex justify-between items-center shadow bg-blue-200/10">
        <h1
          class="text-2xl text-transparent bg-gradient-to-r from-blue-500 from-10% via-violet-500 via-30% to-orange-500 to-90% bg-clip-text font-extrabold"
        >
          Welcome Rider!
        </h1>
        <Icon
          @click="showAside"
          icon="gg:menu-right"
          class="text-2xl text-blue-500"
        />
      </header>
  
      <div>
        <div
          @click="showAside"
          v-if="showBar"
          class="backdrop-blur-sm bg-gray-700/10 fixed z-10 top-0 left-0 w-full h-full"
        ></div>
        <aside
          v-if="showBar"
          class="bg-gray-200 fixed z-20 top-0 right-0 w-72 h-screen"
        >
          <div class="flex justify-between items-center shadow p-2">
            <h1 class="font-semibold text-lg">Menu</h1>
            <div
              @click="showAside"
              class="p-2 rounded-full bg-gray-700/20 text-red-500"
            >
              <Icon icon="ic:round-close" class="text-xl" />
            </div>
          </div>
          <div>
            <RouterLink to="/rider_home">
              <div
                class="w-full hover:bg-gray-700/10 transition hover:shadow hover:text-blue-500 hover:font-medium p-2 mt-3"
              >
                <h1 class="" @click="showBar=false">home</h1>
              </div>
            </RouterLink>
            <RouterLink to="/menu2">
              <div
                class="w-full hover:bg-gray-700/10 transition hover:shadow hover:text-blue-500 hover:font-medium p-2"
              >
                <h1 class="" @click="showBar=false">delivery</h1>
              </div>
            </RouterLink>
            <RouterLink to="/menu3">
              <div
                class="w-full hover:bg-gray-700/10 transition hover:shadow hover:text-blue-500 hover:font-medium p-2"
              >
                <h1 class="" @click="showBar=false">return</h1>
              </div>
            </RouterLink>
          </div>
        </aside>
      </div>
  
      <main>
        <!--  -->
        <router-view> </router-view>
        <!--  -->
      </main>
    </div>
  </template>
  <script>
  import { Icon } from "@iconify/vue";
  import { ref } from "vue";
  export default {
    components: {
      Icon,
    },
    setup() {
      const showBar = ref(false);
  
      const showAside = () => {
        showBar.value = !showBar.value;
      };
  
      return { showBar, showAside };
    },
  };
  </script>