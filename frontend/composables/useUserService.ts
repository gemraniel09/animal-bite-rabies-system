import { useDataFetch } from "~/composables/useDataFetch";
import type { UserType, FullUserDataType } from "~/lib/types";

export const useUserService = () => {
  const getUsers = () => useDataFetch<UserType[]>("/api/users");

  const getUser = (id: string | number) =>
    useDataFetch<UserType>(`/api/users/${id}`);

  const createUser = (user: FullUserDataType) =>
    useApiFetch("/api/users", { method: "POST", body: JSON.stringify(user) });

  const updateUser = (user: FullUserDataType | UserType, id: number) =>
    useApiFetch(`/api/users/${id}`, {
      method: "PUT",
      body: JSON.stringify(user),
    });

  const deleteUser = (id: number) =>
    useApiFetch(`/api/users/${id}`, { method: "DELETE" });

  return { getUsers, getUser, createUser, updateUser, deleteUser };
};
