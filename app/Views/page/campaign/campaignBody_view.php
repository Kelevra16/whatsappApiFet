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
                    <th scope="col" class="text-center">Estatus</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody id="bodyTableCampaign">

            </tbody>
        </table>
        </div>
        <div class="pagination justify-content-end"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/campaign.js?v=1.0.2"></script>
<?= $this->endSection() ?>