<style type="text/css">
    .topnavbar .navbar-header {
        background-image: none;
        background-color: transparent;
        background-repeat: no-repeat;
        filter: none;
    }
</style>
<header class="topnavbar-wrapper">
    <!-- START Top Navbar-->
    <nav role="navigation" class="navbar topnavbar">
        <!-- START navbar header-->
        <?php $display = config_item('logo_or_icon'); ?>
        <div class="navbar-header">
            <?php if ($display == 'logo' || $display == 'logo_title') { ?>
                <a href="#/" class="navbar-brand">
                    <div class="brand-logo">
                        <img src="<?= base_url() . config_item('company_logo') ?>" alt="App Logo" class="img-responsive">
                    </div>
                    <div class="brand-logo-collapsed">
                        <img style="width: 48px;height: 48px;border-radius: 50px" src="<?= base_url() . config_item('company_logo') ?>" alt="App Logo" class="img-responsive">
                    </div>
                </a>
            <?php }
            ?>
        </div>
        <!-- END navbar header-->
        <!-- START Nav wrapper-->
        <div class="navbar-collapse collapse">
            <!-- START Left navbar-->
            <ul class="nav navbar-nav text-uppercase" style="float: right;">
                <li><a href="https://www.draravindsivf.com/">Home</a></li>
                <li><a href="https://www.draravindsivf.com/hrm/frontend">Career</a></li>
            </ul>
            <!-- <ul class="nav navbar-nav">
                <li><a href="<?= base_url() ?>frontend"><?= lang('all_job_circular') ?></a></li>
                <li><a href="<?= base_url() ?>knowledgebase"><?= lang('knowledgebase') ?></a></li>
                <li class="pull-right"><a href="<?= base_url() ?>login"><?= lang('login') ?></a></li>
            </ul> -->
    </nav>
    <!-- END Top Navbar-->
</header>