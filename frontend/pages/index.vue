<script setup lang="ts">
import type {Patient} from "~/lib/types";

const isActive = ref(false);
const { toast } = useToast();
const loading = ref(false);

const authStore = useAuthStore();

const submit = async (method: string) => {
  loading.value = true;
  try {
    const data = await useApiFetch<{ status: string }>("/api/test", { method });
    console.log(data);
    toast(`API call with method ${method} was successful`, data.status);
  } catch (error) {
    toast(`API call with method ${method} failed`, "error");
  }
  loading.value = false;
};
const getUser = async () => {
  try {
    const user = await useApiFetch("/api/user");
    console.log(user);
    toast(`Hello user: ${user.username}`, "success");
  } catch (error: any) {
    // Get error response
    toast(`Failed to fetch user: ${error.data.message}`, "error");
  }
};
const login = async () => {
  const creds = { login: "johndoe", password: "password" };
  authStore.login(creds);
};
const logout = async () => {
  authStore.logout();
};
</script>

<template>
  <div class="flex flex-col items-center justify-center w-full h-full gap-4">
    <div class="flex gap-10 flex-wrap">
      <div class="flex flex-col gap-3">
        <button
          :class="loading && 'loading'"
          class="btn btn-border"
          @click="submit('GET')"
        >
          Test 'GET' API Call
        </button>
        <button
          :class="loading && 'loading'"
          class="btn btn-border"
          @click="submit('POST')"
        >
          Test 'POST' API Call
        </button>
        <button
          @click="isActive = !isActive"
          :class="['btn', isActive ? 'btn-primary' : 'btn-secondary']"
        >
          Toggle Button
        </button>
        <NuxtLink href="login" class="btn">Login Page</NuxtLink>
      </div>
      <div class="flex flex-col gap-3">
        <button class="btn" @click="getUser">Get User</button>
        <button class="btn" @click="login">Login</button>
        <button class="btn" @click="logout">Logout</button>
      </div>
    </div>
  </div>
</template>
