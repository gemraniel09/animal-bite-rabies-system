<script setup lang="ts">
import type { UserType } from "~/lib/types";

const user = ref({
  first_name: "",
  last_name: "",
  middle_name: "",
  username: "",
  role: -1,
  email: "",
  password: "",
  password_confirmation: "",
});
const userService = useUserService();
const loading = ref(false);
const { toast } = useToast();
const submitForm = async () => {
  loading.value = true;
  console.log("Submitting user:", user.value); // Add this line
  try {
    const data = await userService.createUser(user.value);
    toast(data.message, "success");
    user.value = {
      first_name: "",
      last_name: "",
      middle_name: "",
      username: "",
      email: "",
      role: -1,
      password: "",
      password_confirmation: "",
    };
    useRouter().push("/admin/maintenance/users");
  } catch (error: any) {
    toast(error.data.message, "error");
  }
  loading.value = false;
};
</script>

<template>
  <div class="card bg-base-100 shadow-sm w-full">
    <div class="card-body">
      <h2 class="card-title">Create User</h2>
      <form @submit.prevent="submitForm">
        <div class="flex flex-wrap gap-4">
          <!-- Left column -->
          <div class="flex-1 min-w-[280px]">
            <!-- First Name -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">First Name</span>
              </label>
              <input
                type="text"
                v-model="user.first_name"
                placeholder="Enter first name"
                class="input input-bordered w-full"
                required
                minlength="3"
                maxlength="100"
              />
            </div>

            <!-- Middle Name -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5"
                  >Middle Name (Optional)</span
                >
              </label>
              <input
                type="text"
                v-model="user.middle_name"
                placeholder="Enter middle name"
                class="input input-bordered w-full"
              />
            </div>
            <!-- Last Name -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Last Name</span>
              </label>
              <input
                type="text"
                v-model="user.last_name"
                placeholder="Enter last name"
                class="input input-bordered w-full"
                required
              />
            </div>
            <!-- Username -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Username</span>
              </label>
              <input
                type="text"
                v-model="user.username"
                placeholder="Enter username"
                class="input input-bordered w-full"
                required
              />
            </div>
          </div>

          <!-- Right column -->
          <div class="flex-1 min-w-[280px]">
            <!-- Email -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Email</span>
              </label>
              <input
                type="email"
                v-model="user.email"
                placeholder="Enter email"
                class="input input-bordered w-full"
                required
              />
            </div>
            <!-- Role -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Role</span>
              </label>
              <select v-model="user.role" class="select w-full">
                <option selected disabled :value="-1">Assign a role</option>
                <option :value="0">Admin</option>
                <option :value="1">Nurse</option>
              </select>
            </div>
            <!-- Password -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Password</span>
              </label>
              <input
                type="password"
                v-model="user.password"
                placeholder="Enter password"
                class="input input-bordered w-full"
                required
                minlength="6"
              />
            </div>

            <!-- Confirm Password -->
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text mt-2 mb-0.5">Confirm Password</span>
              </label>
              <input
                type="password"
                v-model="user.password_confirmation"
                placeholder="Confirm password"
                class="input input-bordered w-full"
                required
                minlength="6"
              />
            </div>
          </div>
        </div>

        <div class="form-control mt-6 flex justify-end">
          <button class="btn btn-primary" :class="{ loading: loading }">
            Create User
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
