<main class="d-flex flex-nowrap row">

    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-2 col-sm-2 col-md-2 col-xl-3 col-xxl-2 px-sm-2 px-0 bg-white animation-navbar" style="min-height: 90px;">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-black min-vh-100">

                    <a href="/" class="d-flex align-items-center pb-3 mx-auto my-3 my-md-5">
                        <img class="img-fluid" src="/assets/img/dashboard/fecLogo2.svg" alt="fec">
                    </a>

                    <ul class="nav nav-pills flex-column mb-sm-auto mb-auto align-items-center align-items-sm-start col-12" id="menu">

                        <li class="nav-item col-12 my-1">
                            <a href="/campaign" class="nav-link <?php echo ($section === 'campaña')? "active-green":"" ?> align-middle text-black-grey">
                                <img class="svgFontSize" src="/assets/img/dashboard/message.svg" alt="message"> <span class="ms-1 d-none d-lg-inline">Campañas</span>
                            </a>
                        </li>

                        <li class="nav-item col-12 my-1">
                            <a href="/difusion" class="nav-link <?php echo ($section === 'difusión')? "active-green":"" ?> align-middle text-black-grey">
                                <img class="svgFontSize" src="/assets/img/dashboard/building.svg" alt="building"> <span class="ms-1 d-none d-lg-inline">Lista de difusión</span></a>
                        </li>

                        <?php if ($session->get("role") == 0){ ?>
                            <li class="nav-item col-12 my-1">
                                <a href="/empresas" class="nav-link <?php echo ($section === 'empresas')? "active-green":"" ?> align-middle text-black-grey">
                                <i class="bi bi-building-fill"></i> <span class="ms-1 d-none d-lg-inline">Empresas</span></a>
                            </li>
                        <?php } ?>

                        <?php if ($session->get("role") == 0 || $session->get("role") == 1){ ?>
                            <li class="nav-item col-12 my-1">
                                <a href="/usuarios" class="nav-link <?php echo ($section === 'usuarios')? "active-green":"" ?> align-middle text-black-grey">
                                <i class="bi bi-person-circle"></i> <span class="ms-1 d-none d-lg-inline">Usuarios</span></a>
                            </li>
                        <?php } ?>

                        <?php if ($session->get("role") == 0){ ?>
                            <li class="nav-item col-12 my-1">
                                <a href="/comandos" class="nav-link <?php echo ($section === 'comandos')? "active-green":"" ?> align-middle text-black-grey">
                                <i class="bi bi-terminal"></i> <span class="ms-1 d-none d-lg-inline">Comandos</span></a>
                            </li>
                        <?php } ?>

                        <li class="nav-item col-12 my-1">
                            <a href="/logError" class="nav-link <?php echo ($section === 'log')? "active-green":"" ?> align-middle text-black-grey">
                            <i class="bi bi-exclamation-triangle"></i> <span class="ms-1 d-none d-lg-inline">Registro Errores</span></a>
                        </li>

                        <!-- <li class="nav-item col-12 my-1">
                            <a href="#" class="nav-link align-middle text-black-grey">
                                <img class="svgFontSize" src="/assets/img/dashboard/bar_chart.svg" alt="barChar"> <span class="ms-1 d-none d-lg-inline">Estadísticas</span> </a>
                        </li> -->
                    </ul>
                    <hr>
                    <div class="col-12 dropup dropup-end">
                        <a data-bs-toggle="dropdown" aria-expanded="false" class="col-12 btn <?php echo ($section === 'myAccount')? "active-green":"btn-grey-bg" ?>  mb-5 px-0" ><i class="bi bi-person-circle"></i> <span class="ms-1 d-none d-xl-inline">Mi cuenta</span></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/myaccount">
                                    <i class="bi bi-person-circle"></i> <span class="ms-1">Ajustes</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/logout">
                                    <i class="bi bi-box-arrow-right"></i> <span class="ms-1">Cerrar sesión</span>
                                </a>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col py-5" style="height: 100vh; overflow-y:auto; overflow-x: hidden !important;">
                <div class="container-fluid pb-5 mt-5 ">
                    <div class="row">
                        <div class="col-12 px-3 px-md-3 px-lg-4 px-xl-5" style="min-height: 400px;">
                            <?= $this->renderSection('content') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</main>