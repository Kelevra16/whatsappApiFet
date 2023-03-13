<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="row gx-5">
    <div class="col-6">

        <div class="row my-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5>Datos del comando</h5>

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <label for="titulo">Titulo del comando</label>
                                    <input class="form-control" type="text" id="titulo" name="titulo"></input>
                                </div>
                                <div class="col-12 pt-4">
                                    <label for="type">Tipo de Comando</label>
                                    <select class="form-select" aria-label="Default select example" id="type" name="type">
                                            <option selected>Selecciona un tipo</option>
                                            <option value="Suscribir">Suscripción</option>
                                    </select>
                                </div>
                                <div class="col-12 pt-4">
                                    <label for="command">Comando</label>
                                    <input class="form-control" type="text" id="command" name="command"></input>
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
                        <h6>Texto del mensaje</h6>
                        <p>Edita el texto qué mandarás.</p>

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <textarea class="form-control" id="textAreaMessage" rows="4" maxlength="250"></textarea>
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
                        <h6>Opciones de programación</h6>

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-flex justify-content-end pt-3">
                                    <a class="btn btt-red-cancel">Cancelar</a>
                                    <button class="btn px-4 btt-green-whatsApp" onclick="sendCanpaing();">Publicar ahora</button>
                                    <button class="btn px-4 btt-grey-normal disabled">Programar</button>
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
<script src="/assets/js/newcommand.js?v=1.0.0"></script>
<?= $this->endSection() ?>