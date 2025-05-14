<script setup lang="ts">
const paramsId = Number(useRoute().params.id);
const { toast } = useToast();
const userService = useUserService();
const router = useRouter();

const { data: user, loading, execute } = userService.getUser(paramsId);
const { formatDate } = useDateFormatter();

const userPassword = ref<{ password: string; password_confirmation: string }>({
  password: "",
  password_confirmation: "",
});

const submitLoading = ref(false);

const submitForm = async () => {
  submitLoading.value = true;
  try {
    if (
      userPassword.value.password ||
      userPassword.value.password_confirmation
    ) {
      //Attempts to change password
      //Include password values
      user.value = { ...user.value, ...userPassword.value };
    }

    const data = await userService.updateUser(user.value, paramsId);
    toast(data.message, "success");
  } catch (error: any) {
    toast(error.data.message, "error");
    execute({ showLoading: true });
  }
  submitLoading.value = false;
};

async function deleteUser() {
  const data = await userService.deleteUser(paramsId);
  toast(data.message, "success");
  await router.push(`/admin/maintenance/users`);
}
</script>

<template>
  <div class="pt-10">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column - User Card -->
        <div class="md:col-span-1">
          <div class="card bg-base-100 shadow-xl">
            <figure class="px-6 pt-6">
              <div class="avatar">
                <div
                  class="w-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2"
                >
                  <div
                    class="bg-primary text-primary-content w-full h-full flex items-center justify-center text-3xl font-bold"
                  >
                    {{ user.first_name ? user.first_name[0] : "U" }}
                  </div>
                </div>
              </div>
            </figure>
            <div class="card-body items-center text-center">
              <h2 class="card-title text-xl">
                {{ user.first_name }} {{ user.last_name }}
              </h2>
              <p class="text-sm text-base-content/70">@{{ user.username }}</p>
              <RoleBadge :role="user.role" />
              <div class="divider my-1 flex items-center"></div>
              <div class="stats stats-horizontal">
                <div class="stat px-3 py-2">
                  <div class="stat-title text-xs">User ID</div>
                  <div class="stat-value text-sm">#{{ user.id }}</div>
                </div>
                <div class="stat px-3 py-2">
                  <div class="stat-title text-xs">Date Created</div>
                  <div class="stat-value text-sm">
                    {{ formatDate(user.created_at) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - User Details -->
        <div class="md:col-span-2">
          <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
              <div class="flex justify-between items-center">
                <h2 class="card-title">Personal Information</h2>
                <DialogButton
                  :disabled="loading"
                  class="btn-error"
                  :header="`Delete user: '${user.username}'?`"
                  buttonText="Delete User"
                  closeText="Close"
                  submitText="Delete"
                  maxWidth="600"
                  :onSubmit="async () => await deleteUser()"
                >
                  <p class="text-base">
                    Are you sure you want to delete this user?
                  </p>
                  <span class="text-xs">
                    Note: Deleted users will be archived and can be restored.
                    However, another user cannot be created with the same
                    username and email.
                  </span>
                </DialogButton>
              </div>
              <div class="divider my-1"></div>
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
                        <option selected disabled :value="-1">
                          Assign a role
                        </option>
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
                        v-model="userPassword.password"
                        type="password"
                        placeholder="Enter password"
                        class="input input-bordered w-full"
                        minlength=""
                      />
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-control w-full">
                      <label class="label">
                        <span class="label-text mt-2 mb-0.5"
                          >Confirm Password</span
                        >
                      </label>
                      <input
                        v-model="userPassword.password_confirmation"
                        type="password"
                        placeholder="Confirm password"
                        class="input input-bordered w-full"
                        minlength=""
                      />
                    </div>
                  </div>
                </div>

                <div class="form-control mt-6 flex justify-end">
                  <button
                    :disabled="submitLoading || loading"
                    class="btn btn-success"
                  >
                    Confirm Changes
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
