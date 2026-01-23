<!DOCTYPE html>
<html lang="es">

<head>

    <title><?= isset($empresa) ? $empresa : 'Salamandra' ?></title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Flash Able Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
    <meta name="keywords" content="admin templates, bootstrap admin templates, bootstrap 4, dashboard, dashboard templets, sass admin templets, html admin templates, responsive, bootstrap admin templates free download,premium bootstrap admin templates, Flash Able, Flash Able bootstrap admin template">
    <meta name="author" content="Codedthemes" />

    <!-- Favicon icon -->
    <link rel="icon" href="<?= url_path('public/') ?>assets/images/favicon.ico" type="image/x-icon">
    <!-- fontawesome icon -->
    <link rel="stylesheet" href="<?= url_path('public/') ?>assets/fonts/fontawesome/css/fontawesome-all.min.css">
    <!-- animation css -->
    <link rel="stylesheet" href="<?= url_path('public/') ?>assets/plugins/animation/css/animate.min.css">

    <!-- vendor css -->
    <link rel="stylesheet" href="<?= url_path('public/') ?>assets/plugins/notyf/notyf.min.css">

    <?php if (isset($styles)) : ?>
        <?= $this->function->renderStyles($styles); ?>
    <?php endif ?>

    <link rel="stylesheet" href="<?= url_path('public/') ?>assets/css/style.css">

    <style>
        #table-show {
            display: none;
        }

        .badge-custom {
            font-size: 1.05rem;
            /* Ajusta el tamaño según tus preferencias */
            padding-top: 1.5rem;
            /* Ajusta el espacio superior según tus preferencias */
            padding-bottom: .3rem;
            /* Ajusta el espacio inferior según tus preferencias */
            padding-left: 3rem;
            /* Ajusta el espacio izquierdo según tus preferencias */
            padding-right: 3rem;
            /* Ajusta el espacio derecho según tus preferencias */
        }
    </style>

</head>

<body class="">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ navigation menu ] start -->
    <nav class="pcoded-navbar menupos-fixed menu-light brand-blue ">
        <div class="navbar-wrapper ">
            <div class="navbar-brand header-logo">
                <a href="index.html" class="b-brand">
                    <img src="<?= url_path('public/') ?>assets/images/logo.svg" alt="" class="logo images">
                    <img src="<?= url_path('public/') ?>assets/images/logo-icon.svg" alt="" class="logo-thumb images">
                </a>
                <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
            </div>

            <div class="navbar-content scroll-div">
                <ul class="nav pcoded-inner-navbar">

                    <!-- ZONA ADMINISTRATIVA -->
                    <?php if (adminAccess()) : ?>
                        <li class="nav-item pcoded-menu-caption">
                            <label>Administración</label>
                        </li>

                        <li class="nav-item <?= $controller == 'dashboard' ? 'active' : '' ?>">
                            <a href="<?= url_path('backend/dashboard') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= url_path('backend/usuarios') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                                <span class="pcoded-mtext">Usuarios</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= url_path('backend/clientes') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                                <span class="pcoded-mtext">Clientes</span>
                            </a>
                        </li>

                        <li class="nav-item pcoded-menu-caption">
                            <label>Ventas</label>
                        </li>
                        <li class="nav-item">
                            <a href="<?= url_path('backend/ventas') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-file-text"></i></span>
                                <span class="pcoded-mtext">Ventas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= url_path('backend/detalleventa') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-file-text"></i></span>
                                <span class="pcoded-mtext">Detalle de ventas</span>
                            </a>
                        </li>
                        <li class="nav-item <?= $controller == 'cierre' ? 'active' : '' ?>">
                            <a href="<?= url_path('backend/cierre') ?>" class="nav-link"><span class="pcoded-micon"><i class="feather icon-shuffle"></i></span><span class="pcoded-mtext">Cierre de caja</span></a>
                        </li>

                        <li class="nav-item pcoded-menu-caption">
                            <label>Inventarios</label>
                        </li>

                        <li class="nav-item <?= $controller == 'productos' ? 'active' : '' ?>">
                            <a href="<?= url_path('backend/productos') ?>" class="nav-link"><span class="pcoded-micon"><i class="feather icon-package"></i></span><span class="pcoded-mtext">Productos</span></a>
                        </li>
                        <!-- <li class="nav-item <?= $controller == 'insumos' ? 'active' : '' ?>">
                            <a href="<?= url_path('backend/insumos') ?>" class="nav-link"><span class="pcoded-micon"><i class="feather icon-box"></i></span><span class="pcoded-mtext">Insumos</span></a>
                        </li>
 -->
                        <li class="nav-item <?= $controller == 'sucursales' ? 'active' : '' ?>">
                            <a href="<?= url_path('backend/sucursales') ?>" class="nav-link"><span class="pcoded-micon"><i class="feather icon-smartphone"></i></span><span class="pcoded-mtext">Sucursales</span></a>
                        </li>
                        <li class="nav-item <?= $controller == 'transferencias' ? 'active' : '' ?>">
                            <a href="<?= url_path('backend/transferencias') ?>" class="nav-link"><span class="pcoded-micon"><i class="feather icon-shuffle"></i></span><span class="pcoded-mtext">Traslados</span></a>
                        </li>
                        <li class="nav-item <?= $controller == 'requisiciones' ? 'active' : '' ?>">
                            <a href="<?= url_path('backend/requisiciones') ?>" class="nav-link"><span class="pcoded-micon"><i class="feather icon-file-minus"></i></span><span class="pcoded-mtext">Requisisiones</span></a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= url_path('backend/inventarioproductos/botonPanico') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-disc"></i></span>
                                <span class="pcoded-mtext">Botón de Pánico</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= url_path('backend/carrito') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-shopping-cart"></i></span>
                                <span class="pcoded-mtext">Carrito</span>
                            </a>
                        </li>
                    <?php endif ?>

                    <!-- ZONA VENDEDORES -->
                    <?php if (sellerAccess()) : ?>
                        <li class="nav-item pcoded-menu-caption">
                            <label>PUNTO DE VENTA</label>
                        </li>

                        <li class="nav-item">
                            <a href="<?= url_path('backend/punto') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-shopping-cart"></i></span>
                                <span class="pcoded-mtext">POS</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= url_path('backend/ventas') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-file-text"></i></span>
                                <span class="pcoded-mtext">Ventas</span>
                            </a>
                        </li>
                        <?php if ($this->session->getUserData('userSession', 'usuarioRol') == 2) : ?>
                            <li class="nav-item">
                                <a href="<?= url_path('backend/cierre') ?>" class="nav-link">
                                    <span class="pcoded-micon"><i class="feather icon-file-text"></i></span>
                                    <span class="pcoded-mtext">Cierre de caja</span>
                                </a>
                            </li>
                        <?php endif ?>
                        <!-- <li class="nav-item">
                            <a href="<?= url_path('backend/punto/enviarEmail') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-shopping-cart"></i></span>
                                <span class="pcoded-mtext">Enviar Email/span>
                            </a>
                        </li> -->
                    <?php endif ?>

                    <!-- ZONA PRODUCTORES -->
                    <?php if (OperatorAccess()) : ?>
                        <li class="nav-item pcoded-menu-caption">
                            <label>PRODUCCIÓN</label>
                        </li>

                        <li class="">
                            <a href="<?= url_path('backend/producciones') ?>" class="">Dashboard</a>
                        </li>
                        <li class="">
                            <a href="<?= url_path('backend/producciones/pendientes') ?>" class="">Producción pendiente</a>
                        </li>
                        <li class="">
                            <a href="<?= url_path('backend/producciones/requisiciones') ?>" class="">Requisición pendiente</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= url_path('backend/ventas') ?>" class="nav-link">
                                <span class="pcoded-micon"><i class="feather icon-file-text"></i></span>
                                <span class="pcoded-mtext">Ventas</span>
                            </a>
                        </li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- [ navigation menu ] end -->

    <!-- [ Header ] start -->
    <header class="navbar pcoded-header navbar-expand-lg navbar-light headerpos-fixed">
        <div class="m-header">
            <a class="mobile-menu" id="mobile-collapse1" href="#!"><span></span></a>
            <a href="<?= url_path('backend/dashboard') ?>" class="b-brand">
                <img src="<?= url_path('public/') ?>assets/images/logo.svg" alt="" class="logo images">
                <img src="<?= url_path('public/') ?>assets/images/logo-icon.svg" alt="" class="logo-thumb images">
            </a>
        </div>
        <a class="mobile-menu" id="mobile-header" href="#!">
            <i class="feather icon-more-horizontal"></i>
        </a>
        <div class="collapse navbar-collapse">
            <a href="#!" class="mob-toggler"></a>

            <div class="mt-2">
                <span class="pl-3"><?= $this->session->getUserData('userSession', 'usuarioUser') ?></span class="mt-1 pl-3">
                <h6 class="pl-3"><?= $this->session->getUserData('userSession', 'usuarioRol') == 1 ? 'Administrador' : ($this->session->getUserData('userSession', 'usuarioRol') == 2 ? 'Cajero' : ($this->session->getUserData('userSession', 'usuarioRol') == 3 ? 'Vendedor' : 'Operario')) ?></h6>
            </div>

            <!-- <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <div class="main-search open">
                        <div class="input-group">
                            <input type="text" id="m-search" class="form-control" placeholder="Buscar . . .">
                            <a href="#!" class="input-group-append search-close">
                                <i class="feather icon-x input-group-text"></i>
                            </a>
                            <span class="input-group-append search-btn btn btn-primary">
                                <i class="feather icon-search input-group-text"></i>
                            </span>
                        </div>
                    </div>
                </li>
            </ul> -->

            <ul class="navbar-nav ml-auto">
                <!-- <li>
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown"><i class="icon feather icon-bell"></i></a>
                        <div class="dropdown-menu dropdown-menu-right notification">
                            <div class="noti-head">
                                <h6 class="d-inline-block m-b-0">Notifications</h6>
                                <div class="float-right">
                                    <a href="#!" class="m-r-10">mark as read</a>
                                    <a href="#!">clear all</a>
                                </div>
                            </div>
                            <ul class="noti-body">
                                <li class="n-title">
                                    <p class="m-b-0">NEW</p>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?= url_path('public/') ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>John Doe</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>5 min</span></p>
                                            <p>New ticket Added</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="n-title">
                                    <p class="m-b-0">EARLIER</p>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?= url_path('public/') ?>assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>10 min</span></p>
                                            <p>Prchace New Theme and make payment</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?= url_path('public/') ?>assets/images/user/avatar-3.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Sara Soudein</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>12 min</span></p>
                                            <p>currently login</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?= url_path('public/') ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>30 min</span></p>
                                            <p>Prchace New Theme and make payment</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?= url_path('public/') ?>assets/images/user/avatar-3.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Sara Soudein</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>1 hour</span></p>
                                            <p>currently login</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?= url_path('public/') ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>2 hour</span></p>
                                            <p>Prchace New Theme and make payment</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="noti-footer">
                                <a href="#!">show all</a>
                            </div>
                        </div>
                    </div>
                </li> -->
                <li>
                    <div class="dropdown drp-user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon feather icon-settings"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-notification">
                            <div class="pro-head">
                                <img src="<?= url_path('public/') ?>assets/images/user/avatar-1.jpg" class="img-radius" alt="User-Profile-Image">
                                <span><?= $this->session->getUserData('userSession', 'usuarioNombre') ?></span>
                                <a href="<?= url_path('backend/auth/logout') ?>" class="dud-logout" title="Logout">
                                    <i class="feather icon-log-out"></i>
                                </a>
                            </div>
                            <ul class="pro-body">
                                <li><a href="#!" class="dropdown-item"><i class="feather icon-user"></i> Perfil</a></li>
                                <li><a href="message.html" class="dropdown-item"><i class="feather icon-mail"></i> Mi actividad</a></li>
                                <li><a href="<?= url_path('backend/auth/logout') ?>" class="dropdown-item"><i class="feather icon-lock"></i> Cerrar sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </header>
    <!-- [ Header ] end -->

    <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <?php include VIEWS_PATH . $content ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dangerMessage = "<?= $this->session->getFlashType('danger') ?>";
        const warningMessage = "<?= $this->session->getFlashType('warning') ?>";
        const infoMessage = "<?= $this->session->getFlashType('info') ?>";
        const successMessage = "<?= $this->session->getFlashType('success') ?>";
        const contentMessage = "<?= $this->session->getFlashContent() ?>";
    </script>
    <script src="<?= url_path('public/') ?>assets/js/vendor-all.min.js"></script>
    <script src="<?= url_path('public/') ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?= url_path('public/') ?>assets/js/pcoded.min.js"></script>

    <script src="<?= url_path('public/') ?>assets/plugins/notyf/notyf.min.js"></script>
    <script src="<?= url_path('public/') ?>customs/toastr.js"></script>

    <?php if (isset($scripts)) : ?>
        <?= $this->function->renderScripts($scripts) ?>
    <?php endif ?>

</body>

</html>