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
                                    <h5 class="card-title">Titulo de la nueva difusión</h5>
                                    <input class="form-control" type="text" id="titleDifusion" name="titleDifusion"></input>
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
                                    <h6>Descripción de la difusión</h6>
                                    <input class="form-control" type="text" id="description" name="description"></input>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6>Ubicación</h6>
                                    <input class="form-control" type="text" id="location" name="location"></input>
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
                        <h6>Archivo a subir</h6>
                        <p>Selecciona el archivo en formato xlxs con los contactos a subir</p>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="" id="noarchive" name="noarchive" onchange="noArchive()">
                              <label class="form-check-label" for="noarchive">
                                crear difusión vacía
                              </label>
                            </div>
                        <div class="d-grid gap-2 d-flex justify-content-end pt-3">
                            <div>
                                <button id="resetImg" class="btn px-4 btt-red-cancel d-none" onclick="resetArchive();">Borrar</button>
                            </div>
                            <div>
                                <label for="excel" id="lbImgArchive" class="btn px-4 btt-grey-normal">Agregar archivo</label>
                                <input class="form-control d-none" type="file" id="excel"  onchange="selectFileArchive();" accept=".xlsx">
                            </div>
                        </div>
                        <div class="row">
                            <label id="nameArchiveSelect" class="d-none truncate pt-3 text-end"></label>
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
                                    <a href="/assets/xlsx/plantilla.xlsx" download class="btn px-4 btt-green-whatsApp" >Descargar plantilla</a>
                                    <a href="/difusion" class="btn btt-red-cancel">Cancelar</a>
                                    <button class="btn px-4 btt-green-whatsApp" onclick="saveDifusion();">Publicar ahora</button>
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
<script src="/assets/js/newdifusion.js?v=1.0.3"></script>
<?= $this->endSection() ?>