<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notificaciones en tiempo real</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      background: #f5f7fa;
    }
    .noti-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 1rem;
    }
    .noti-card {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 12px;
      padding: 1rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .noti-card:hover {
      transform: translateY(-3px);
    }
    .noti-header {
      font-weight: 600;
      color: #1e3a8a;
      font-size: 1.1rem;
    }
    .noti-body {
      margin: 0.5rem 0;
    }
    .noti-footer {
      display: flex;
      justify-content: space-between;
      font-size: 0.8rem;
      color: gray;
    }
    .status-badge {
      padding: 0.2rem 0.6rem;
      border-radius: 8px;
      color: #fff;
      font-weight: 600;
      font-size: 0.8rem;
    }
    .status-pending { background-color: #f59e0b; }
    .status-resolved { background-color: #16a34a; }
    .status-other { background-color: #3b82f6; }

    #statusText.conectado { color: green; font-weight: bold; }
    #statusText.desconectado { color: red; font-weight: bold; }
    #statusText.error { color: orange; font-weight: bold; }

    .edit-btn {
      font-size: 0.8rem;
    }
  </style>
</head>
<body>
  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>🔔 Notificaciones en tiempo real</h3>
      <div>
        Estado: <span id="statusText" class="desconectado">Desconectado</span>
      </div>
    </div>

    <a href="<?= base_url('/notificaciones/crear') ?>" class="btn btn-primary mb-3">➕ Nueva notificación</a>

    <div id="noti-container" class="noti-grid">
      <div class="text-center text-muted">Conectando al servidor...</div>
    </div>
  </div>

  <script>
    const notiContainer = document.getElementById('noti-container');
    const statusText = document.getElementById('statusText');

    // 📅 Formato bonito de fecha
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

    // 🧱 Crear tarjeta visual (evita duplicados)
    function addNotification(data) {
      if (document.getElementById(`noti-${data.id}`)) return;

      const div = document.createElement('div');
      div.classList.add('noti-card');
      div.id = `noti-${data.id}`;

      let statusClass = 'status-other';
      if (data.estado?.toLowerCase() === 'pendiente') statusClass = 'status-pending';
      if (data.estado?.toLowerCase() === 'resuelto') statusClass = 'status-resolved';

      div.innerHTML = `
        <div class="noti-header">${data.cliente}</div>
        <div class="noti-body">${data.problema}</div>
        <div class="noti-footer">
          <span>${formatoBonito(data.fechahora)}</span>
          <span class="status-badge ${statusClass}">${data.estado}</span>
        </div>
        <button class="btn btn-sm btn-outline-primary mt-2 edit-btn" onclick="editarNotificacion('${data.id}')">✏️ Editar</button>
      `;

      // Agregar al inicio (más reciente primero)
      notiContainer.prepend(div);
    }

    function addSystemMessage(msg) {
      const div = document.createElement('div');
      div.classList.add('text-center', 'text-muted');
      div.textContent = msg;
      notiContainer.appendChild(div);
    }

    function editarNotificacion(id) {
      window.location.href = `/notificaciones/editar/${id}`;
    }

    // 🔄 Cargar notificaciones existentes sin borrar las anteriores
    async function cargarNotificacionesIniciales() {
      try {
        const res = await fetch('/api/notificaciones');
        const datos = await res.json();

        if (datos.length === 0) {
          addSystemMessage('No hay notificaciones registradas.');
        } else {
          datos.forEach(n => addNotification(n));
        }
      } catch (err) {
        console.error('Error cargando notificaciones:', err);
        addSystemMessage('⚠️ No se pudieron cargar las notificaciones.');
      }
    }

    // 🔌 WebSocket
    const socket = new WebSocket('ws://localhost:8080');

    socket.onopen = () => {
      statusText.textContent = 'Conectado';
      statusText.className = 'conectado';
      addSystemMessage('✅ Conectado al servidor.');
      cargarNotificacionesIniciales(); // carga solo una vez
    };

    socket.onmessage = e => {
      const data = JSON.parse(e.data);
      if (data.type === 'notificacion') addNotification(data);
    };

    socket.onerror = e => {
      statusText.textContent = 'Error';
      statusText.className = 'error';
    };

    socket.onclose = () => {
      statusText.textContent = 'Desconectado';
      statusText.className = 'desconectado';
    };
  </script>
</body>
</html>
