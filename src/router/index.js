import { createRouter, createWebHistory } from "vue-router";
import Customer from "../views/Customer.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      redirect: "/home",
    },
    {
      path: "/home",
      name: "home",
      component: Customer,
    },
    {
      path: "/products",
      name: "products",
      component: () => import("../components/Products.vue"),
    },
    {
      path: "/search_products",
      name: "search_products",
      component: () => import("../views/SearchProducts.vue"),
    },
    {
      path: "/admin_dashboard",
      name: "admin_dashboard",
      component: () => import("../Admin/adminViews/adminDashboard.vue"),
      children: [
        {
          path: "",
          redirect: { name: "admin_dashboard_home" },
        },
        {
          path: "/manage_request",
          name: "manage_request",
          component: () => import("../Admin/adminViews/adminManageRequest.vue"),
          children: [
            {
              path: "",
              redirect: { name: "manage_request_seller" },
            },
            {
              path: "/manage_request_rider",
              name: "manage_request_rider",
              component: () => import("../Admin/adminViews/riderRequest.vue"),
            },
            {
              path: "/manage_request_seller",
              name: "manage_request_seller",
              component: () => import("../Admin/adminViews/sellerRequest.vue"),
            },
          ],
        },
        {
          path: "/admin_dashboard_home",
          name: "admin_dashboard_home",
          component: () => import("../Admin/adminViews/adminDashboardHome.vue"),
        },
        {
          path: "/users", // Example nested route
          name: "users",
          component: () => import("../Admin/adminViews/AdminUsers.vue"),
          children: [
            {
              path: "",
              redirect: { name: "admin_dashboard_customers" },
            },
            {
              path: "/admin_dashboard_customers",
              name: "admin_dashboard_customers",
              component: () =>
                import("../Admin/adminViews/adminManageCustomers.vue"),
            },
            {
              path: "/admin_dashboard_sellers",
              name: "admin_dashboard_sellers",
              component: () =>
                import("../Admin/adminViews/adminManageSellers.vue"),
            },
            {
              path: "/admin_dashboard_riders",
              name: "admin_dashboard_riders",
              component: () =>
                import("../Admin/adminViews/adminManageRiders.vue"),
            },
          ],
        },
        {
          path: "/admin_refund",
          name: "admin_refund",
          component: () => import("../Admin/adminViews/refundRequest.vue"),
        },
        {
          path: "/admin_delivery",
          name: "admin_delivery",
          component: () => import("../Admin/adminViews/adminDelivery.vue"),
        },
      ],
    },
    {
      path: "/seller_dashboard",
      name: "seller_dashboard",
      component: () => import("../Sellers/sellersViews/sellerDashboard.vue"),
      children: [
        {
          path: "",
          redirect: { name: "seller_products" },
        },
        {
          path: "/seller_products",
          name: "seller_products",
          component: () => import("../Sellers/sellersViews/sellerProducts.vue"),
        },
        {
          path: "/seller_Add_products",
          name: "seller_Add_products",
          component: () =>
            import("../Sellers/sellersViews/sellerAddProducts.vue"),
        },

        {
          path: "/seller_order",
          name: "seller_order",
          component: () =>
            import("../Sellers/sellersViews/sellerOrderManagement.vue"),
        },

        {
          path: "/seller_product_list",
          name: "seller_product_list",
          component: () =>
            import("../Sellers/sellersViews/sellerProductList.vue"),
        },
      ],
    },
    {
      path: "/rider_index",
      name: "rider_index",
      component: () => import("../rider/riderIndex.vue"),
      children: [
        {
          path: "",
          redirect: { name: "rider_home" },
        },
        {
          path: "/rider_home",
          name: "rider_home",
          component: () => import("../rider/views/riderHome.vue"),
        },
        {
          path: "/rider_history",
          name: "rider_history",
          component: () => import("../rider/views/riderHistory.vue"),
        },
        {
          path: "/rider_refund",
          name: "rider_refund",
          component: () => import("../rider/views/riderRefund.vue"),
        },
      ],
    },
    {
      path: "/rider_start",
      name: "rider_start",
      component: () => import("../rider/riderStart.vue"),
      children: [
        {
          path: "",
          redirect: { name: "rider_login" },
        },
        {
          path: "/rider_login",
          name: "rider_login",
          component: () => import("../rider/riderLogin.vue"),
        },
        {
          path: "/rider_register",
          name: "rider_register",
          component: () => import("../rider/riderRegister.vue"),
        },
      ],
    },
    {
      path: "/seller_index",
      name: "seller_index",
      component: () => import("../Sellers/sellerIndex.vue"),
      children: [
        {
          path: "",
          redirect: { name: "seller_login" },
        },
        {
          path: "/seller_login",
          name: "seller_login",
          component: () =>
            import("../Sellers/sellersComponents/sellerLogin.vue"),
        },
        {
          path: "/seller_register",
          name: "seller_register",
          component: () =>
            import("../Sellers/sellersComponents/sellerregister.vue"),
        },
      ],
    },
    {
      path: "/:catchAll(.*)",
      name: "notFound",
      component: () => import("../views/NotFound.vue"),
    },
  ],
});

export default router;
