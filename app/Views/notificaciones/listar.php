<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notificaciones en tiempo real</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      background: #f8fafc;
    }
    #noti-container {
      height: 600px;
      border: 1px solid #ccc;
      padding: 1rem;
      overflow-y: auto;
      border-radius: 8px;
      background: #fff;
    }
    .noti-message {
      margin: 1rem 0;
      padding: 1rem;
      border-radius: 12px;
      background: #e9f7ef;
      box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }
    .noti-header {
      font-weight: bold;
      color: #2c7a7b;
    }
    .noti-footer {
      font-size: 0.8rem;
      color: gray;
      display: flex;
      justify-content: space-between;
    }
    .status-badge {
      padding: 0.1rem 0.6rem;
      border-radius: 12px;
      font-weight: 600;
      color: white;
    }
    .status-pending {
      background-color: #f6ad55;
    }
    .status-resolved {
      background-color: #38a169;
    }
    /* Estado del servidor */
    #statusText {
      font-weight: bold;
    }
    #statusText.conectado {
      color: green;
    }
    #statusText.desconectado {
      color: red;
    }
    #statusText.error {
      color: orange;
    }
  </style>
</head>
<body>
  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>🔔 Notificaciones en tiempo real</h3>
      <div>
        Estado: <span id="statusText" class="desconectado">Desconectado</span>
      </div>
    </div>

    <a href="/notificaciones/crear" class="btn btn-primary mb-3"> Nueva notificación</a>

    <div id="noti-container">
      <div class="noti-message noti-system" style="background:#f0f0f0; color:#666; text-align:center;">
        Conectando al servidor...
      </div>
    </div>
  </div>

  <script>
    const notiContainer = document.getElementById('noti-container');
    const statusText = document.getElementById('statusText');

    // 🔹 Formatear fecha legible
    function formatoBonito(fechaISO) {
      try {
        const f = new Date(fechaISO);
        return f.toLocaleString('es-PE', {
          dateStyle: 'short',
          timeStyle: 'short'
        });
      } catch {
        return fechaISO;
      }
    }

    function addSystemMessage(msg) {
      const div = document.createElement('div');
      div.classList.add('noti-message');
      div.style.background = '#f0f0f0';
      div.style.color = '#666';
      div.style.textAlign = 'center';
      div.textContent = msg;
      notiContainer.appendChild(div);
      notiContainer.scrollTop = notiContainer.scrollHeight;
    }

    function addNotification(data) {
      const div = document.createElement('div');
      div.classList.add('noti-message');

      let statusClass = 'status-pending';
      if (data.estado?.toLowerCase() === 'resuelto') statusClass = 'status-resolved';

      div.innerHTML = `
        <div class="noti-header">${data.cliente}</div>
        <div>${data.problema}</div>
        <div class="noti-footer">
          <span>${formatoBonito(data.fechahora)}</span>
          <span class="status-badge ${statusClass}">${data.estado}</span>
        </div>
      `;
      notiContainer.appendChild(div);
      notiContainer.scrollTop = notiContainer.scrollHeight;
    }

    // ===============================
    // WebSocket Connection
    // ===============================
    const socket = new WebSocket('ws://localhost:8080');

    socket.onopen = () => {
      console.log(' Conectado al WebSocket');
      statusText.textContent = 'Conectado';
      statusText.className = 'conectado';
      addSystemMessage(' Conectado al servidor WebSocket.');
    };

    socket.onmessage = e => {
      const data = JSON.parse(e.data);
      if (data.type === 'notificacion') {
        addNotification(data);
      } else {
        addSystemMessage(data.message || '📩 Mensaje recibido.');
      }
    };

    socket.onerror = e => {
      console.error(' Error WebSocket', e);
      statusText.textContent = 'Error de conexión';
      statusText.className = 'error';
      addSystemMessage(' Error en la conexión WebSocket.');
    };

    socket.onclose = () => {
      console.log(' Conexión cerrada');
      statusText.textContent = 'Desconectado';
      statusText.className = 'desconectado';
      addSystemMessage('🔌 Desconectado del servidor.');
    };
  </script>
</body>
</html>
