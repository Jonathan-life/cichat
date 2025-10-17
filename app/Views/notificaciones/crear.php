<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Crear Notificación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
</head>
<body>
<div class="container mt-4">
  <h1>Crear Nueva Notificación</h1>

  <?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-success">
      <?= session()->getFlashdata('msg') ?>
    </div>
  <?php endif; ?>

  <form method="post" action="/notificaciones/guardar">
    <div class="mb-3">
      <label for="cliente" class="form-label">Cliente</label>
      <input type="text" name="cliente" id="cliente" class="form-control" required />
    </div>

    <div class="mb-3">
      <label for="problema" class="form-label">Problema</label>
      <textarea name="problema" id="problema" class="form-control" rows="3" required></textarea>
    </div>

    <div class="mb-3">
      <label for="fechahora" class="form-label">Fecha y Hora</label>
      <input type="datetime-local" name="fechahora" id="fechahora" class="form-control" required />
    </div>

    <div class="mb-3">
      <label for="estado" class="form-label">Estado</label>
      <select name="estado" id="estado" class="form-select" required>
        <option value="Pendiente" selected>Pendiente</option>
        <option value="En proceso">En proceso</option>
        <option value="Resuelto">Resuelto</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="/notificaciones" class="btn btn-secondary">Ver Lista</a>
  </form>
</div>
</body>
</html>
