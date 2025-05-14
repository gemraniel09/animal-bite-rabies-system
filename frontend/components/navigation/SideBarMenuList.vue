<script setup lang="ts">
import type { MenuList } from "~/lib/types";
const props = defineProps<{
  menuList: MenuList[];
  path: string;
}>();
</script>
<template>
  <ul class="menu w-full rounded-box">
    <li v-for="item in props.menuList" :key="item.name" class="mb-1">
      <NuxtLink
        v-if="!item.children"
        :href="item.href"
        class="flex items-center gap-3 px-4 py-2 hover:bg-base-300"
        :class="path === item.href && 'bg-base-200'"
      >
        <component :is="item.icon" class="h-5 w-5" />
        <span>{{ item.name }}</span>
      </NuxtLink>

      <details v-else open class="w-full">
        <summary
          class="flex items-center gap-3 px-4 py-2 hover:bg-base-200 cursor-pointer"
        >
          <component :is="item.icon" class="h-5 w-5" />
          <span>{{ item.name }}</span>
        </summary>
        <div class="pl-4 mt-1">
          <SideBarMenuList :path="path" :menu-list="item.children" />
        </div>
      </details>
    </li>
  </ul>
</template>
