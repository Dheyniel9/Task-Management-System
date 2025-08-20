<!-- src/layouts/AuthLayout.vue -->
<!--
  This layout is used for authentication pages like login and register.
  It centers the content on the page with a nice background.
-->
<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
      <router-view />
    </div>
  </div>
</template>
