<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="row gx-5">
    <div class="col-12 col-md-7 col-lg-6">
        <div class="row my-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="card-title">Nuevo Usuario</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <h6>Nombre</h6>
                                    <input class="form-control" type="text" id="nombre" name="nombre"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Apellido paterno</h6>
                                    <input class="form-control" type="text" id="aPaterno" name="aPaterno"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Apellido materno</h6>
                                    <input class="form-control" type="text" id="aMaterno" name="aMaterno"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Email</h6>
                                    <input class="form-control" type="text" id="email" name="email"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Contraseña</h6>
                                    <input class="form-control" type="text" id="password" name="password"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Confirmar Contraseña</h6>
                                    <input class="form-control" type="text" id="confpassword" name="confpassword"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Empresa</h6>
                                    <?php if($session->role == 0): ?>
                                        <select class="form-select" aria-label="Default select example" id="idEmpresa" name="idEmpresa">
                                            <option selected>Selecciona una empresa</option>
                                            <?php foreach($empresas as $empresa): ?>
                                                <option value="<?= $empresa->id ?>"><?= $empresa->nombre ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input class="form-control" type="text" id="idEmpresa" name="idEmpresa" value="<?= $session->idEmpresa ?>" readonly></input>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Rol</h6>
                                    <?php if($session->role == 0): ?>
                                        <select class="form-select" aria-label="Default select example" id="idRole" name="idRole">
                                            <option selected>Selecciona un rol</option>
                                            <option value="1">Root</option>
                                            <option value="2">Administrador</option>
                                            <option value="3">Usuario</option>
                                        </select>
                                    <?php else: ?>
                                        <select class="form-select" aria-label="Default select example" id="idRole" name="idRole">
                                            <option selected>Selecciona un rol</option>
                                            <option value="2">Administrador</option>
                                            <option value="3">Usuario</option>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h6>Opciones</h6>

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-flex justify-content-end pt-3">
                                    <a href="/usuarios" class="btn btt-red-cancel">Cancelar</a>
                                    <button class="btn px-4 btt-green-whatsApp" onclick="saveUser();">Guardar</button>
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


<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/newuser.js"></script>
<?= $this->endSection() ?>