import type { DataFetchOptions, Patient } from "~/lib/types";

export const usePatientService = () => {
  const getPatients = (options?: DataFetchOptions) =>
    useDataFetch<Patient[]>("/api/patients", options);
  const getPatient = (id: number) =>
    useDataFetch<Patient>(`/api/patient/${id}`);

  const createPatient = (patient: Patient) =>
    useApiFetch<{ message: string; patient: Patient }>("/api/patients", {
      method: "POST",
      body: JSON.stringify(patient),
    });
  const downloadPatients = (params: Record<string, any>) =>
    useDownloadFile().downloadFile("/api/patient/exportCsv", params);

  return { getPatients, createPatient, getPatient, downloadPatients };
};
