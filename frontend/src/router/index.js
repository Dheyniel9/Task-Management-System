import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

// Layouts
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";

// Views
import Login from "@/views/auth/Login.vue";
import Register from "@/views/auth/Register.vue";
import Tasks from "@/views/Tasks.vue";

// Admin Views
import AdminDashboard from "@/views/admin/AdminDashboard.vue";
import SiteAdministration from "@/views/admin/SiteAdministration.vue";

const routes = [
  {
    path: "/",
    redirect: "/tasks",
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
    path: "/admin",
    component: DefaultLayout,
    children: [
      {
        path: "",
        name: "AdminDashboard",
        component: AdminDashboard,
        meta: {
          requiresAuth: true,
          requiresAdmin: true,
          title: "Admin Dashboard"
        },
      },
      // Add the Site Administration route
      {
        path: "site",
        name: "SiteAdministration",
        component: SiteAdministration,
        meta: {
          requiresAuth: true,
          requiresAdmin: true,
          title: "Site Administration"
        },
      },
    ],
  },
  {
    path: "/:pathMatch(.*)*",
    redirect: "/tasks",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Wait for user info to be loaded if not present but token exists
  if (!authStore.user && authStore.token) {
    await authStore.fetchUser();
  }

  // If admin is authenticated and tries to access /tasks or /, redirect to /admin
  if (
    authStore.isAuthenticated &&
    authStore.isAdmin &&
    (to.path === '/tasks' || to.path === '/')
  ) {
    return next('/admin');
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next('/login');
  }
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    return next('/tasks');
  }
  if (to.meta.guest && authStore.isAuthenticated) {
    return next('/tasks');
  }
  next();
});

export default router;