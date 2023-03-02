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
                                    <h5 class="card-title">Nombre de la Empresa</h5>
                                    <input class="form-control" type="text" id="tittleEmpresa" name="tittleEmpresa"></input>
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
                                    <h6>Dirección</h6>
                                    <input class="form-control" type="text" id="direccion" name="direccion"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Descripción</h6>
                                    <input class="form-control" type="text" id="descripcion" name="descripcion"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Teléfono</h6>
                                    <input class="form-control" type="text" id="telefono" name="telefono"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Apikey</h6>
                                    <input class="form-control" type="text" id="apikey" name="apikey"></input>
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
                                    <a href="/empresas" class="btn btt-red-cancel">Cancelar</a>
                                    <button class="btn px-4 btt-green-whatsApp" onclick="saveEmpresa();">Guardar</button>
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
<script src="/assets/js/newempresa.js"></script>
<?= $this->endSection() ?>