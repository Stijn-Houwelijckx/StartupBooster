// Fetch the JSON data from the API for the sector

averageWageInput = document.getElementById("employee_wage");

fetch("https://www.stijn-houwelijckx.be/myApi/api.php")
  .then((response) => response.json())
  .then((data) => {
    // console.log(data.data);
    // console.log(json_sector_UID);

    // Iterate through the array to find the object with the matching UID
    const sector = data.data.find((item) => item.UID === json_sector_UID);

    // Check if sector is found
    if (sector) {
      // Output the wage of the sector
      // console.log(`Wage for sector ${sector.sector}: ${sector.wage}`);

      // Fill the input field with the wage
      averageWageInput.value = sector.wage;
    } else {
      // If sector is not found, output a message
      console.log("Sector not found");
    }
  })
  .catch((error) => console.log("Error:", error));

// Simulation for the new employee

employeeSubmitButton = document.getElementById("employee_simulate");
employeeForm = document.getElementById("employee_form");

employeeSubmitButton.addEventListener("click", (event) => {
  event.preventDefault();

  // Get the values from the form
  const employeeCount = employeeForm.elements["employee_count"].value;
  const employeeHours = employeeForm.elements["employee_hours"].value;
  const employeeWage = employeeForm.elements["employee_wage"].value;

  // Find the latest year in the jsonData array
  const latestYear = jsonData.reduce((latest, current) => {
    const year = parseInt(current[0]);
    return year > latest ? year : latest;
  }, 0);

  jsonData[0][3] = "simulatie";

  for (let i = 1; i < jsonData.length; i++) {
    jsonData[i][3] = jsonData[i][1];
    // jsonData[i][3] = 0;
  }

  // Calculate the next two years
  const nextYear = latestYear + 1;
  const yearAfterNext = latestYear + 2;

  var value = 0;
  var selectedValue = document.getElementById("statsFilterChart").value;
  switch (selectedValue) {
    case "revenue":
      // Do something when "Omzet" is selected
      value = 0;
      break;
    case "costs":
      // Calculate the wage per year (hourly rate -> yearly rate)
      const wagePerYear = employeeWage * employeeHours * 5 * 52;

      // Calculate the total wage for all employees
      const totalWage = wagePerYear * employeeCount;

      value = totalWage;
      break;
    case "profit_loss":
      // Do something when "Winst" is selected
      value = 0;
      break;
    case "personnel":
      // Do something when "Personeel" is selected
      value = employeeCount;
      break;
    case "equityCapital":
      // Do something when "Eigen vermogen" is selected
      value = 0;
      break;
    case "grossMargin":
      // Do something when "Bruto marge" is selected
      value = 0;
      break;
    default:
    // Default action if none of the above cases match
  }

  console.log(jsonData[jsonData.length - 1][1] + value);

  jsonData.push([
    String(nextYear),
    Number(jsonData[jsonData.length - 1][1]),
    Number(jsonData[jsonData.length - 1][2]),
    jsonData[jsonData.length - 1][1] + Number(value),
  ]);
  jsonData.push([
    String(yearAfterNext),
    Number(jsonData[jsonData.length - 1][1]),
    Number(jsonData[jsonData.length - 1][2]),
    jsonData[jsonData.length - 1][1] + Number(value),
  ]);

  drawChart();

  // Output the updated jsonData array
  console.log(jsonData);
});

// ======================================================================================================== //
