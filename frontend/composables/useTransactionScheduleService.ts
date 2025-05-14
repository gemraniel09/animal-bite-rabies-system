export const useTransactionScheduleService = () => {
  const updateSchedule = (
    id: number,
    data: { remarks: string; visited_date: string }
  ) =>
    useApiFetch<{ message: string }>(`/api/transactions/schedules/${id}`, {
      method: "PUT",
      body: JSON.stringify(data),
    });
  return { updateSchedule };
};
