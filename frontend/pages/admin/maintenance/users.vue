<script setup lang="ts">
const router = useRouter();
const userService = useUserService();

const goToUserManage = (id: number) => {
  router.push(`/admin/maintenance/user/${id}`);
};

const { data: users, loading, error, refresh } = userService.getUsers();
const { formatDate } = useDateFormatter();
</script>

<template>
  <div class="overflow-x-auto">
    <div class="mx-5 mb-5">
      <NuxtLink
        class="btn btn-primary my-5"
        href="/admin/maintenance/create-user"
        >Create User</NuxtLink
      >
      <table class="table table-zebra">
        <thead>
          <tr>
            <th>Userame</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Role</th>
            <th>Email</th>
            <th>Date Created</th>
            <th>Manage</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="8 " class="text-center">
              <p class="loading text-center"></p>
            </td>
          </tr>
          <tr v-else v-for="user in users" :key="user.id">
            <td>{{ user.username }}</td>
            <td>{{ user.first_name }}</td>
            <td>{{ user.middle_name }}</td>
            <td>{{ user.last_name }}</td>
            <td>
              <RoleBadge :role="user.role" />
            </td>
            <td>{{ user.email }}</td>
            <td>{{ formatDate(user.created_at) }}</td>
            <td>
              <div class="flex gap-2">
                <NuxtLink
                  class="btn btn-success btn-sm btn-soft"
                  :href="`user/${user.id}`"
                  >Manage</NuxtLink
                >
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
