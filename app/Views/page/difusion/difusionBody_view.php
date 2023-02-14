<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="row pb-3">
    <div class="col-12 text-end">
        <a href="/difusion/created" class="btn btt-green-whatsApp">Agregar nueva lista</a>
    </div>
</div>
<div class="pagination justify-content-end"></div>
<div id="bodyDifusionList" class="row g-4 pb-5" style="height: 100%;">
    <div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
        <div class="card shine" style="height: 350px; width: 100%; background-repeat-y: repeat;"></div>
    </div>

    <div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
        <div class="card shine" style="height: 350px; width: 100%; background-repeat-y: repeat;"></div>
    </div>

    <div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
        <div class="card shine" style="height: 350px; width: 100%; background-repeat-y: repeat;"></div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/difusion.js"></script>
<?= $this->endSection() ?>