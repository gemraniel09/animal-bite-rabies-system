import type { TransactionSchedule } from "~/lib/types";
import { format } from "date-fns";

export const usePatientSchedule = () => {
  const generateSchedule = (
    startDate: Date,
    intervals: number[]
  ): TransactionSchedule[] => {
    return intervals.map((interval) => {
      const newDate = new Date(startDate);
      newDate.setDate(startDate.getDate() + interval);

      // Check if weekend using getDay() (0 = Sunday, 6 = Saturday)
      const dayOfWeek = newDate.getDay();
      if (dayOfWeek === 0 || dayOfWeek === 6) {
        // Add days to reach Monday (add 1 for Sunday, 2 for Saturday)
        const daysToAdd = dayOfWeek === 0 ? 1 : 2;
        newDate.setDate(newDate.getDate() + daysToAdd);
      }
      return {
        name: `Day ${interval}`,
        schedule: format(newDate, "MMMM dd, yyyy"),
      };
    });
  };

  const isVisited = (schedule: TransactionSchedule) => {
    return schedule.visited_date !== null;
  };

  const isUpcoming = (schedule: TransactionSchedule) => {
    if (schedule.visited_date) return false;
    const today = new Date();
    const scheduleDate = new Date(schedule.schedule);
    return scheduleDate >= today;
  };
  return { generateSchedule, isVisited, isUpcoming };
};
