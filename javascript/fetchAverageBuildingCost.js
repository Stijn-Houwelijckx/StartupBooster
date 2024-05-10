let premisesLocation = document.getElementById("premises_location");
var averageBuildingCost = document.getElementById("default_estimated_cost");

premisesLocation.addEventListener("change", function () {
  var city = this.value;
  var formData = new FormData();
  formData.append("city", city);
  console.log("city: " + city);

  // Fetch tasks by user

  fetch("ajax/fetchAverageBuildingCost.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.status === "error") {
        console.log("Error: ", result);
        return;
      } else {
        console.log("Success: ", result);
        if (result.price == null) {
          averageBuildingCost.innerHTML = "Niet gevonden";
          return;
        } else {
          averageBuildingCost.innerHTML = result.price;
        }
      }
    })
    .catch((error) => {
      console.error("Error retreiving the tasks:", error);
    });
});
