// src/router/index.js - Fixed with root redirect and better navigation
import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

// Layouts
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";

// Views
import Login from "@/views/auth/Login.vue";
import Register from "@/views/auth/Register.vue";
import Tasks from "@/views/Tasks.vue";

const routes = [
  {
    path: "/",
    redirect: "/tasks", // ✅ Added root redirect to tasks page
  },
  {
    path: "/login",
    component: AuthLayout,
    children: [
      {
        path: "",
        name: "Login",
        component: Login,
        meta: { guest: true },
      },
    ],
  },
  {
    path: "/register",
    component: AuthLayout,
    children: [
      {
        path: "",
        name: "Register",
        component: Register,
        meta: { guest: true },
      },
    ],
  },
  {
    path: "/tasks",
    component: DefaultLayout,
    children: [
      {
        path: "",
        name: "Tasks",
        component: Tasks,
        meta: { requiresAuth: true },
      },
    ],
  },
  {
    path: "/:pathMatch(.*)*", // ✅ Added 404 catch-all route
    redirect: "/tasks",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guards: control access to routes based on user status
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // ✅ Added debugging logs
  console.log(
    "Navigation to:",
    to.path,
    "Auth status:",
    authStore.isAuthenticated
  );

  // If route needs login and user isn't logged in, go to login
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    console.log("Redirecting to login - not authenticated");
    return next("/login");
  }
  // If route needs admin and user isn't admin, go to tasks
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    console.log("Redirecting to tasks - not admin");
    return next("/tasks");
  }
  // If route is for guests and user is logged in, go to tasks
  if (to.meta.guest && authStore.isAuthenticated) {
    console.log("Redirecting to tasks - already authenticated");
    return next("/tasks");
  }
  next();
});

export default router;
