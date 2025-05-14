import type {Barangay} from "~/lib/types";

export const useBarangayService = () => {
  const getBarangays = () => useDataFetch<Barangay[]>("/api/barangays");

  return {
    getBarangays
  }
};
