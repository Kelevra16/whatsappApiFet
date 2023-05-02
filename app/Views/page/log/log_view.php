<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="card-title d-flex justify-content-between align-items-center">
            <h5 class="">Registro de errores</h5>
        </div>
    </div>
    <div class="pagination justify-content-end"></div>
    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Mensaje</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody id="bodyTableLog">

                    <?php for ($i = 0; $i < 5; $i++) { ?>
                        <tr scope="row">
                            <td colspan="1">
                                <div class="row gx-3">
                                    <div class="rounded-3 col shine me-1" style="height: 30px; width: 40px;"></div>
                                </div>
                            </td>
                            <td colspan="1">
                                <div class="row gx-3">
                                    <div class="rounded-3 col shine me-1" style="height: 30px; width: 70px;"></div>
                                </div>
                            </td>
                            <td colspan="1">
                                <div class="row gx-3">
                                    <div class="rounded-3 col shine me-1" style="height: 30px; width: 50px;"></div>
                                </div>
                            </td>
                            <td colspan="1">
                                <div class="row gx-3">
                                    <div class="rounded-3 col shine me-1" style="height: 30px; width: 150px;"></div>
                                </div>
                            </td>
                            <td colspan="1">
                                <div class="row gx-3">
                                    <div class="rounded-3 col shine me-1" style="height: 30px; width: 50px;"></div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>
        <div class="pagination justify-content-end"></div>
    </div>
</div>

<div class="modal fade" id="modalLog" tabindex="-1" aria-labelledby="modalLogLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="modalContentLog">

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/logError.js"></script>
<?= $this->endSection() ?>