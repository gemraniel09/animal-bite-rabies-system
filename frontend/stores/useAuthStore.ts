import { defineStore } from "pinia";
import type { UserType } from "~/lib/types";

const roles: { [key: number]: { name: string } } = {
  0: { name: "admin" },
  1: { name: "nurse" },
};
const { toast } = useToast();
export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: {} as UserType,
    loading: false,
  }),
  getters: {
    isLoggedIn: (state) => !!state.user?.id,
    roleName: (state) => roles[state.user.role]?.name,
    placeHolder: (state) => state.user.first_name[0] + state.user.last_name[0],
    fullName: (state) => state.user.first_name + " " + state.user.last_name,
  },
  actions: {
    async getCurrentUser() {
      this.loading = true;
      try {
        const user = await useApiFetch<UserType>("/api/user");
        this.user = user;
      } catch (error) {
        this.user = {} as UserType;
      }
      this.loading = false;
    },
    async login(credentials: { login: string; password: string }) {
      this.loading = true;
      try {
        const response = await useApiFetch<{ user: UserType }>("/api/login", {
          method: "POST",
          body: credentials,
        });
        this.user = response.user;
        this.loading = false;
        if(response.user.role === 1)
          return useRouter().push(`/admin/report/transactions`);

        await useRouter().push(`/admin/dashboard`);
      } catch (error: any) {
        toast(`${error.data.message}`, "error");
      }
      this.loading = false;
    },
    async logout() {
      this.loading = true;
      try {
        this.user = {} as UserType;
        await useApiFetch("/logout", { method: "POST" });
        await useRouter().push("/login");
      } catch (error: any) {
        toast(`${error.data.message}`, "error");
      }
      this.loading = false;
    },
  },
});
