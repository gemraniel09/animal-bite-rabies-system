<script setup lang="ts">
import {
  LayoutDashboard,
  Newspaper,
  Construction,
  BadgeDollarSign,
  Lock,
} from "lucide-vue-next";
import type { MenuList } from "~/lib/types";
import SideBarMenuList from "./SideBarMenuList.vue";
const auth = useAuthStore();
const role = computed(() => auth.roleName);
const menuList: Record<string, MenuList[]> = {
  admin: [
    {
      name: "Dashboard",
      icon: LayoutDashboard,
      href: `/admin/dashboard`,
    },
    {
      name: "Transaction",
      icon: BadgeDollarSign,
      href: `/`,
      children: [
        { name: "New Transaction", href: `/transaction/new` },
        { name: "Follow-up Transaction", href: `/transaction/follow-up` },
      ],
    },
    {
      name: "Report",
      icon: Newspaper,
      href: "/",
      children: [
        { name: "Patients", href: `/admin/report/patients` },
        { name: "Transactions", href: `/admin/report/transactions` },
        { name: "Analytic Report", href: `/admin/report/analytic` },
      ],
    },
    {
      name: "Maintenance",
      icon: Construction,
      href: "/",
      children: [
        { name: "Users", href: `/admin/maintenance/users` },
        { name: "Animals", href: `/admin/maintenance/animals` },
        { name: "Brands", href: `/admin/maintenance/brands` },
      ],
    },
  ],
  nurse: [
    {
      name: "Transaction",
      icon: BadgeDollarSign,
      href: `/`,
      children: [
        { name: "New Transaction", href: `/transaction/new` },
        { name: "Follow-up Transaction", href: `/transaction/follow-up` },
      ],
    },
    {
      name: "Report",
      icon: Newspaper,
      href: "/",
      children: [
        { name: "Patients", href: `/admin/report/patients` },
        { name: "Transactions", href: `/admin/report/transactions` },
      ],
    },
  ],
  guest: [
    {
      name: "Login",
      icon: Lock,
      href: `/login`,
    },
  ],
};
defineProps<{
  drawerOpen: boolean;
  close?: () => void;
}>();
const route = useRoute();
</script>
<template>
  <div class="flex h-[calc(100vh-70px)] relative">
    <!-- Fixed position sidebar -->
    <div
      class="fixed top-[70px] bottom-0 z-10 transform transition-transform duration-300 ease-out"
      :class="[drawerOpen ? 'translate-x-0' : '-translate-x-full']"
    >
      <div class="card card-border w-72 h-full bg-base-250">
        <div class="card-body">
          <div>
            <h2 class="card-title">{{ role }}</h2>
            <SideBarMenuList
              :path="route.path"
              :menu-list="menuList[role] ?? menuList['guest']"
            />
          </div>
        </div>
      </div>
    </div>
    <div
      class="transition-all duration-300 ease-out w-full h-full mt-18"
      :class="[drawerOpen ? 'pl-72' : 'pl-0']"
    >
      <slot />
    </div>
  </div>
</template>

<style scoped>
/* Optional: Add some hardware acceleration to improve performance */
.fixed {
  will-change: transform;
  backface-visibility: hidden;
}
</style>
