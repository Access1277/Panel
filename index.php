<?php

//FILL UP ALL VARIABLE [ $ ] | BASE TO YOUR DESIRE://
$Nginx_Port= "85";	// Nginx Port
$site_name        = "A-B-C VPN";
$site_description = "Premium Fast And Reliable Servers";
$site_template    = "darkly"; // (flatly, darkly, sketchy, lumen, materia)
//$site_ovpnmonitoring = "http://206.189.148.68:89"; // Openvpn Monitoring
//$site_v2ray = "http://206.189.148.68:54321"; // v2ray ip and PORT
$site_config = "http://157.245.150.6:86"; // list of config
//$config_zips = "http://206.189.148.68:85/config.zip"; // ovpn zip check it on /home/vps/public_html

//$pc = "https://www.mediafire.com/download/2eq9clhxxao96nz/";

//$android = "https://www.mediafire.com/download/xb1fnxqnl17tof6";

//$ios = "https://apps.apple.com/us/app/openvpn-connect/id590379981";

$dns = "https://www.pointdns.net/create-dns";

$free_vpn1 = "https://play.google.com/store/apps/details?id=com.art.vpn";

$messenger_gc = "https://m.me/j/Aba32vMoh8pP8yZ6";

$messenger = "https://m.me/KingArthur.2233";

$daily_limit_user = "200"; // set daily limit


/////////////////////////////
/////////////////////////////

//Double Check if correct. Update anything not correct.
//$port_ssh= '22'; 		   	 // SSH Ports
//$port_dropbear= '550';  	 // Dropbear Ports
//$port_ssl_dp= '445';                    	 // SSL through Dropbear
//$port_ssl_ssh= '443';                    	 // SSL through OpenSSH
//$port_websocket= '80';                    	 // SSL through Openvpn
//$port_squid= '8000';  // Squid Ports
//$port_privoxy= '9880 | 9880'; 	 // Privoxy Ports
//$port_ohp_sq= '5595 | 5596';  		 // OHP through Squid
//$port_ohp_pr= '5597 | 5598';  		 // OHP through Privoxy
//$port_ohp_ov= '5599';  				 // OHP through Openvpn
//$port_slowdns= '2222';  		 // Python Simple Socks Proxy
//$port_psp_dir= '8044 | 22444';  		 // Python Direct Socks Proxy
//$port_psp_ov= '8055 | 22555';
//$config_fog = "config.zip";				 // Zip File Name for your OVPN Config /Default: config.zip


/////////////////////////////  
/////////////////////////////
//  SHARPEN YOUR EYES!! READ PATTERN BELOW AND EDIT LINE 45,46,47,ETC
/* Server Data */
/* Variable = Server_Name, IP_Address, Root_Pass, Account_Validity */
/* Sample: 1=>array(1=>"Name of your Server 1","your-ip-address","server-or-root-password","5"), */

$server_lists_array=array(
			1=>array(1=>"Art SGDO Server1","sgdo1.active-vpn.ml","free4","10"),
			2=>array(1=>"Art SGDO Server2","sgdo2.active-vpn.ml","@V6access","10"),
			3=>array(1=>"Art SGDO Server3","sgdo3.active-vpn.ml","@V6access","10"),
	);			


//$git_korn = "https://github.com/korn-sudo/Project-Fog";



for ($row = 1;$row < 101;$row++) {
    if ($_POST['server'] == $server_lists_array[$row][1]) {
        $hosts = $server_lists_array[$row][2];
        $root_pass = $server_lists_array[$row][3];
        $expiration = $server_lists_array[$row][4];
        break;
    }
}
$error = false;
if (isset($_POST['user'])) {
    $username = trim($_POST['user']);
    $username = strip_tags($username);
    $username = htmlspecialchars($username);
    $password1 = trim($_POST['password']);
    $password1 = strip_tags($password1);
    $password1 = htmlspecialchars($password1);
    $nDays = $expiration;
    $datess = date('m/d/y', strtotime('+' . $nDays . ' days'));
    $password = escapeshellarg(crypt($password1));
    if (empty($username)) {
        $error = true;
        $nameError = "Please Enter A Username";
    } else if (strlen($username) < 3) {
        $error = true;
        $nameError = "Name Must Have Atleast 3 Characters.";
    }
    if (empty($password1)) {
        $error = true;
        $passError = "Please Enter A Password.";
    } else if (strlen($password1) < 3) {
        $error = true;
        $passError = "Password Must Have Atleast 3 Characters.";
    }
    if ($username == $password1) {
        $error = true;
        $ConfirmError = "Username and Password Should Not Be The Same ";
    }
    if (!$error) {
        date_default_timezone_set('UTC');
        date_default_timezone_set("Asia/Manila");
        $my_date = date("Y-m-d H:i:s");
        $connection = ssh2_connect($hosts, 22);
        if (ssh2_auth_password($connection, 'root', $root_pass)) {
            $check_user = ssh2_exec($connection, "id -u $username");
            $check_user_error = ssh2_fetch_stream($check_user, SSH2_STREAM_STDERR);
            stream_set_blocking($check_user_error, true);
            stream_set_blocking($check_user, true);
            $stream_check_user_error = stream_get_contents($check_user_error);
            $stream_check_user = stream_get_contents($check_user);
            if (!empty($stream_check_user)) {
                $ServerError = "Username Already Taken, Try Again!!!";
            } elseif (!empty($stream_check_user_error)) {
                $check_daily_limit = ssh2_exec($connection, "wc -l < /home/vps/public_html/daily_user_limit.txt");
                $check_daily_limit_error = ssh2_fetch_stream($check_user, SSH2_STREAM_STDERR);
                stream_set_blocking($check_daily_limit_error, true);
                stream_set_blocking($check_daily_limit, true);
                $stream_check_daily_limit_error = stream_get_contents($check_daily_limit_error);
                $stream_check_daily_limit = stream_get_contents($check_daily_limit);
                if ($stream_check_daily_limit >= $daily_limit_user) {
                    $ServerError = "Server Full, Try Again Tomorrow";
                } else {
                    $show = true;
                    ssh2_exec($connection, "useradd $username -m -p $password -e $datess -d  /tmp/$username -s /bin/false");
                    ssh2_exec($connection, 'echo "====================" >> /home/vps/public_html/daily_user_limits.txt');
                }
            }
        } else {
            die('Connection Failed...');
        }
    }
}
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  
<title><?php echo $site_name; ?> | <?php echo $site_description; ?></title>
<meta name="description" content="<?php echo $site_description; ?>"/>
<meta property="og:type" content="website" />
<meta property="og:image" content="https://raw.githubusercontent.com/V6ACCESS/Image/main/received_5595380007195363.webp" alt="" height="300" width"500"/>
   <script language='JavaScript'>
        var txt = '   ' + document.title + '   '
        var speed = 400;
        var refresh = null;
        function site_name() 
			{
            		document.title = txt;
            		txt = txt.substring(1, txt.length) + txt.charAt(0);
            		refresh = setTimeout("site_name()", speed);
        	}
        site_name();     
    </script>
<link rel="shortcut icon" type="image/x-icon" href="https://raw.githubusercontent.com/V6ACCESS/Image/main/received_5595380007195363.webp">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.1.1/<?php echo $site_template; ?>/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" ></script>
</head>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
<a class="navbar-brand" href="/">
  <img src="https://raw.githubusercontent.com/V6ACCESS/Image/main/received_5595380007195363.webp" width="30" height="30" class="d-inline-block align-top" alt="">
  <?php echo $site_name; ?>
</a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarTogglerDemo02">
<ul class="navbar-nav mr-auto mt-2 mt-lg-0">


<li class="nav-item active">
<a class="nav-link" href="<?php echo $dns; ?>"target="_blank">   Create Dns <span class="sr-only">(current)</span></a>
</li>


<li class="nav-item active">
<a class="nav-link" href="<?php echo $free_vpn1; ?>"target="_blank">   A-B-C VPN Apk <span class="sr-only">(current)</span></a>
</li>


<li class="nav-item active">
<a class="nav-link" href="<?php echo $messenger_gc; ?>"target="_blank">   Join Our Group Chat <span class="sr-only">(current)</span></a>
</li>


<li class="nav-item active">
<a class="nav-link" href="<?php echo $messenger; ?>"target="_blank">   Contact Us <span class="sr-only">(current)</span></a>
</li>


</nav>		
</header>
	<div align="center">
      <img src="https://raw.githubusercontent.com/V6ACCESS/Image/main/received_5595380007195363.webp" alt="" height="200" width"200"/>
    	<div class="col-md-4" align="center">
			<form method="post" align="center" class="softether-create">
						<div class="form-group">												
							<?php
if ($show == true) {
    echo '<div class="card alert-success">';
    echo '<table class="table-success">';
    echo '<tr>';
    echo '<td> </td>';
    echo '<td> </td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td align=center>Host | IP:</td>';
    echo '<td>';
    echo $hosts;
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td align=center>Username:</td>';
    echo '<td>';
    echo $username;
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td align=center>Password:</td>';
    echo '<td>';
    echo $password1;
    echo '</td>';
    echo '</tr>';

    //echo '<tr>';
    //echo '<td align=center>SSH Port:</td>';
    //echo '<td>';
    //echo $port_ssh;
    //echo '</td>';
    //echo '</tr>';

    //echo '<tr>';
    //echo '<td align=center>Squid Port:</td>';
    //echo '<td>';
    //echo $port_squid;
    //echo '</td>';
    //echo '</tr>';

  //  echo '<tr>';
   // echo '<td align=center>Privoxy Port:</td>';
//    echo '<td>';
   // echo $port_privoxy;
   // echo '</td>';
 //   echo '</tr>';

    //echo '<tr>';
    //echo '<td align=center>Dropbear Port:</td>';
    //echo '<td>';
    //echo $port_dropbear;
    //echo '</td>';
    //echo '</tr>';

   // echo '<tr>';
   // echo '<td align=center>OHP through Squid:</td>';
    //echo '<td>';
    //echo $port_ohp_sq;
    //echo '</td>';
   // echo '</tr>';

   // echo '<tr>';
    //echo '<td align=center>OHP through Privoxy:</td>';
    //echo '<td>';
   // echo $port_ohp_pr;
   // echo '</td>';
    //echo '</tr>';

    //echo '<tr>';
   // echo '<td align=center>OHP through Openvpn:</td>';
    //echo '<td>';
    //echo $port_ohp_ov;
    //echo '</td>';
    //echo '</tr>';

    //echo '<tr>';
    //echo '<td align=center>SSL Port:</td>';
    //echo '<td>';
    //echo $port_ssl_dp;
    //echo '</td>';
    //echo '</tr>';

    //echo '<tr>';
    //echo '<td align=center>SSL Port:</td>';
    //echo '<td>';
    //echo $port_ssl_ssh;
    //echo '</td>';
    //echo '</tr>';

    //echo '<tr>';
    //echo '<td align=center>Websocket SSH Port:</td>';
    //echo '<td>';
    //echo $port_websocket;
    //echo '</td>';
    //echo '</tr>';

    //echo '<tr>';
    //echo '<td align=center>SlowDNS Port:</td>';
    //echo '<td>';
    //echo $port_slowdns;
    //echo '</td>';
    //echo '</tr>';

    //echo '<tr>';
   // echo '<td align=center>Python Direct Socks Proxy:</td>';
  //  echo '<td>';
    //echo $port_psp_dir;
   // echo '</td>';
    //echo '</tr>';

    //echo '<tr>';
    //echo '<td align=center>Python Openvpn Socks Proxy:</td>';
    //echo '<td>';
    //echo $port_psp_ov;
    //echo '</td>';
    //echo '</tr>';

    echo '<tr>';
    echo '<td align=center>Expiration Date:</td>';
    echo '<td>';
    echo $datess;
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td> </td>';
    echo '<td> </td>';
    echo '</tr>';
    echo '</table>';
    echo '</div>';
}
?>
						</div>
						<div class="form-group">
							<div class="alert-danger">
								<span class="text-light"><?php echo $ServerError; ?></span>
							</div>					
							<div class="alert-danger">
								<span class="text-light"><?php echo $nameError; ?></span>
							</div>
							<div class="alert-danger">
								<span class="text-light"><?php echo $passError; ?></span>
							</div>
							<div class="alert-danger">
								<span class="text-light"><?php echo $ConfirmError; ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">									
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fas fa-globe" style="color:green;"></i></span>
								</div>
								<select class="custom-select" name="server" required>
									<option disabled selected value>Choose Server</option> 
											<?php
for ($row = 1;$row < 101;$row++) {
    if (!empty($server_lists_array[$row][1])) {
        echo '<option>';
        echo $server_lists_array[$row][1];
        echo '</option>';
    } else {
        break;
    }
}
?>
										</optgroup>														
								</select> 
							</div>
						</div>															
						<div class="form-group">								
							<div class="input-group">									
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fas fa-user-circle" style="color:green;"></i></span>
								</div>
									<input type="text" class="form-control" id="username" placeholder="Username" name="user" autocomplete="off" >
							</div>
						</div>
						<div class="form-group">								
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fas fa-key" style="color:green;"></i></span>
								</div>
									<input type="text" class="form-control" id="password" placeholder="Password" name="password" autocomplete="off"  >
							</div>						
						</div>						
										
						<div class="form-group ">
							<button type="submit" id="button" class="btn btn-success btn-block btn-action">CREATE ACCOUNT</button>
						</div>
					</form>					
				</div>
			</div>
		</div>
	</div>
</body>
</html>
