import type { Brand } from "~/lib/types";

export const useBrandService = () => {
  const getBrands = () => useDataFetch<Brand[]>("/api/brands");

  const createBrand = (brand: Brand) =>
    useApiFetch<{ message: string }>("/api/brands", {
      method: "POST",
      body: JSON.stringify(brand),
    });

  const updateBrand = (brand: Brand) =>
    useApiFetch<{ message: string }>(`/api/brands/${brand.id}`, {
      method: "PUT",
      body: JSON.stringify(brand),
    });

  const deleteBrand = (id: number) =>
    useApiFetch(`/api/brands/${id}`, { method: "DELETE" });

  return { getBrands, createBrand, updateBrand, deleteBrand };
};
