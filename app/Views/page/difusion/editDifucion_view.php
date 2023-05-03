<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="card-title d-flex justify-content-between align-items-center">
            <h5 class="">Editar lista de difusión</h5>
            <button data-bs-toggle="modal" data-bs-target="#contactoModal" class="btn btt-green-whatsApp"> Agregar Contacto</button>
        </div>
    </div>

    <div class="card-body px-4 mx-2">
        <div class="row">
        <div class="col-12 col-md-6 mt-3">
            <label for="name" class="form-label">Nombre</label>
            <input class="form-control" disabled type="text" id="name" name="name" value="<?php echo $difusion->nombre; ?>"></input>
        </div>

        <div class="col-12 col-md-6 mt-3">
            <label for="description" class="form-label">Descripción</label>
            <input class="form-control" disabled type="text" id="description" name="description" value="<?php echo $difusion->descripcion; ?>"></input>
        </div>

        <div class="col-12 col-md-6 mt-3">
            <label for="location" class="form-label">Ubicación</label>
            <input class="form-control" disabled type="text" id="location" name="location" value="<?php echo $difusion->location; ?>"></input>
        </div>

        <div class="col-12 col-md-6 mt-3">
            <label for="totalContactos" class="form-label">Total de contactos</label>
            <input class="form-control" disabled type="text" id="totalContactos" name="totalContactos" value="<?php echo $difusion->totalContactos; ?>"></input>
        </div>
        </div>
    </div>

    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Lada</th>
                        <th scope="col">Numero</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody id="bodyTableDifucion">

                    <?php for ($i = 0; $i < 5; $i++) { ?>
                        <tr scope="row">
                            <td colspan="1">
                                <div class="row gx-3">
                                    <div class="col rounded-3 shine me-1" style="height: 40px; width: 45px;"></div>
                                </div>
                            </td>
                            <td colspan="1">
                                <div class="row gx-3">
                                    <div class="rounded-3 col shine me-1" style="height: 40px; width: 150px;"></div>
                                </div>
                            </td>
                            <td colspan="1">
                                <div class="row gx-3">
                                    <div class="rounded-3 col shine me-1" style="height: 40px; width: 150px;"></div>
                                </div>
                            </td>
                            <td colspan="1">
                                <div class="row gx-3">
                                    <div class="rounded-3 col shine me-1" style="height: 40px; width: 50px;"></div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>
        <div class="pagination justify-content-end"></div>
    </div>
    <input type="hidden" id="idDifucion" value="<?php echo $idDifusion ?>">
</div>

<div class="modal fade" id="contactoModal" tabindex="-1" aria-labelledby="contactoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="contactoModalLabel">Nuevo Contacto</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row needs-validation" novalidate>
          <div class="col-12 mb-3">
            <label for="nombre" class="col-form-label">Nombre:</label>
            <input type="text" require class="form-control" id="nombre" name="nombre">
          </div>
          <div class="col-4 mb-3">
            <label for="lada" class="col-form-label">lada:</label>
            <input type="tel" min="1" require class="form-control" id="lada" name="lada" pattern="[0-9]{2}|[0-9]{3}">
          </div>
          <div class="col-8 mb-3">
            <label for="telefono" class="col-form-label">Teléfono:</label>
             <input type="tel" require class="form-control" id="telefono" name="telefono" pattern="[0-9]{10}">
          </div>
          <div class="col-12 mb-3">
            <label for="empresa" class="col-form-label">Empresa:</label>
             <input type="text" require class="form-control" id="empresa" name="empresa">
          </div>
          <div class="col-12 mb-3">
            <label for="puesto" class="col-form-label">Puesto:</label>
             <input type="text" require class="form-control" id="puesto" name="puesto">
          </div>
          <div class="col-12 mb-3">
            <label for="email" class="col-form-label">Correo:</label>
             <input type="email" require class="form-control" id="email" name="email">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btt-red-cancel" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btt-green-whatsApp" onclick="saveContacto()">Guardar</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/editdifucion.js?v=1.0.5"></script>
<?= $this->endSection() ?>