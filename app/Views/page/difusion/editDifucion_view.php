<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="card-title d-flex justify-content-between align-items-center">
            <h5 class="">Editar lista de difusión</h5>
            <a href="#" disabled class="btn btt-green-whatsApp disabled"> Agregar Contacto</a>
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

<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/editdifucion.js"></script>
<?= $this->endSection() ?>