let changingPosition = false;

document.querySelectorAll(".steps .delete").forEach(function (deleteBtn) {
  deleteBtn.addEventListener("click", function (e) {
    e.preventDefault();
    var stepId = this.getAttribute("data-step-id");
    var popup = document.getElementById("popup_" + stepId);
    popup.style.display = "flex";
  });
});

// Add task button between steps
document.querySelectorAll(".step").forEach(function (step) {
  // Show add button on mouse over
  step.addEventListener("mouseenter", function (e) {
    // console.log("Mouse over step: ", this);

    if (changingPosition === false) {
      step.firstElementChild.style.display = "flex";
    }
  });

  // Hide add button on mouse leave
  step.addEventListener("mouseleave", function (e) {
    step.firstElementChild.style.display = "none";
  });
});

// Add task button click event
document.querySelectorAll(".addTaskBtn").forEach(function (addTaskBtn) {
  addTaskBtn.addEventListener("click", function (e) {
    var taskId = this.dataset.taskid;
    console.log("Task ID: ", taskId);
  });
});

// ================== Drag and drop to change position ==================

const changePositionBtn = document.getElementById("changePositionBtn");
const appendTaskBtn = document.getElementById("appendTaskBtn");
const saveChangesBtn = document.getElementById("saveChangesBtn");
const dropzone = document.getElementById("dropzone");
let tasks = document.querySelectorAll(".step");

// Event listener for when the change position button is clicked
changePositionBtn.addEventListener("click", function (e) {
  console.log("Change position button clicked");

  // Add class to button to indicate that it is active
  changePositionBtn.classList.toggle("active");

  // Toggle the change position button
  if (changePositionBtn.classList.contains("active")) {
    // Start changing position
    changePositionBtn.innerHTML = `<i class="fa fa-sort" style="padding-right:8px"></i> Stop volgorde aanpassen`;
    changePositionBtn.style.backgroundColor = "var(--lightBlue)";
    changePositionBtn.style.color = "var(--blue)";

    // Set changing position to true
    changingPosition = true;

    // Disable add task and save button
    appendTaskBtn.classList.add("btnDisabled");
    saveChangesBtn.classList.add("btnDisabled");

    // Disable all input fields and select fields
    document
      .querySelectorAll(".step input:not(.task-position)")
      .forEach(function (input) {
        input.disabled = true;
        input.classList.add("areaDisabled");
      });

    document.querySelectorAll(".step select").forEach(function (select) {
      select.disabled = true;
      select.classList.add("areaDisabled");
    });

    // Hide delete buttons
    document
      .querySelectorAll(".step .icons label")
      .forEach(function (deleteBtn) {
        deleteBtn.style.display = "none";
      });

    // Show drag handle
    document
      .querySelectorAll(".step .icons .handle")
      .forEach(function (handle) {
        handle.style.display = "block";
      });

    // Make the tasks draggable
    tasks.forEach((task) => {
      task.setAttribute("draggable", "true");
    });
  } else {
    // Stop changing position
    changePositionBtn.innerHTML = `<i class="fa fa-sort" style="padding-right:8px"></i> Volgorde aanpassen`;
    changePositionBtn.style.backgroundColor = "var(--blue)";
    changePositionBtn.style.color = "var(--white)";

    // Enable add task button and save button
    appendTaskBtn.classList.remove("btnDisabled");
    saveChangesBtn.classList.remove("btnDisabled");

    // Set changing position to false
    changingPosition = false;

    // Enable all input fields and select fields
    document.querySelectorAll(".step input").forEach(function (input) {
      input.disabled = false;
      input.classList.remove("areaDisabled");
    });

    document.querySelectorAll(".step select").forEach(function (select) {
      select.disabled = false;
      select.classList.remove("areaDisabled");
    });

    // Show delete buttons
    document
      .querySelectorAll(".step .icons label")
      .forEach(function (deleteBtn) {
        deleteBtn.style.display = "block";
      });

    // Hide drag handle
    document
      .querySelectorAll(".step .icons .handle")
      .forEach(function (handle) {
        handle.style.display = "none";
      });

    // Make the tasks not draggable
    tasks.forEach((task) => {
      task.setAttribute("draggable", "false");
    });

    // Submit form to save changes
    saveChangesBtn.click();
  }
});

// Drag and drop functionality
tasks.forEach((task) => {
  task.addEventListener("dragstart", () => {
    task.classList.add("dragging");
  });

  task.addEventListener("dragend", () => {
    task.classList.remove("dragging");

    tasks = document.querySelectorAll(".step");
    updateTaskPositions();
  });
});

// Dropzone functionality
dropzone.addEventListener("dragover", (e) => {
  e.preventDefault();

  const bottomTask = insertAboveTask(dropzone, e.clientY);
  const draggingTask = document.querySelector(".step.dragging");

  if (!bottomTask) {
    dropzone.appendChild(draggingTask);
  } else {
    dropzone.insertBefore(draggingTask, bottomTask);
  }
});

/**
 * Finds the closest task element above the given mouseY position within the dropzone.
 *
 * @param {HTMLElement} dropzone - The dropzone element containing the tasks.
 * @param {number} mouseY - The vertical position of the mouse cursor.
 * @returns {HTMLElement|null} - The closest task element above the mouseY position, or null if no task is found.
 */
const insertAboveTask = (dropzone, mouseY) => {
  const taskElements = dropzone.querySelectorAll(".step:not(.dragging)");

  let closestTask = null;
  let closestOffset = Number.NEGATIVE_INFINITY;

  taskElements.forEach((task) => {
    const { top } = task.getBoundingClientRect();
    const offset = mouseY - top;

    if (offset < 0 && offset > closestOffset) {
      closestTask = task;
      closestOffset = offset;
    }
  });

  return closestTask;
};

/**
 * Updates the positions of tasks based on their index.
 */
const updateTaskPositions = () => {
  tasks.forEach((task, index) => {
    const positionInput = task.querySelector(".task-position");
    if (positionInput) {
      positionInput.value = index + 1;
    }

    const stepID = task.querySelector(".stepID");
    if (stepID) {
      stepID.textContent = `Stap ${index + 1}`;
    }

    const addTaskBtn = task.querySelector(".addTaskBtn");
    if (addTaskBtn) {
      addTaskBtn.dataset.taskid = index + 1;
    }
  });
};
