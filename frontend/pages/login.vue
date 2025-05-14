<script setup lang="ts">
import {User, KeyRound, Lock} from "lucide-vue-next";

const authStore = useAuthStore();
const login = ref<string>("");
const password = ref<string>("");

async function handleLogin() {
  // Server automatically determines if user used username or password in login attribute
  const creds = {login: login.value, password: password.value};
  authStore.login(creds);
}
</script>

<template>
  <div
      class="flex items-center justify-center w-full h-[calc(100vh-150px)] bg-[url('/logo.png')] bg-no-repeat bg-[position:top_center]">
    <div class="card w-96 bg-base-100 shadow-xl card-border mt-[300px]">
      <div class="card-body">
        <div class="text-center mb-4">
          <button class="btn btn-circle btn-lg mb-1">
            <Lock/>
          </button>
          <h2 class="text-2xl font-bold">Login</h2>
          <p class="text-sm text-base-content/70">Login to your account</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-4">
          <!-- Username input -->
          <div class="form-control">
            <label class="label">
              <span class="label-text mb-1">Email or username</span>
            </label>
            <label class="input input-bordered flex items-center gap-2 w-full">
              <User :size="18"/>
              <input
                  v-model="login"
                  type="text"
                  required
                  placeholder="Enter email or username"
                  minlength="3"
                  maxlength="100"
                  class="grow"
              />
            </label>
          </div>

          <!-- Password input -->
          <div class="form-control">
            <label class="label">
              <span class="label-text mb-1">Password</span>
            </label>
            <label class="input input-bordered flex items-center gap-2 w-full">
              <KeyRound :size="18"/>
              <input
                  v-model="password"
                  type="password"
                  required
                  placeholder="Enter password"
                  minlength="6"
                  pattern=".{6,}"
                  title="Must be at least 6 characters"
                  class="grow"
              />
            </label>
            <div hidden class="text-xs text-base-content/70 mt-1 text-error">
              Password must have at least 6 characters with numbers, lowercase
              and uppercase letters
            </div>
          </div>

          <!-- Remember me checkbox -->
          <!-- <div class="form-control">
            <label class="label cursor-pointer justify-start gap-2">
              <input
                type="checkbox"
                class="checkbox checkbox-sm checkbox-primary"
              />
              <span class="label-text">Remember me</span>
            </label>
          </div> -->

          <!-- Submit button -->
          <div class="form-control mt-6">
            <button type="submit" class="btn btn-primary w-full">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
