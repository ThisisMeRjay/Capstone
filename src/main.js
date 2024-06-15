// Temporarily override console methods in development
// if (true) {
//   const noop = () => {};
//   console.log = noop;
//   console.info = noop;
//   console.warn = noop;
//   console.error = noop;
// }

// import "./style.css";
// import { createApp } from "vue";
// import App from "./App.vue";
// import router from "./router";

// const app = createApp(App);
// app.use(router);
// app.mount("#app");

import "./style.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

const app = createApp(App);

app.use(router);

app.mount("#app");