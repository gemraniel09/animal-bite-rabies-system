import type {
  DataFetchOptions,
  Patient,
  Transaction,
  TransactionSchedule,
} from "~/lib/types";

export const useTransactionService = () => {
  const getTransactions = (options?: DataFetchOptions) =>
    useDataFetch<Transaction[]>("/api/transactions", options);

  const getPredictedCases = (options?: DataFetchOptions) =>
    useDataFetch<Transaction[]>("/api/predictCase", options);

  const getMonth6Cases= () =>
      useDataFetch<Array<any>>("/api/month6Cases");


  const getOnlyAnimalsCount= () =>
      useDataFetch<Record<string, number>>("/api/getOnlyAnimalsCount");

  const getTopBarangay= () =>
      useDataFetch<Array<any>>("/api/getTop10BrangayBase6Month");



  const getCounts= () =>
      useDataFetch<Record<string, number>>("/api/getCounts");

  const getTransaction = (id: number) =>
    useApiFetch<Transaction>(`/api/transactions/${id}`);

  const createTransaction = ({
    patient,
    transaction,
    transaction_schedules,
  }: {
    patient: Patient;
    transaction: Transaction;
    transaction_schedules?: TransactionSchedule[];
  }) => {
    return useApiFetch<{ message: string; transaction_id: number }>(
      "/api/transactions",
      {
        method: "POST",
        body: JSON.stringify({
          patient,
          transaction,
          transaction_schedules,
        }),
      }
    );
  };

  const updateTransaction = (transaction: Transaction) =>
    useApiFetch<{ message: string; transaction: Transaction }>(
      `/api/transactions/${transaction.id}`,
      {
        method: "PUT",
        body: JSON.stringify(transaction),
      }
    );
  const downloadTransactions = (params: Record<string, any>) =>
    useDownloadFile().downloadFile("/api/transaction/exportCsv", params);

  return {
    getTransactions,
    createTransaction,
    getTransaction,
    updateTransaction,
    getPredictedCases,
    downloadTransactions,
    getMonth6Cases,
    getCounts,
    getOnlyAnimalsCount,
    getTopBarangay
  };
};
