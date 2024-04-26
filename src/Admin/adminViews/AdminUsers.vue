<template>
  <h2 class="md:text-2xl text-lg font-bold tracking-tight text-sky-900">
    Users
  </h2>
  <div class="py-3">
    <div class="flex gap-5 p-2">
      <RouterLink
        to="/admin_dashboard_customers"
        :class="{
          'bg-sky-800/20 text-sky-800': $route.name === 'admin_dashboard_customers',
          'text-sky-800 bg-slate-300/10': $route.name !== 'admin_dashboard_customers',
          'px-3 py-2 font-semibold shadow rounded-md': true,
        }"
      >
        Customers
      </RouterLink>
      <RouterLink
        to="/admin_dashboard_sellers"
        :class="{
          'bg-sky-800/20 text-sky-800': $route.name === 'admin_dashboard_sellers',
          'text-sky-800 bg-slate-300/10': $route.name !== 'admin_dashboard_sellers',
          'px-3 py-2 font-semibold shadow rounded-md': true,
        }"
      >
        Sellers
      </RouterLink>
      <RouterLink
        to="/admin_dashboard_riders"
        :class="{
          'bg-sky-800/20 text-sky-800': $route.name === 'admin_dashboard_riders',
          'text-sky-800 bg-slate-300/10': $route.name !== 'admin_dashboard_riders',
          'px-3 py-2 font-semibold shadow rounded-md': true,
        }"
      >
        Riders
      </RouterLink>
    </div>
    <div>
      <!--  -->
      <router-view> </router-view>
      <!--  -->
    </div>
  </div>
</template>
