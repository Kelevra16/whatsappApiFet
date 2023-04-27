<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="row pb-3">
    <div class="col-12 text-end">
        <a href="/difusion/created" class="btn btt-green-whatsApp">Agregar nueva lista</a>
    </div>
</div>
<div class="pagination justify-content-end"></div>
<div id="bodyDifusionList" class="row g-4 pb-5" style="height: 100%;">
    <div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
        <div class="card shine" style="height: 350px; width: 100%; background-repeat-y: repeat;"></div>
    </div>

    <div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
        <div class="card shine" style="height: 350px; width: 100%; background-repeat-y: repeat;"></div>
    </div>

    <div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
        <div class="card shine" style="height: 350px; width: 100%; background-repeat-y: repeat;"></div>
    </div>

</div>


<div class="modal fade" id="modalPass" tabindex="-1" aria-labelledby="modalPassLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPassLabel">Cambio de contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="col-12 mt-3">
            <label for="passOrig" class="form-label">Contraseña Anterior</label>
            <input class="form-control" type="password" id="passOrig" name="passOrig"></input>
        </div>

        <div class="col-12 mt-3">
            <label for="newPass" class="form-label">Nueva contraseña</label>
            <input class="form-control" type="password" id="newPass" name="newPass"></input>
        </div>

        <div class="col-12 mt-3">
            <label for="confirPass" class="form-label">Confirmar Contraseña</label>
            <input class="form-control" type="password" id="confirPass" name="confirPass"></input>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button onclick="changePassword();" type="button" class="btn btt-green-whatsApp">Guardar</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/difusion.js?v=1.0.2"></script>
<?= $this->endSection() ?>