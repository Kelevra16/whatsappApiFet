<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="row gx-5">
    <div class="col-12 col-md-7 col-lg-8">
        <div class="row my-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <label for="user" class="form-label">Usuario</label>
                                    <input class="form-control" type="text" id="user" name="user" disabled value="<?php echo $user->username; ?>"></input>
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="nameUser" class="form-label">Nombre</label>
                                    <input class="form-control" type="text" id="nameUser" name="nameUser" value="<?php echo $user->nombre; ?>"></input>
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="apePaterno" class="form-label">Apellido Paterno</label>
                                    <input class="form-control" type="text" id="apePaterno" name="apePaterno" value="<?php echo $user->aPaterno; ?>"></input>
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="apeMaterno" class="form-label">Apellido Materno</label>
                                    <input class="form-control" type="text" id="apeMaterno" name="apeMaterno" value="<?php echo $user->aMaterno; ?>"></input>
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="email" class="form-label">Correo</label>
                                    <input class="form-control" type="text" id="email" name="email" value="<?php echo $user->correo; ?>"></input>
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="rol" class="form-label">Rol</label>
                                    <input class="form-control" disabled type="text" id="rol" name="rol" value="<?php echo $roles->nombre; ?>"></input>
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="company" class="form-label">Empresa</label>
                                    <input class="form-control" type="text" id="company" name="company" disabled value="<?php echo $empresa->nombre; ?>"></input>
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="company" class="form-label">Estado de servicio</label>
                                    <div id="status">
                                        <div class="alert alert-warning" role="alert">
                                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                            cargando....
                                        </div>
                                    </div>
                                </div>

                                <?php if($user->role <= 1){ ?>
                                <div id="desvid" class="col-12 mt-3 row d-none">
                                    <label for="company" class="form-label">Desvincular Numero</label>
                                    <div class="" >
                                        <button class="btn btt-red-cancel" onclick="unlinkAccount()">Desvincular</button>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if($user->role <= 1){ ?>
        <div class="row my-4">
            <div class="col">
                <div id="cardqr" class="card d-none">
                    <div class="card-body">
                        <h6>Qr para Enlazar numero</h6>
                        <div id="loadingQR" class="alert alert-warning" role="alert">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            cargando QR....
                        </div>
                        <div class="text-center" id="qr"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="row my-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h6>Acciones</h6>

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-flex justify-content-end pt-3">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalPass" class="btn btt-grey-normal">Cambiar contraseña</button>
                                    <button class="btn px-4 btt-green-whatsApp" onclick="saveChangesUser();">Guardar cambios</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
<script src="/assets/js/myaccount.js?v=1.0.1"></script>
<?= $this->endSection() ?>