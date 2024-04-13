document.querySelectorAll(".steps .delete").forEach(function (deleteBtn) {
  deleteBtn.addEventListener("click", function (e) {
    e.preventDefault();
    var stepId = this.getAttribute("data-step-id");
    var popup = document.getElementById("popup_" + stepId);
    popup.style.display = "flex";
  });
});

document.querySelectorAll(".step").forEach(function (step) {
  step.addEventListener("mouseenter", function (e) {
    console.log("Mouse over step: ", this);

    step.firstElementChild.style.display = "flex";
  });

  step.addEventListener("mouseleave", function (e) {
    step.firstElementChild.style.display = "none";
  });
});

document.querySelectorAll(".addTaskBtn").forEach(function (addTaskBtn) {
  addTaskBtn.addEventListener("click", function (e) {
    var taskId = this.dataset.taskid;
    console.log("Task ID: ", taskId);
  });
});
