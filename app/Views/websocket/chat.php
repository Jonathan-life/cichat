<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>CHAT</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <style>
    .message {
      margin: 1rem 0rem;
      max-width: 70%;
      border-radius: 18px;
      box-shadow: 0px 2px 5px 0px gray;
      padding: 1rem; 
    }
    .message-user{
      background-color: rgb(190, 255, 233);
      margin-left: auto;
    }

    .message-system{
      color: gray;
      font-style: italic;
      font-size: 0.76rem;
    }
    .time{
      font-size: 0.75rem;
      color: gray;
    }
    .header{
      font-weight: bold;
      color: dodgerblue;
    }
    #chat-container{
      height: 700px;
      border: 1px solid;
      padding: 1rem;
      overflow-y: auto;
      background: #f9f9f9;
    }
  </style>
</head>
<body>

  <div class="container">
    <div id="chat-header">
      <h4>Chat en tiempo real</h4>
      <div>
        <!-- status de la conexión -->
        <span id="statusText">Desconectado</span>
      </div>
    </div>

    <div id="chat-container">
      <div class="card my-3">
        <div class="card-body">
          <div id="chat-messages">
            <div class="message message-system">
              Conectando al servidor...
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Caja de texto para enviar los mensajes -->
    <div id="chat-input">
      <div class="mb-2">
        <input type="text" class="form-control" placeholder="Nombre" id="user-name" autofocus />
      </div>
      <div class="mb-2">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Tu mensaje aquí" id="message" />
          <button class="btn btn-success" type="button" id="sendButton">Enviar</button>
        </div>
      </div>
    </div> <!-- ./chat-input -->

  </div> <!-- ./container -->

  <script>
    let conn = null;
    const chatContainer = document.getElementById("chat-container");
    const messageInput = document.getElementById("message");
    const usernameInput = document.getElementById("user-name");
    const sendButton = document.getElementById("sendButton");
    const chatMessages = document.getElementById("chat-messages");
    const statusText = document.getElementById("statusText");

    //Función para hacer scroll al final del chat
    function scrollToBottom() {
      chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    //Función conectar al servidor
    function connect() {
      conn = new WebSocket('ws://localhost:8080');

      conn.onopen = function(e) {
        console.log("Conexión establecida");
        statusText.textContent = "Conectado";
        addSystemMessage("Conectado al servidor");
      };

      conn.onmessage = function(e) {
        console.log("Mensaje recibido...");
        const data = JSON.parse(e.data);

        if (data.type === 'system') {
          addSystemMessage(data.message);
        } else {
          addMessage(data);
        }
      };

      conn.onclose = function(e) {
        console.log("Conexión finalizada");
        statusText.textContent = "Desconectado";
        addSystemMessage("Desconectado del servidor");
      };

      conn.onerror = function(e) {
        console.error("Problemas en la conexión");
        statusText.textContent = "Error en la conexión";
        addSystemMessage("Error en la conexión");
      };
    }
    
    function sendMessage() {
      const message = messageInput.value.trim();
      const username = usernameInput.value.trim();

      if (message && username && conn.readyState === WebSocket.OPEN) {
        const data = {
          message: message,
          username: username,
          timestamp: new Date().toLocaleTimeString()
        };

        conn.send(JSON.stringify(data));
        messageInput.value = '';
      }
    }

    function addSystemMessage(text) {
      const messageDiv = document.createElement("div");
      messageDiv.textContent = text;
      messageDiv.classList.add("message-system", "message");
      chatMessages.appendChild(messageDiv);
      scrollToBottom();
    }

    function addMessage(data) {
      const messageDiv = document.createElement("div");
      messageDiv.classList.add("message");

      const isCurrentUser = data.username === usernameInput.value.trim();

      const contentDiv = document.createElement("div");
      if (isCurrentUser) {
        contentDiv.classList.add("message-user");
      } else {
        const headerDiv = document.createElement("div");
        headerDiv.textContent = data.username;
        headerDiv.classList.add("header");
        contentDiv.appendChild(headerDiv);
      }

      const textDiv = document.createElement("div");
      textDiv.textContent = data.message;
      contentDiv.appendChild(textDiv);

      if (data.timestamp) {
        const timeDiv = document.createElement("div");
        timeDiv.textContent = data.timestamp;
        timeDiv.classList.add("time");
        contentDiv.appendChild(timeDiv);
      }

      messageDiv.appendChild(contentDiv);
      chatMessages.appendChild(messageDiv);
      scrollToBottom();
    }

    sendButton.addEventListener("click", sendMessage);

    messageInput.addEventListener("keypress", (event) => {
      if (event.key === "Enter") {
        sendMessage();
      }
    });

    connect();
  </script>

</body>
</html>
