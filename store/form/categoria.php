<!-- Este contenido irá dentro del div id="vista-formulario" -->
<style>
 .form-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  
  padding: 50px 20px;
  background-color: #f0f2f5;
  min-height: 87vh;
  font-family: 'Segoe UI', sans-serif;
}

.form-container {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  width: 100%;
  max-width: 520px;
  padding: 40px 35px;
  animation: fadeIn 0.8s ease-out;
}

.form-container h2 {
  margin-bottom: 25px;
  color: #2e2e2e;
  text-align: center;
  font-size: 1.8rem;
  letter-spacing: 1px;
  font-weight: 600;
}

label {
  display: block;
  margin-bottom: 6px;
  color: #444;
  font-weight: 500;
  font-size: 14px;
}

input[type="text"] {
  width: 100%;
  padding: 12px 14px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 15px;
  background: #f9f9f9;
  transition: border 0.3s, background-color 0.3s;
}

input[type="text"]:focus{
  border-color: #007f5f;
  background-color: #fff;
  outline: none;
}


.form-check {
  display: flex;
  align-items: center;
  margin-bottom: 25px;
}

.form-check input {
  margin-right: 10px;
}

.btn-group {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.btn {
  flex: 1;
  padding: 12px 0;
  border: none;
  border-radius: 6px;
  color: #fff;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
}

.btn-success {
  background-color: #007f5f;
}

.btn-success:hover {
  background-color: #006b4f;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 127, 95, 0.2);
}

.btn-secondary {
  background-color: #6c757d;
}

.btn-secondary:hover {
  background-color: #5a6268;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(90, 98, 104, 0.2);
}

.text-danger {
  color: #dc3545;
  font-size: 13px;
  margin-top: -10px;
  margin-bottom: 10px;
}

/* Animación al aparecer */
@keyframes fadeIn {
  from {
    transform: translateY(30px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

</style>

<div class="form-wrapper">
  <div class="form-container">
    <h2>Nueva Categoría</h2>
    <form action="../api_store/save_category.php" method="POST" id="formCategoria">
      <label for="nombre">Nombre de la categoría <span class="text-danger">*</span></label>
      <input type="text" id="nombre" name="nombre" required>

      <div class="form-check">
        <input type="checkbox" id="activo" name="activo" checked>
        <label for="activo">Categoría activa</label>
      </div>

      <div class="btn-group">
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="#" class="btn btn-secondary" onclick="mostrarInicio()">Cancelar</a>
      </div>
    </form>
  </div>
</div>