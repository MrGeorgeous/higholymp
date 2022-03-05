<!DOCTYPE html>
<html class="<?= $params['html_class'] ?>" lang="<?= $intl->getLocaleTag() ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= $view->render('head') ?>
        <?php $view->style('theme', 'theme:css/theme.css') ?>
        <?php $view->script('theme', 'theme:js/theme.js', ['uikit-sticky',  'uikit-lightbox',  'uikit-parallax']) ?>
    </head>
    <body>

    <div class="uk-offcanvas-content">

        <?php if ($params['logo'] || $view->menu()->exists('main') || $view->position()->exists('navbar')) : ?>
        <div class="<?= $params['classes.navbar'] ?>" <?= $params['classes.sticky'] ?>>
            <div class="uk-container uk-container-center">

                <nav class="uk-navbar">

                    <a class="uk-navbar-brand" href="<?= $view->url()->get() ?>">
                        <?php if ($params['logo']) : ?>
                            <img class="tm-logo uk-responsive-height" src="<?= $this->escape($params['logo']) ?>" alt="">
                            <img class="tm-logo-contrast uk-responsive-height" src="<?= ($params['logo_contrast']) ? $this->escape($params['logo_contrast']) : $this->escape($params['logo']) ?>" alt="">
                        <?php else : ?>
                            <?= $params['title'] ?>
                        <?php endif ?>
                    </a>

                    <?php if ($view->menu()->exists('main') || $view->position()->exists('navbar')) : ?>
                    <div class="uk-navbar-flip uk-hidden-small uk-navbar-center uk-visible@m">
                        <?= $view->menu('main', 'menu-navbar.php') ?>
                        <?= $view->position('navbar', 'position-blank.php') ?>
                    </div>
                    <?php endif ?>

                    
                    <div class="uk-navbar-flip uk-navbar-right">
                        <ul class="uk-navbar-nav">
                            
                            <li class="uk-hidden@m">
                                <a href="#offcanvas-usage" uk-toggle><span uk-icon="icon: menu; ratio: 2"></span></a>

    <div id="offcanvas-usage" uk-offcanvas>
        <div class="uk-offcanvas-bar">

            <button class="uk-offcanvas-close" type="button" uk-close></button>

            <h3>Меню</h3>

            <?= $view->menu('offcanvas', ['class' => 'uk-nav-offcanvas']) ?>

        </div>
    </div>
                            </li>
                        </ul>
                        
                    </div>
                    <?php if ($view->position()->exists('offcanvas') || $view->menu()->exists('offcanvas')) : ?>
                    <?php endif ?>

                </nav>

            </div>
        </div>
        <?php endif ?>

        <?php if ($view->position()->exists('hero')) : ?>
        <div id="tm-hero" class="tm-hero uk-block uk-block-large uk-cover-background uk-flex uk-flex-middle <?= $params['classes.hero'] ?>" <?= $params['hero_image'] ? "style=\"background-image: url('{$view->url($params['hero_image'])}');\"" : '' ?> <?= $params['classes.parallax'] ?>>
            <div class="uk-container uk-container-center">

                <section class="uk-grid uk-grid-match" data-uk-grid-margin>
                    <?= $view->position('hero', 'position-grid.php') ?>
                </section>

            </div>
        </div>
        <?php endif; ?>

        <?php if ($view->position()->exists('top')) : ?>
        <div id="tm-top" class="tm-top uk-block <?= $params['top_style'] ?>">
            <div class="uk-container uk-container-center">

                <section class="uk-grid uk-grid-match" data-uk-grid-margin>
                    <?= $view->position('top', 'position-grid.php') ?>
                </section>

            </div>
        </div>
        <?php endif; ?>

        <div id="tm-main" class="tm-main uk-block <?= $params['main_style'] ?>">
            <div class="uk-container uk-container-center">

                <div class="uk-grid" data-uk-grid-match data-uk-grid-margin>

                    <main class="<?= $view->position()->exists('sidebar') ? 'uk-width-medium-3-4' : 'uk-width-1-1'; ?>">
                        <?= $view->render('content') ?>
                    </main>

                    <?php if ($view->position()->exists('sidebar')) : ?>
                    <aside class="uk-width-medium-1-4 <?= $params['sidebar_first'] ? 'uk-flex-order-first-medium' : ''; ?>">
                        <?= $view->position('sidebar', 'position-panel.php') ?>
                    </aside>
                    <?php endif ?>

                </div>

            </div>
        </div>

        <?php if ($view->position()->exists('bottom')) : ?>
        <div id="tm-bottom" class="tm-bottom uk-block <?= $params['bottom_style'] ?>">
            <div class="uk-container uk-container-center">

                <section class="uk-grid uk-grid-match" data-uk-grid-margin>
                    <?= $view->position('bottom', 'position-grid.php') ?>
                </section>

            </div>
        </div>
        <?php endif; ?>

        <?php if ($view->position()->exists('footer')) : ?>
        <div id="tm-footer" class="tm-footer uk-block uk-block-secondary uk-contrast">
            <div class="uk-container uk-container-center">

                <section class="uk-grid uk-grid-match" data-uk-grid-margin>
                    <?= $view->position('footer', 'position-grid.php') ?>
                </section>

            </div>
        </div>
        <?php endif; ?>

        <?= $view->render('footer') ?>

        </div>

        
                <!-- OFFCANVAS -->
        <div id="offcanvas-nav" class="uk-offcanvas-content" data-uk-offcanvas="flip: true; overlay: true">
            <div class="uk-offcanvas-bar uk-offcanvas-bar-animation uk-offcanvas-slide">
                <button class="uk-offcanvas-close uk-close uk-icon" type="button" data-uk-close></button>

                <?= $view->menu('offcanvas', ['class' => 'uk-nav-offcanvas']) ?>

                <ul class="uk-nav uk-nav-default">
                    <li class="uk-active"><a href="#">Active</a></li>
                    <li class="uk-parent">
                        <a href="#">Parent</a>
                        <ul class="uk-nav-sub">
                            <li><a href="#">Sub item</a></li>
                            <li><a href="#">Sub item</a></li>
                        </ul>
                    </li>
                    <li class="uk-nav-header">Header</li>
                    <li><a href="/cms/programmes"><span class="uk-margin-small-right uk-icon" data-uk-icon="icon: table"></span> Item</a></li>
                    <li><a href="#"><span class="uk-margin-small-right uk-icon" data-uk-icon="icon: thumbnails"></span> Item</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a href="#"><span class="uk-margin-small-right uk-icon" data-uk-icon="icon: trash"></span> Item</a></li>
                </ul>
                <h3>Title</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            </div>
        </div>
        <!-- /OFFCANVAS -->

        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar uk-offcanvas-bar-flip">

                <?php if ($params['logo_offcanvas']) : ?>
                <div class="uk-panel uk-text-center">
                    <a href="<?= $view->url()->get() ?>">
                        <img src="<?= $this->escape($params['logo_offcanvas']) ?>" alt="">
                    </a>
                </div>
                <?php endif ?>

                <?php if ($view->menu()->exists('offcanvas')) : ?>
                    <?= $view->menu('offcanvas', ['class' => 'uk-nav-offcanvas']) ?>
                <?php endif ?>

                <?php if ($view->position()->exists('offcanvas')) : ?>
                    <?= $view->position('offcanvas', 'position-panel.php') ?>
                <?php endif ?>

            </div>
        </div>
        <?php if ($view->position()->exists('offcanvas') || $view->menu()->exists('offcanvas')) : ?>
        <?php endif ?>

    </body>
</html>
