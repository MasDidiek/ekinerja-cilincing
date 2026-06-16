<div class="leftside-menu">
    <?php $usergroup = $this->session->userdata('usergroup'); ?>
    <!-- LOGO -->
    <a href="<?php echo base_url(); ?>dashboard/index" class="logo text-center logo-light">
        <span class="logo-lg">
            <img src="<?php echo base_url(); ?>assets/images/logo-ekin-baru.png" alt="" height="16">
        </span>
        <span class="logo-sm">
            <img src="<?php echo base_url(); ?>assets/images/logo-ekin-baru.png" alt="" height="16">
        </span>
    </a>

    <!-- LOGO -->
    <a href="<?php echo base_url(); ?>dashboard/index" class="logo text-center logo-dark">
        <span class="logo-lg">
            <img src="<?php echo base_url(); ?>assets/images/logo-ekin-baru.png" alt="" height="40">
        </span>
        <span class="logo-sm">
            <img src="<?php echo base_url(); ?>assets/images/logo-ekin-baru.png" alt="" height="16">
        </span>
    </a>


    <?php
    $usergroup_id = $this->session->userdata('usergroup');

   // print_array($this->session->userdata);
    $usergroup = $this->Auth_model->getMenuUsergroup($usergroup_id);
    //print_array($this->session->userdata);
    
    $usergroup_menu =  $usergroup[0]->usergroup_menu;
    $menu_access = explode(',', $usergroup_menu);  // hasil: ['1','3','4','5']


    $list_menu = $this->Auth_model->get_menu();


    $visible_menu = [];
    foreach ($list_menu as $m) {
        if (in_array($m->id_menu, $menu_access)) {
            $visible_menu[] = $m;
        }
    }


    // print_array($visible_menu);
    // kelompokkan

    // $parents = [];
    $children = [];
    // foreach ($visible_menu  as $m) {

    //    if ($m->menu_level == 1) {
    //         $parents[$m->id_menu] = $m;
    //     } else {
    //         $children[$m->parent_id][] = $m;
    //     }


    // }


    // print_array($children);
    ?>
    <div class="h-100" id="leftside-menu-container" data-simplebar="">

   
        <!--- Sidemenu -->

        <ul class="side-nav">

            <li class="side-nav-title side-nav-item">Navigation</li>

            <?php
            foreach ($visible_menu as $p) {
                $menu_name = $p->menu_name;
                $url_title = str_replace(" ", "", $menu_name);
                $id_menu = $p->id_menu;


                $children = $this->Auth_model->getMenuChild($id_menu);

                echo ' <li class="side-nav-item">';

                if (!empty($children)) {
                    echo '<a data-bs-toggle="collapse" href="#' . $url_title . '" aria-expanded="false" aria-controls="' . $url_title . '" class="side-nav-link">
                                                 <i class="' . $p->icon . '"></i>
                                                <span> ' . $p->menu_name . ' </span>
                                                <span class="menu-arrow"></span>
                                            </a>';
                } else {
                    echo '<a href="' . base_url() . $p->link . '" class="side-nav-link">
                                                 <i class="' . $p->icon . '"></i>
                                                <span> ' . $p->menu_name . ' </span>

                                            </a>';
                }

                // cek apakah parent ini punya child


                if (!empty($children)) {
                    echo '<div class="collapse" id="' . $url_title . '">
                                                 <ul class="side-nav-second-level">';

                    foreach ($children as $c) {
                        echo '   <li>
                                                                    <a href="' . base_url() . $c->link . '">' . $c->menu_name . '</a>
                                                                </li>';
                    }

                    echo '</ul>
                                                </div>';
                }

                echo '</li>';
            }
            ?>


        </ul>


        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->

<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->