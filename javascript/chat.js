const ajaxPathExtention = isAdmin ? "../" : "";

// Open chat
function openChat() {
  // Display chat window
  document.querySelector(".chat").style.display = "flex";

  // Scroll to the bottom of the chat window
  const chatWindow = document.getElementById("chatWindow");
  chatWindow.scrollTop = chatWindow.scrollHeight;
}

// ================== Event Listeners + AJAX ================== //

// Send message
document
  .querySelector("#btnSendMessage")
  .addEventListener("click", function (e) {
    // Get chat id and message
    // let chatId = this.dataset.chatid;
    let message = document.querySelector("#chatMessage").value;

    // Check if the message is empty
    if (!message.trim()) {
      // alert("Message cannot be empty.");
      return; // Exit the function if the message is empty
    }

    // Send message to server
    let formData = new FormData();
    // formData.append("chatId", chatId);
    formData.append("message", message);

    fetch(ajaxPathExtention + "ajax/sendChatMessage.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.status === "error") {
          document.querySelector(".errorMsg").innerHTML = result.message;
          return;
        } else {
          document.querySelector(".errorMsg").innerHTML = "";

          const chatWindow = document.getElementById("chatWindow");

          updateChatInterfact(result);

          // Scroll to the bottom of the chat window
          chatWindow.scrollTop = chatWindow.scrollHeight;

          // Clear the chat message input
          document.querySelector("#chatMessage").value = "";
        }
      })
      .catch((error) => {
        console.error("Error: ", error);
      });
  });

function fetchNewChatMessages() {
  fetch(ajaxPathExtention + "ajax/fetchNewChatMessages.php")
    .then((response) => response.json())
    .then((result) => {
      // Update the chat interface with the new messages
      updateChatInterfact(result);

      // Scroll to the bottom of the chat window
      chatWindow.scrollTop = chatWindow.scrollHeight;
    })
    .catch((error) => {
      console.error("Error: ", error);
    });
}

// End chat
document.querySelector("#btnEndChat").addEventListener("click", function (e) {
  // Send request to server to end the chat
  fetch(ajaxPathExtention + "ajax/endChat.php", {
    method: "POST",
    body: new FormData(),
  })
    .then((response) => response.json())
    .then((result) => {
      // Check the result and handle accordingly
      if (result.status === "success") {
        // Update UI or perform any other actions
        document.querySelector(".chat").style.display = "none";
      } else {
        // Display error message or handle the error
        console.error("Failed to end the chat:", result.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
});

// ================== Helper Functions ================== //

/**
 * Updates the chat interface with a new message.
 *
 * @param {Object} message - The message object containing the message details.
 * @param {string} message.profileImg - The URL of the profile image for the sender.
 * @param {string} message.body - The content of the message.
 */
function updateChatInterfact(message) {
  const chatWindow = document.getElementById("chatWindow");

  // Create a new message element
  var messageElement = document.createElement("div");

  // Set the class of the message element based on the sender
  messageElement.className = "row user";
  // result.sender === "user" ? "row user" : "row admin";

  // Create a profile picture div
  var profilePicture = document.createElement("div");
  profilePicture.className = "profilePicture";
  profilePicture.style.backgroundImage = "url('" + message.profileImg + "')";

  // Create a paragraph for the message content
  var messageContent = document.createElement("p");
  messageContent.textContent = message.body;

  // Append the profile picture and message content to the message element
  messageElement.appendChild(profilePicture);
  messageElement.appendChild(messageContent);

  // Append the message element to the chat window
  chatWindow.appendChild(messageElement);
}

// Close chat
document
  .querySelector(".chat .fa-plus")
  .addEventListener("click", function (e) {
    document.querySelector(".chat").style.display = "none";
  });
