<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Login\LoginController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Login\LoginController::index');
$routes->post('/login', 'Login\LoginController::login');
$routes->get('/logout', 'Login\LoginController::logout');
$routes->get('/viewfile/(:any)/(:any)', 'Home::fileView/$1/$2');
$routes->post('/webhook', 'UtilsFunction::getAllrequestPOST');
$routes->post('/webhook/(:any)', 'Commands\CommandsController::commandAction/$1');
$routes->get("/cronjob", "CronJob\CronJobController::cronJobStart");
$routes->cli("cronjob", "CronJob\CronJobController::cronJobStart");
$routes->cli("pruebajob", "CronJob\CronJobController::prueba");
$routes->get("/pruebajob", "CronJob\CronJobController::prueba");

$routes->group('difusion', function($routes) {
    $routes->get('/', 'Difusion\DifusionController::index');
    $routes->post('listDifusion', 'Difusion\DifusionController::geListDifucion');
    $routes->post('listDifusion/(:num)', 'Difusion\DifusionController::geListDifucion/$1');
    $routes->post('createdXml', 'Difusion\DifusionController::createdListDifusionByFileXlsx');
    $routes->post('createdlist', 'Difusion\DifusionController::createdListDifusion');
    $routes->get('created', 'Difusion\DifusionController::difusionCreated');
    $routes->get('edit/(:num)', 'Difusion\DifusionController::editListDifucion/$1');
    $routes->post('edit/list/(:num)', 'Difusion\DifusionController::getDataListDifucion/$1');
    $routes->post('edit/delte/contacto', 'Difusion\DifusionController::deleteContacto');
    $routes->post('edit/add/contacto', 'Difusion\DifusionController::saveContacto');
});


$routes->group('campaign', function($routes) {
    $routes->get('new', 'Campaign\CampaignController::index');
    $routes->get('/', 'Dashboard\DashboardController::index');
    $routes->post('list', 'Campaign\CampaignController::getListCampaign');
    $routes->post('list/(:num)', 'Campaign\CampaignController::getListCampaign/$1');
    $routes->post('save', 'Campaign\CampaignController::saveCampaign');
    $routes->post('deleteCampaign', 'Campaign\CampaignController::deleteCampaign');
    $routes->get('viewCampaign/(:num)', 'Campaign\CampaignController::viewCampaign/$1');
    $routes->post('view/contactsList/(:num)', 'Campaign\CampaignController::listContacts/$1');
});

$routes->group('myaccount',function($routes){
    $routes->get('/', 'Myaccount\MyAccountController::index');
});

$routes->group('settings', function($routes) {
    $routes->post('state', 'UtilsFunction::getStateInstancia');
    $routes->post('qr', 'UtilsFunction::getQrImage');
    $routes->post('unlinkaccount', 'UtilsFunction::unlinkAccount');
});

$routes->group('user', function($routes) {
    $routes->post('update', 'Myaccount\MyAccountController::updateSelfUser');
    $routes->post('update/password', 'Myaccount\MyAccountController::updatePassword');
});

$routes->group('comandos', function($routes) {
    $routes->get('/', 'Commands\CommandsController::index');
    $routes->post('list', 'Commands\CommandsController::getListCommands');
    $routes->post('list/(:num)', 'Commands\CommandsController::getListCommands/$1');
    // $routes->post('save', 'Commands\CommandsController::saveDashboard');
    $routes->post('delete', 'Commands\CommandsController::deleteCommand');
    $routes->get('new', 'Commands\CommandsController::newCommand');
});

$routes->group('empresas', function($routes) {
    $routes->get('/', 'Empresas\EmpresasController::index');
    $routes->post('list', 'Empresas\EmpresasController::getListEmpresas');
    $routes->post('list/(:num)', 'Empresas\EmpresasController::getListEmpresas/$1');
    $routes->get('new', 'Empresas\EmpresasController::createdEmpresa');
    $routes->post('save', 'Empresas\EmpresasController::saveEmpresa');
    $routes->post('delete', 'Empresas\EmpresasController::deleteEmpresa');
});

$routes->group('usuarios', function($routes) {
    $routes->get('/', 'Usuarios\UsuariosController::index');
    $routes->post('list', 'Usuarios\UsuariosController::getListUser');
    $routes->post('list/(:num)', 'Usuarios\UsuariosController::getListUser/$1');
    $routes->get('new', 'Usuarios\UsuariosController::newUser');
    $routes->post('save', 'Usuarios\UsuariosController::saveUser');
    $routes->post('delete', 'Usuarios\UsuariosController::deleteUser');
});

$routes->group('logError', function($routes) {
    $routes->get('/', 'LogController::index');
    $routes->post('list', 'LogController::getListLog');
    $routes->post('list/(:num)', 'LogController::getListLog/$1');
    $routes->get('view/(:num)', 'LogController::viewLogInfo/$1');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
