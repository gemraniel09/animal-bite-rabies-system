import type { FetchOptions } from "ofetch";

export type MenuList = {
  name: string;
  icon?: Component;
  href: string;
  children?: MenuList[];
};

export type UserType = {
  id: number;
  username: string;
  email: string;
  first_name: string;
  middle_name: string;
  last_name: string;
  role: number;
  created_at: Date;
};

export type FullUserDataType = {
  first_name: string;
  last_name: string;
  middle_name: string;
  username: string;
  role: number;
  email: string;
  password?: string;
  password_confirmation?: string;
  created_at?: string;
  updated_at?: string;
  deleted_at?: string;
};

export type Animal = {
  id?: number;
  name: string;
  transaction_count: number;
  created_at?: string;
};

export type Barangay = {
  id?: number;
  code: string;
  name: string;
  created_at?: string;
};

export type Patient = {
  id?: number;
  first_name: string;
  middle_name: string;
  last_name: string;
  full_name: string;
  transactions: Array<Transaction>;
  barangay: Barangay;
  barangay_id: number;
  birth_date: string;
  gender: string;
  created_at?: string;
  updated_at?: string;
  transactions_count?: number;
};

export type Transaction = {
  id?: number;
  barangay: Barangay;
  barangay_id: number;
  place: string;
  animal?: Animal;
  animal_id?: number;
  animal_status: string;
  body_part: string;
  category: number;
  wash_bite: boolean;
  rig_given: boolean;
  booster_given: boolean;
  vaccination_status: VaccinationStatus;
  created_at?: string;
  updated_at?: string;
  schedules?: TransactionSchedule[];
  patient?: Patient;
  brand_id: number;
  brand?: Brand;
  age?: number;
};

export type TransactionSchedule = {
  id?: number;
  transaction_id?: number;
  transaction?: Transaction;
  name: string;
  schedule: string;
  visited_date?: string | null;
  remarks?: string | null;
  user?: UserType | null;
};

export type VaccinationStatus =
  | "required"
  | "optional_opted_in"
  | "optional_opted_out";

export type Brand = {
  id?: number;
  name: string;
  created_at?: string;
  transaction_count?: number;
};

export type DataFetchOptions = FetchOptions<any> & { immediate?: boolean };
export type ExecuteOptions = {
  showLoading?: boolean;
  params?: Record<string, any>;
  search?: {
    params: Record<string, any>;
  };
};
