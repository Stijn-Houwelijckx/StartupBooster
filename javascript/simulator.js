const averageWageInput = document.getElementById("employee_wage");
var averageMonthlyIncome = 0;

fetch("https://www.stijn-houwelijckx.be/myApi/api.php")
  .then((response) => response.json())
  .then((data) => {
    const sector = data.data.find((item) => item.UID === json_sector_UID);
    if (sector) {
      averageWageInput.value = sector.wage;
      averageMonthlyIncome = sector.income;
    } else {
    }
  })
  .catch((error) => console.log("Error:", error));

employeeSubmitButton = document.getElementById("employee_simulate");
employeeForm = document.getElementById("employee_form");

employeeSubmitButton.addEventListener("click", (event) => {
  event.preventDefault();
  const employeeCount = employeeForm.elements["employee_count"].value;
  const employeeHours = employeeForm.elements["employee_hours"].value;
  const employeeWage = employeeForm.elements["employee_wage"].value;

  const latestYear = jsonData.reduce((latest, current) => {
    const year = parseInt(current[0]);
    return year > latest ? year : latest;
  }, 0);

  jsonData[0][3] = "simulatie";

  for (let i = 1; i < jsonData.length; i++) {
    jsonData[i][3] = jsonData[i][1];
  }

  const nextYear = latestYear + 1;
  const yearAfterNext = latestYear + 2;

  var value = 0;
  var selectedValue = document.getElementById("statsFilterChart").value;
  switch (selectedValue) {
    case "revenue":
      value = employeeCount * averageMonthlyIncome * 12 * (employeeHours / 8);
      break;
    case "costs":
      const wagePerYear = employeeWage * employeeHours * 5 * 52;
      const totalWage = wagePerYear * employeeCount;
      value = totalWage;
      break;
    case "profit_loss":
      value = 0;
      break;
    case "personnel":
      value = employeeCount;
      break;
    case "equityCapital":
      value = 0;
      break;
    case "grossMargin":
      value = 0;
      break;
    default:
  }

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
});

// ======================================================================================================== //
premisesSubmitButton = document.getElementById("premises_simulate");
premisesForm = document.getElementById("premises_form");

premisesSubmitButton.addEventListener("click", (event) => {
  event.preventDefault();
  const location = premisesForm.elements["premises_location"].value;
  var count = document.querySelector("#default_estimated_cost").innerHTML;
  const latestYear = jsonData.reduce((latest, current) => {
    const year = parseInt(current[0]);
    return year > latest ? year : latest;
  }, 0);

  jsonData[0][3] = "simulatie";

  for (let i = 1; i < jsonData.length; i++) {
    jsonData[i][3] = jsonData[i][1];
  }

  const nextYear = latestYear + 1;
  const yearAfterNext = latestYear + 2;

  var value = 0;
  var selectedValue = document.getElementById("statsFilterChart").value;
  switch (selectedValue) {
    case "revenue":
      value = 0;
      break;
    case "costs":
      count = parseInt(count);
      value = count * 12;
      break;
    case "profit_loss":
      value = 0;
      break;
    case "personnel":
      value = 0;
      break;
    case "equityCapital":
      value = 0;
      break;
    case "grossMargin":
      value = 0;
      break;
    default:
  }

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
});
