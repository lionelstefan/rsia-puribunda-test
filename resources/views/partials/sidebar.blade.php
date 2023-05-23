<link href="{{mix('css/sidebar.css')}}" rel="stylesheet" type="text/css">
<link href="{{mix('css/main.css')}}" rel="stylesheet" type="text/css">
<!-- <script type="text/javascript" src="js/sidebar.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script>
    jQuery(function($) {
        $(".sidebar-dropdown > a").click(function() {
            $(".sidebar-submenu").slideUp(200);
            if ($(this).parent().hasClass("active")) {
                $(".sidebar-dropdown").removeClass("active");
                $(this).parent().removeClass("active");
            } else {
                $(".sidebar-dropdown").removeClass("active");
                $(this).next(".sidebar-submenu").slideDown(200);
                $(this).parent().addClass("active");
            }
        });

        $("#close-sidebar").click(function() {
            $(".page-wrapper").removeClass("toggled");
        });

        $("#show-sidebar").click(function() {
            $(".page-wrapper").addClass("toggled");
        });
    });
</script>

<!-- <div class="page-wrapper chiller-theme toggled">
    <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
        <i class="fas fa-bars"></i>
    </a> -->
    <!-- Button  -->

    <nav id="sidebar" class="sidebar-wrapper">
        <div class="sidebar-content">
            <div class="sidebar-brand">
                <a href="{{ url('home') }}">dashboard</a>
                <div id="close-sidebar"><i class="fas fa-times"></i></div>
            </div>
            <!-- sidebar-brand -->
            <!-- <div class="sidebar-header"> -->
            <!--     <div class="user-pic" style="color:#fff;"> -->
            <!--         <i class="fa fa-user-circle fa-4x" aria-hidden="true"></i> -->
            <!--     </div> -->
            <!--     <div class="user-info"> -->
            <!--         <span class="user-name"> <strong>Renaldy Cahya</strong></span> -->
            <!--         <span class="user-role">Administrator</span> -->
            <!--         <span class="user-status"><i class="fa fa-circle"></i> <span>Online</span></span> -->
            <!--     </div> -->
            <!-- </div> -->
            <div class="sidebar-menu">
                <ul>
                    <li class="header-menu"><span>General</span></li>
                    <li class="sidebar-dropdown">
                        <a href="#"><i class="fa fa-calendar"></i><span>Unit</span></a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li><a href="{{ url('list-unit') }}">List</a></li>
                                <li><a href="{{ url('create-unit') }}">Create</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="sidebar-dropdown">
                        <a href="#"><i class="fa fa-calendar"></i><span>Jabatan</span></a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li><a href="{{ url('list-jabatan') }}">List</a></li>
                                <li><a href="{{ url('create-jabatan') }}">Create</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="sidebar-dropdown">
                        <a href="#"><i class="fa fa-calendar"></i><span>Karyawan</span></a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li><a href="{{ url('list-karyawan') }}">List</a></li>
                                <li><a href="{{ url('create-karyawan') }}">Create</a></li>
                            </ul>
                        </div>
                    </li>
                    

                </ul>
            </div>
            <!-- sidebar-menu  -->
        </div>
        <!-- sidebar-content  -->
        <div class="sidebar-footer">
            <a href="{{ url('logout') }}">
                <i class="fa fa-power-off"></i>
            </a>
        </div>
        <!-- sidebar-footer  -->
    </nav>
    <!-- sidebar-wrapper  -->
    <!-- page-content" -->
<!-- </div> -->
<!-- page-wrapper" -->
