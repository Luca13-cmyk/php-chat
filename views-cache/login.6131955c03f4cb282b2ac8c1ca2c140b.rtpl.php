<?php if(!class_exists('Rain\Tpl')){exit;}?><!doctype html>
<html lang="en">
<head>
    <title>Login/Register Modal by Creative Tim</title>

	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />


	<style>body{padding-top: 60px;}</style>

    <link href="/res/site/login/assets/css/bootstrap.css" rel="stylesheet" />

	<link href="/res/site/login/assets/css/login-register.css" rel="stylesheet" />
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">

	<script src="/res/site/login/assets/js/jquery-1.10.2.js" type="text/javascript"></script>
	<script src="/res/site/login/assets/js/bootstrap.js" type="text/javascript"></script>
	<script src="/res/site/login/assets/js/login-register.js" type="text/javascript"></script>

</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                 <a class="btn big-login" data-toggle="modal" style="display: none;" href="javascript:void(0)" onclick="openLoginModal();">Log in</a>
                 <a class="btn big-register" style="display: none;" data-toggle="modal" href="javascript:void(0)">Register</a></div>
            <div class="col-sm-4"></div>
        </div>


		 <div class="modal fade login" id="loginModal">
		      <div class="modal-dialog login animated">
    		      <div class="modal-content">
    		         <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Login</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box">
                             <div class="content">
                                <div class="social">
                                    <!-- <a class="circle github" href="#">
                                        <i class="fa fa-github fa-fw"></i>
                                    </a>
                                    <a id="google_login" class="circle google" href="#">
                                        <i class="fa fa-google-plus fa-fw"></i>
                                    </a>
                                    <a id="facebook_login" class="circle facebook" href="#">
                                        <i class="fa fa-facebook fa-fw"></i>
                                    </a> -->
                                </div>
                                <div class="division">
                                    <div class="line l"></div>
                                      <!-- <span>or</span> -->
                                    <div class="line r"></div>
                                </div>
                                <div class="error"></div>
                                <div class="form loginBox">
                                    <form id="form" method="" action="" accept-charset="UTF-8">
                                    <input id="email" class="form-control" type="text" placeholder="Email" name="desemail">
                                    <input id="password" class="form-control" type="password" placeholder="Password" name="despassword">
                                    <input id="submit-login" class="btn btn-default btn-login" type="button" value="Login">
                                    </form>
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="forgot login-footer">
                            <span>Looking to
                                 <a href="/register">create an account</a>
                            ?</span>
                        </div>
                        
                    </div>
    		      </div>
		      </div>
		  </div>
    </div>

    <script src="https://www.gstatic.com/firebasejs/8.2.7/firebase-app.js"></script>

    <!-- TODO: Add SDKs for Firebase products that you want to use
    https://firebase.google.com/docs/web/setup#available-libraries -->
    <script src="https://www.gstatic.com/firebasejs/8.2.7/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.7/firebase-firestore.js"></script>
    <script src="/res/site/src/util/MD5.js"></script>
    <script src="/res/site/src/util/Firebase.js"></script>
	<script src="/res/site/login/assets/js/Login.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        openLoginModal();
    });
</script>



</body>
</html>
