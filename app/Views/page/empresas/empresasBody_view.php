<?= $this->extend('page/dashboard/dashboardBody_view') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="card-title d-flex justify-content-between align-items-center">
            <h5 class="">Empresas</h5>
            <a href="/empresas/new" class="btn btt-green-whatsApp">Nueva Empresa</a>
        </div>
    </div>
    <div class="pagination justify-content-end"></div>
    <div class="card-body px-4 pt-4 mt-2 mx-2">
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Dirección</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody id="bodyTableEmpresa">

            <?php for ($i=0; $i < 5; $i++) { ?>
                <tr scope="row" >
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                        </div>
                    </td>
                    <td colspan="1">
                        <div class="row gx-3">
                            <div class="rounded-3 col shine me-1"  style="height: 30px; width: 150px;"></div>
                        </div>
                    </td>
                </tr>
            <?php }?>

            </tbody>
        </table>
        </div>
        <div class="pagination justify-content-end"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('jsExtra') ?>
<script src="/assets/js/empresas.js"></script>
<?= $this->endSection() ?>