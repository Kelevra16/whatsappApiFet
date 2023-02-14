<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="card-title d-flex justify-content-between align-items-center">
            <h5 class="">Campañas</h5>
            <a href="/campaign/new" class="btn btt-green-whatsApp"> Nueva Campaña</a>
        </div>
    </div>
    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Titulo del mensaje</th>
                    <th scope="col">Fecha de publicación</th>
                    <th scope="col">Alcance</th>
                    <th scope="col">Estatus</th>
                </tr>
            </thead>
            <tbody id="bodyTableCampaign">
                <tr scope="row" >
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 shine me-1"  style="height: 45px; width: 45px;"></div>
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 80px;"></div>
                            <div class="rounded-3 col shine me-1"  style="height: 38px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 20px; width: 100px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 50px;"></div>
                        </div>
                    </td>
                </tr>

                <tr scope="row" >
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 shine me-1"  style="height: 45px; width: 45px;"></div>
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 80px;"></div>
                            <div class="rounded-3 col shine me-1"  style="height: 38px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 20px; width: 100px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 50px;"></div>
                        </div>
                    </td>
                </tr>

                <tr scope="row" >
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 shine me-1"  style="height: 45px; width: 45px;"></div>
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 80px;"></div>
                            <div class="rounded-3 col shine me-1"  style="height: 38px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 20px; width: 100px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 50px;"></div>
                        </div>
                    </td>
                </tr>

                <tr scope="row" >
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 shine me-1"  style="height: 45px; width: 45px;"></div>
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 80px;"></div>
                            <div class="rounded-3 col shine me-1"  style="height: 38px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 20px; width: 100px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 50px;"></div>
                        </div>
                    </td>
                </tr>


            </tbody>
        </table>
        </div>
        <div class="pagination justify-content-end"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/campaign.js"></script>
<?= $this->endSection() ?>