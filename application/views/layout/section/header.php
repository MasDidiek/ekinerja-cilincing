<head>
        <meta charset="utf-8">
        <title>Ekinerja - Puskesmas Cilincing</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
        <meta content="Coderthemes" name="author">
        <!-- App favicon -->
      <!-- Layout config Js -->
	    <link rel="shortcut icon" type="image/png" href="<?php echo base_url();?>assets/images/favicon.png"  />   <!-- Sweet Alert css-->


         <meta name="description" content="Sistem penginputan dan pemantauan kinerja pegawai Puskesmas Cilincing" itemprop="description" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="initial-scale = 1.0, user-scalable = no, width=device-width, height=device-height, maximum-scale=1.0" />
        <meta property="og:type" content="application" />
        <meta property="og:site_name" content="ekinerja" />
        <meta property="og:title" content="ekinerja - Sistem penginputan dan pemantauan kinerja pegawai Puskesmas Cilincing" />
        <meta property="og:image" content="<?php echo base_url();?>assets/images/logo-ekin-baru.png" />
        <meta property="og:description" content="Sistem penginputan dan pemantauan kinerja pegawai Puskesmas Cilincing" />
        <meta property="og:url" content="https://ekinerja-puskesmascilincing.jakarta.go.id/" />
        <meta property="og:image:type" content="image/jpeg" />
        <meta property="og:image:width" content="650" />
        <meta property="og:image:height" content="366" />
        <meta name="copyright" content="" itemprop="dateline" />
        <meta name="p:domain_verify" content="2057b86bf61e5a346e22a380c6fecf89" />
        <meta name="robots" content="index, follow" />
        <meta name="googlebot" content="index, follow" />
        <meta name="googlebot-news" content="index, follow" />

        <meta content="Sistem penginputan dan pemantauan kinerja pegawai Puskesmas Cilincing" itemprop="headline" />
        <meta name="keywords" content="ekinerja puskesmas cilincing, ekin puskesmas cilincing, sistem informasi kinerja puskesmas cilincing" itemprop="keywords" />
        <meta name="thumbnailUrl" content="<?php echo base_url();?>assets/images/logo-ekin-baru.png" itemprop="thumbnailUrl" />
        <meta name="dtk:acctype" content="acc-ekinerja" />
        <meta name="dtk:kanalid" content="-" />
        <meta name="dtk:articleid" content="-" />
        <meta name="dtk:createddate" content="-1" />



        <!-- App css -->
        <link href="<?php echo base_url();?>assets/new/css/icons.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url();?>assets/new/css/app.min.css" rel="stylesheet" type="text/css" id="light-style">
        <link href="<?php echo base_url();?>assets/new/css/app-dark.min.css" rel="stylesheet" type="text/css" id="dark-style">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


                        <!-- Datatables css -->
        <link href="<?php echo base_url();?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />


<style>
        .inputfile {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        .inputfile + label {
            font-size: 1.05em;
            font-weight: 700;
            color: white;
            background-color: #2993EA;
            display: inline-block;
            padding:5px 10px;
            border-radius:4px;
        }

        .inputfile:focus + label,
        .inputfile + label:hover {
            background-color: #1B77C2;
        }

        .inputfile + label {
            cursor: pointer; /* "hand" cursor */
        }

        .inputfile:focus + label {
            outline: 1px dotted #000;
            outline: -webkit-focus-ring-color auto 5px;
        }

        .btn-profile{
            width: auto;
            height: 40px;
            line-height: 40px;
            padding:10px;
            color:#999;
            transition: 1.0s;
            border-radius:2px;
            font-size:15px;
            text-transform:uppercase;
            border-bottom:2px solid #EEE;
            font-weight:bold;
        }
        .btn-profile:hover{
             border-bottom:2px solid #50b6ef;
            color:#50b6ef;
            font-weight:bold;

        }

        .active-tab{
            border-bottom:2px solid #50b6ef;
            color:#50b6ef;
            font-weight:bold;
        }

        #snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            color: #fff !important;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
            }

            #snackbar.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
            }

            @-webkit-keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
            }

            @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
            }

            @-webkit-keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
            }

            @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
            }


            .btn-xs{
                padding:2px 8px !important;
                font-size: 12px;
            }

              .table-xs td{
                padding:2px 5px !important;
            }

            .tab-list{
                background-color: #FFF;
                border-bottom: 1px solid #c8dbd3;

            }
            .btn-tab{
                padding: 8px;
                display: inline-block;
                color: #999;
            }

            .btn-tab:hover{
                   color: #477c64;
                font-weight: bold;
            }
            .tab-active{
                color: #477c64;
                font-weight: bold;
            }

            .photo-profile{

                width: 135px;
                height: 165px;
                padding:5px;
                box-shadow: 10px 10px 5px -4px rgba(240,240,240,0.75);
                -webkit-box-shadow: 10px 10px 5px -4px rgba(240,240,240,0.75);
                -moz-box-shadow: 10px 10px 5px -4px rgba(240,240,240,0.75);
                 border-radius: 5px;
            }

            .photo-profile img{

                width: 120px;
                height: 150px;
                      border-radius: 5px;

            }

            .img-thumbnail{
                width:130px;
                height:80px;
            }

</style>


    </head>
