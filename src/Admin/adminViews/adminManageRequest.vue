<template>
    <h2 class="md:text-2xl text-lg font-bold tracking-tight text-sky-900">
      Requests
    </h2>
    <div class="py-3">
      <div class="flex gap-5 p-2">
        <RouterLink
          to="/manage_request_seller"
          :class="{
            'bg-sky-800/20 text-sky-800': $route.name === 'manage_request_seller',
            'text-sky-800 bg-slate-300/10': $route.name !== 'manage_request_seller',
            'px-3 py-2 font-semibold shadow rounded-md': true,
          }"
        >
          Seller
        </RouterLink>
        <RouterLink
          to="/manage_request_rider"
          :class="{
            'bg-sky-800/20 text-sky-800': $route.name === 'manage_request_rider',
            'text-sky-800 bg-slate-300/10': $route.name !== 'manage_request_rider',
            'px-3 py-2 font-semibold shadow rounded-md': true,
          }"
        >
          Rider
        </RouterLink>
      </div>
      <div>
        <!--  -->
        <router-view> </router-view>
        <!--  -->
      </div>
    </div>
  </template>
  