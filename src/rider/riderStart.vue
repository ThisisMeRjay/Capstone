<template>
    <div class="text-gray-700 bg-blue-50 h-screen">
      <header class="p-2 flex justify-center items-center shadow bg-blue-200/10">
        <h1
          class="text-2xl text-transparent bg-gradient-to-r from-blue-500 from-10% via-violet-500 via-30% to-orange-500 to-90% bg-clip-text font-extrabold"
        >
          Welcome Rider!
        </h1>
      </header>
  
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