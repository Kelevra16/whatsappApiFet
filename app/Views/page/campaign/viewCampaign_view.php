<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="card-title d-flex justify-content-between align-items-center">
            <h5 class="">Ver lista de Difusión</h5>
        </div>
    </div>

    <div class="card-body px-4 mx-2">
        <div class="row">
        <div class="col-12 col-md-6 mt-3">
            <label for="name" class="form-label">Nombre</label>
            <input class="form-control" disabled type="text" id="name" name="name" value="<?php echo $campaign->titulo; ?>"></input>
        </div>

        <div class="col-12">
            <label for="name" class="form-label">Mensaje</label>
            <input class="form-control" disabled type="text" id="name" name="name" value="<?php echo $campaign->mensaje; ?>"></input>
        </div>

        <div class="col-12 col-md-3 mt-3">
            <label for="description" class="form-label">Tipo de Mensaje</label>
            <input class="form-control" disabled type="text" id="description" name="description" value="<?php echo $campaign->messageType; ?>"></input>
        </div>

        <div class="col-6 col-md-3 mt-3">
            <label for="location" class="form-label">Estatus</label>
            <input class="form-control" disabled type="text" id="location" name="location" value="<?php echo $campaign->status; ?>"></input>
        </div>

        <div class="col-6 col-md-3 mt-3">
            <label for="totalContactos" class="form-label">Mensajes Totales</label>
            <input class="form-control" disabled type="text" id="totalContactos" name="totalContactos" value="<?php echo $campaign->totalMensajes; ?>"></input>
        </div>

        <div class="col-6 col-md-3 mt-3">
            <label for="totalContactos" class="form-label">Fecha de Envió</label>
            <input class="form-control" disabled type="text" id="totalContactos" name="totalContactos" value="<?php echo $campaign->dateSend; ?>"></input>
        </div>

        </div>

        <div class="row">
            <div class="col-6 col-md-3 mt-3">
                <label for="totalContactos" class="form-label">Total Enviado</label>
                <input class="form-control" disabled type="text" id="totalContactos" name="totalContactos" value="<?php echo $campaign->totalEnviado; ?>"></input>
            </div>

            <div class="col-6 col-md-3 mt-3">
                <label for="totalContactos" class="form-label">Total Entregado</label>
                <input class="form-control" disabled type="text" id="totalContactos" name="totalContactos" value="<?php echo $campaign->totalEntregado; ?>"></input>
            </div>

            <div class="col-6 col-md-3 mt-3">
                <label for="totalContactos" class="form-label">Total Visto</label>
                <input class="form-control" disabled type="text" id="totalContactos" name="totalContactos" value="<?php echo $campaign->totalVisto; ?>"></input>
            </div>

            <div class="col-6 col-md-3 mt-3">
                <label for="totalContactos" class="form-label">Total Errores</label>
                <input class="form-control" disabled type="text" id="totalContactos" name="totalContactos" value="<?php echo $campaign->totalError; ?>"></input>
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
                        <th scope="col">Estatus</th>
                    </tr>
                </thead>
                <tbody id="bodyTableCampaign">

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
    <input type="hidden" id="idCampaign" value="<?php echo $campaign->id ?>">
</div>


<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/viewCampaign.js?v=1.0.1"></script>
<?= $this->endSection() ?>