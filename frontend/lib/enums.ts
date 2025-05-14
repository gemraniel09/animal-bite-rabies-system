export enum VACCINATION_STATUS {
  REQUIRED = "required", //Patient is required for vaccincation, category > 2
  OPTIONAL_OPTED_IN = "optional_opted_in", //Vaccincation is mandatory, but patient prefer to be vaccinated
  OPTIONAL_OPTED_OUT = "optional_opted_out", //Vaccincation is mandatory, but patient prefer to be not vaccinated
}
