<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="row gx-5">
    <div class="col-6">
        <div class="row my-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Seleccionar lista de difusión</h5>
                        <input class="form-control dropdown-toggle" type="text" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <div class="dropdown-menu dropdownMenuButton1" aria-labelledby="dropdownMenuButton1" style="min-width: 500px;">
                            <div class="container-fluid">
                                <div class="row">
                                    <ul class="list-group" id="groupListDifu" aria-labelledby="dropdownMenuButton1" style="max-height: 400px; overflow:auto;">
                                        <li class="list-group-item py-3" style="border:none">
                                            <input class="form-check-input me-1" type="checkbox" value="123" data-name="First checkbox" id="firstCheckbox" onchange="checkGroup(this);">
                                            <label class="form-check-label" for="firstCheckbox">First checkbox</label>
                                        </li>
                                        <li class="list-group-item py-3" style="border:none">
                                            <input class="form-check-input me-1" type="checkbox" value="321" data-name="Second checkbox" id="secondCheckbox" onchange="checkGroup(this);">
                                            <label class="form-check-label" for="secondCheckbox">Second checkbox</label>
                                        </li>
                                        <li class="list-group-item py-3" style="border:none">
                                            <input class="form-check-input me-1" type="checkbox" value="231" data-name="Third checkbox" id="thirdCheckbox" onchange="checkGroup(this);">
                                            <label class="form-check-label" for="thirdCheckbox">Third checkbox</label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="d-grid gap-2 d-flex justify-content-start pt-3">
                                    <button class="btn btt-green-save" onclick="saveGroupSelect();">Guardar</button>
                                    <button id="cancelDropdown" class="btn btt-red-cancel" onclick="cancelGroupSelect();">Cancelar</button>
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
                        <h6>Contenido multimedia</h6>
                        <p>Seleccionar el contenido multimedia que vas a compartir.</p>
                        <div class="d-grid gap-2 d-flex justify-content-end pt-3">
                            <div>
                                <button id="resetImg" class="btn px-4 btt-red-cancel d-none" onclick="resetArchive();">Borrar</button>
                            </div>
                            <div>
                                <label for="imgFile" id="lbImgFile" class="btn px-4 btt-green-whatsApp">Agregar foto</label>
                                <input class="form-control d-none" type="file" id="imgFile" onchange="selectFileImage();" accept="image/*">
                            </div>
                            <div>
                                <label for="archiveFile" id="lbImgArchive" class="btn px-4 btt-grey-normal">Agregar documento</label>
                                <input class="form-control d-none" type="file" id="archiveFile"  onchange="selectFileArchive();" accept="video/*,.mp3,.pdf,.doc">
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
                        <h6>Titulo de la campaña</h6>
                        <p>Escribe el titulo de la campaña.</p>

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <input class="form-control" type="text" id="titulo" name="titulo"></input>
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
<script src="/assets/js/newcampaign.js?v=1.0.4"></script>
<?= $this->endSection() ?>