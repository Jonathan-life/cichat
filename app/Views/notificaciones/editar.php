<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar notificación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="card shadow-sm p-4">
      <h3 class="mb-4">✏️ Editar notificación</h3>

      <form action="<?= base_url('/notificaciones/actualizar/' . $notificacion->id) ?>" method="POST">
        <div class="mb-3">
          <label class="form-label">Cliente</label>
          <input type="text" name="cliente" class="form-control" value="<?= esc($notificacion->cliente) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Problema</label>
          <textarea name="problema" class="form-control" rows="3" required><?= esc($notificacion->problema) ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Fecha y hora</label>
          <input type="datetime-local" name="fechahora" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($notificacion->fechahora)) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="Pendiente" <?= $notificacion->estado == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
            <option value="Resuelto" <?= $notificacion->estado == 'Resuelto' ? 'selected' : '' ?>>Resuelto</option>
          </select>
        </div>

        <button type="submit" class="btn btn-success">💾 Guardar cambios</button>
        <a href="<?= base_url('/notificaciones') ?>" class="btn btn-secondary">⬅️ Volver</a>
      </form>
    </div>
  </div>
</body>
</html>
