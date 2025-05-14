<script setup>
const auth = useAuthStore();
const user = computed(() => auth.user);

const { formatDate } = useDateFormatter();
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
              </div>
              <div class="divider my-1"></div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                  <label class="label">
                    <span class="label-text text-xs opacity-70"
                      >First Name</span
                    >
                  </label>
                  <div
                    class="input input-bordered bg-base-200 h-10 flex items-center px-3"
                  >
                    {{ user.first_name }}
                  </div>
                </div>

                <div class="form-control">
                  <label class="label">
                    <span class="label-text text-xs opacity-70"
                      >Middle Name</span
                    >
                  </label>
                  <div
                    class="input input-bordered bg-base-200 h-10 flex items-center px-3"
                  >
                    {{ user.middle_name }}
                  </div>
                </div>

                <div class="form-control">
                  <label class="label">
                    <span class="label-text text-xs opacity-70">Last Name</span>
                  </label>
                  <div
                    class="input input-bordered bg-base-200 h-10 flex items-center px-3"
                  >
                    {{ user.last_name }}
                  </div>
                </div>

                <div class="form-control">
                  <label class="label">
                    <span class="label-text text-xs opacity-70">Username</span>
                  </label>
                  <div
                    class="input input-bordered bg-base-200 h-10 flex items-center px-3"
                  >
                    {{ user.username }}
                  </div>
                </div>

                <div class="form-control md:col-span-2">
                  <label class="label">
                    <span class="label-text text-xs opacity-70"
                      >Email Address</span
                    >
                  </label>
                  <div
                    class="input input-bordered bg-base-200 h-10 flex items-center px-3"
                  >
                    {{ user.email }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
