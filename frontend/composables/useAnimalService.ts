import type { Animal } from "~/lib/types";

export const useAnimalService = () => {
  const getAnimals = () => useDataFetch<Animal[]>("/api/animals");

  const createAnimal = (name: string) =>
    useApiFetch<{ message: string; animal: Animal }>("/api/animals", {
      method: "POST",
      body: JSON.stringify({ name }),
    });

  const updateAnimal = (animal: Animal) =>
    useApiFetch<{ message: string; animal: Animal }>(
      `/api/animals/${animal.id}`,
      {
        method: "PUT",
        body: JSON.stringify({ name: animal.name }),
      }
    );

  const deleteAnimal = (id: number) =>
    useApiFetch<{ message: string; animal: Animal }>(`/api/animals/${id}`, {
      method: "DELETE",
    });

  return { getAnimals, createAnimal, updateAnimal, deleteAnimal };
};
