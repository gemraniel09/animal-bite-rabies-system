import type { UserType, Patient } from "~/lib/types";

export const useFormatter = () => {
  const formatCategory = (category: number): string => {
    const categories: Record<number, string> = {
      1: "I",
      2: "II",
      3: "III",
    };
    if (category < 1 || category > 3)
      throw new Error("Invalid category number");
    return categories[category];
  };

  const washBite = (washed: boolean): string => {
    return washed ? "Bite Washed" : "Bite Not Washed";
  };

  const formatName = (user: Patient | UserType) => {
    if (!user) return "-";
    return user.first_name + " " + user.middle_name + ". " + user.last_name;
  };

  const formatRole = (role: number) => {
    const roles: { [key: number]: { name: string } } = {
      0: { name: "Admin" },
      1: { name: "Nurse" },
    };
    return roles[role].name;
  };

  return { formatCategory, washBite, formatName, formatRole };
};
