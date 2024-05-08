averageWageInput = document.getElementById("employee_wage");

fetch("https://www.stijn-houwelijckx.be/myApi/api.php")
  .then((response) => response.json())
  .then((data) => {
    // // Handle the data here
    // const outputDiv = document.getElementById("output");
    // outputDiv.innerHTML = ""; // Clear previous content

    // // Iterate over each sector and append it to the output div
    // data.data.forEach((sector) => {
    //   const sectorElement = document.createElement("div");
    //   sectorElement.innerHTML = `<strong>${sector.sector}</strong>: ${sector.wage}`;
    //   outputDiv.appendChild(sectorElement);
    // });
    console.log(data.data);
    console.log(json_sector_UID);

    // Iterate through the array to find the object with the matching UID
    const sector = data.data.find((item) => item.UID === json_sector_UID);

    // Check if sector is found
    if (sector) {
      // Output the wage of the sector
      console.log(`Wage for sector ${sector.sector}: ${sector.wage}`);

      // Fill the input field with the wage
      averageWageInput.value = sector.wage;
    } else {
      // If sector is not found, output a message
      console.log("Sector not found");
    }
  })
  .catch((error) => console.log("Error:", error));
