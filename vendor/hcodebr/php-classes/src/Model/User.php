<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\Model\Userlikes;

class User extends Model {

	const SESSION = "User"; // constant de classe
	const SECRET = "HcodePhp7_Secret";
	const SECRET_IV = "HcodePhp7_Secret_IV";
	const ERROR = "UserError";
	const ERROR_REGISTER = "UserErrorRegister";
	const SUCCESS = "UserSucesss";
	const DOMAIN = "#";

	public static function getFromSession()
	{
		$user = new User();
		if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0)
		{
			

			$user->setData($_SESSION[User::SESSION]);


		}

		return $user;
	}

	public static function checkLogin($inadmin = true)
	{
		
		if (
			
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		){
			// Nao esta logado
			return false;
		}
		else 
		{
			if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true)
			{
				return true;
			} else if ($inadmin === false)
			{
				return true;
			} else 
			{
				return false;
			}
		}
	}

	public static function reCAPTCHA()
	{
		   
		$token = $_POST['token'];
		$action = $_POST['action'];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
			"secret"=>"6LeGZtQUAAAAAOB4JhIkMM7mmJTHmRvEPqGppuwE",
			"response"=>$token,
			"remoteip"=>$_SERVER["REMOTE_ADDR"]
		)));



		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$recaptcha = json_decode(curl_exec($ch), true);

		curl_close($ch);

		$final = [];


		if ($recaptcha["success"] && $recaptcha["action"] == $action && $recaptcha["score"] >= 0.6)
		{
			
			return true;
			
		} else 
		{
			return false;
		}
 
	}

	public static function login($login, $password)
	{
		if (!User::reCAPTCHA()) throw new \Exception("Erro no captcha");
		
       

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE a.deslogin = :LOGIN", array(
			":LOGIN"=>$login
		)); 

		if (count($results) === 0)
		{
			throw new \Exception("Dados incorretos.");
        }

		
		$data = $results[0];
		// print_r($data);
		// exit;

		// if ($login === $data['deslogin'])

		if (password_verify($password, $data["despassword"]) === true)
		{
			
			if (isset($_COOKIE['LOGIN']))
			{
				unset($_COOKIE['LOGIN']);
				setcookie("LOGIN", "yes", time() + 3600);
			} else 
			{

				setcookie("LOGIN", "yes", time() + 3600);
			}


			$user = new User();
			
			$data['desperson'] = utf8_encode($data['desperson']);

			$user->setData($data);

			// $userlikes = new Userlikes();

			// $userlikes->setDataSessionLogin((int)$user->getiduser());

			// print_r($user->getValues());
			
			$_SESSION[User::SESSION] = $user->getValues();


            return $user;
            
		} else {
			throw new \Exception("Dados incorretos.");

        }
        
	}
	
	
	public static function verifyLogin($inadmin = true, $sessiontimeout = true)
	{
		if ($sessiontimeout)
		{

			if (!isset($_COOKIE['LOGIN'])) 
			{
				User::logout();
				if ($inadmin)
				{
					header("Location: /login");
				}
				header("Location: /login");
	
			}
		}
		
		if (!User::checkLogin($inadmin)) {
			if ($inadmin)
			{
				header("Location: /login");
			}
			else 
			{
				header("Location: /login");
			}	
			exit;
		}
	}

	public static function logout()
	{
		$_SESSION[User::SESSION] = NULL;
	}

	public static function listAll()
	{
		$sql = new Sql();

		return  $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
	}

	public function save()
	{
		$sql = new Sql();
		
		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}
	public function  get($iduser)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
			":iduser"=>$iduser
		));

		
		
		$data = $results[0];
		$data['desperson'] = utf8_encode($data['desperson']);

		$this->setData($results[0]);


	}

	public function update()
	{
		$sql = new Sql();

		
		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);
	}

	public function delete()
	{
		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", array(
			":iduser"=>$this->getiduser()
		));
	}

	public static function registerValid()
	{

		$_SESSION['registerValues'] = $_POST;

		if (!isset($_POST['name']) || $_POST['name'] == '') 
		{

			User::setErrorRegister("Preencha o seu nome.");
			header("Location: /register");
			exit;

		}

		if (!isset($_POST['email']) || $_POST['email'] == '') 
		{

			User::setErrorRegister("Preencha o seu e-mail.");
			header("Location: /register");
			exit;

		}

		if (!isset($_POST['password']) || $_POST['password'] == '') 
		{

			User::setErrorRegister("Preencha a senha.");
			header("Location: /register");
			exit;

		}

		if (User::checkLoginExist($_POST['email']) === true) 
		{

			User::setErrorRegister("Este endereço de e-mail já está sendo usado por outro usuário.");
			header("Location: /register");
			exit;

		}

		$data = [
			'inadmin'=>0,
			'deslogin'=>$_POST['email'],
			'desperson'=>$_POST['name'],
			'desemail'=>$_POST['email'],
			'despassword'=>$_POST['password'],
			'nrphone'=>$_POST['phone']
		];
	
		$data = json_encode($data);
	
		$data = openssl_encrypt($data, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));
	
		$data = base64_encode($data);
		
		$mailer = new Mailer($_POST['email'], $_POST['name'],  "Confirmar registro", "Confirm", array(
			"name"=>$_POST['name'],
			"link"=>User::DOMAIN."/register/confirm?data=$data"
		));
		$mailer->send();
	
		$user = new User();
	
		$user->setSuccess("Email enviado, por favor, confirme o cadastro.");
	}

	public static function registerValidConfirm()
	{
		

		function saveData()
		{
			if ($_GET["data"] && $_GET["data"] != '')
			{
				try {
					
					$data = $_GET["data"];
			
					$data = base64_decode($data);
				
					$datarecovery = openssl_decrypt($data, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));
			
					$datarecovery  = json_decode($datarecovery, true);
					
			
					$user = new User();
				
					$user->setData($datarecovery);
				
					$user->save();
				
					header('Location: /login');
					exit;

				} catch (\Exception $th) {
					
					User::setErrorRegister("Nao foi possivel fazer o cadastro. Tente novamente");
					header("Location: /register");
					exit;
				}

			} else 
			{
				User::setErrorRegister("Nao foi possivel fazer o cadastro. Tente novamente");
				header("Location: /register");
				exit;
			}
		}

		try {
			
			saveData();

		} catch (\Exception $th) {
			User::setErrorRegister("Nao foi possivel fazer o cadastro. Tente novamente");
			header("Location: /register");
			exit;
		}
	}

	public function checkPhotoAvatar()
	{
		if (file_exists($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "profile_user" . DIRECTORY_SEPARATOR . "avatar" . DIRECTORY_SEPARATOR . 
				 $this->getiduser() . ".jpg"
		)) {
			$url = "/profile_user/avatar/" . $this->getiduser() . ".jpg";
		} else {
			$url = "/profile_user/avatar/default-avatar.png";
		}

		return  $this->setdesphotoavatar($url);
	}
	public function checkPhotoCap()
	{
		if (file_exists($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "profile_user" . DIRECTORY_SEPARATOR . "cap" . DIRECTORY_SEPARATOR . 
			 $this->getiduser() . ".jpg"
		)) {
			$url = "/profile_user/cap/" . $this->getiduser() . ".jpg";
		} else {
			$url = "/profile_user/cap/default-cap.jpg";
		}

		return  $this->setdesphotocap($url);
	}

	public function getValues()
	{
		$this->checkPhotoAvatar();
		$this->checkPhotoCap();
		$values = parent::getValues();


		return $values;
	}

	
	public function setPhotoAvatar($file)
	{ 
		
		$extension = explode('.', $file['name']);
		$extension = end($extension);
		switch ($extension) {
		case "jpg":
		case "jpeg":
		$image = imagecreatefromjpeg($file["tmp_name"]);
		break;
		case "gif":
		$image = imagecreatefromgif($file["tmp_name"]);
		break;
		case "png":
		$image = imagecreatefrompng($file["tmp_name"]);
		break;
		}
		$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
		"profile_user" . DIRECTORY_SEPARATOR . 
		"avatar" . DIRECTORY_SEPARATOR . 
		$this->getiduser() . ".jpg";
		imagejpeg($image, $dist);
		imagedestroy($image);
		$this->checkPhotoAvatar();
		
	}
	public function setPhotoCap($file)
	{ 
		
		$extension = explode('.', $file['name']);
		$extension = end($extension);
		switch ($extension) {
		case "jpg":
		case "jpeg":
		$image = imagecreatefromjpeg($file["tmp_name"]);
		break;
		case "gif":
		$image = imagecreatefromgif($file["tmp_name"]);
		break;
		case "png":
		$image = imagecreatefrompng($file["tmp_name"]);
		break;
		}
		$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
		"profile_user" . DIRECTORY_SEPARATOR . 
		"cap" . DIRECTORY_SEPARATOR . 
		$this->getiduser() . ".jpg";
		imagejpeg($image, $dist);
		imagedestroy($image);
		$this->checkPhotoCap();
		
	}


	public static function getForgot($email, $inadmin = true)
	{
		$sql = new Sql();

		$results = $sql->select("
		
		SELECT *
		FROM tb_persons a
		INNER JOIN tb_users b USING(idperson)
		WHERE a.desemail = :email;
		
		", array(
			":email"=>$email
		));

		if (count($results) === 0)
		{
			throw new \Exception("Nao foi possivel recuperar a senha.");
			
		}
		else 
		{
			$data = $results[0];
			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data["iduser"],
				":desip"=>$_SERVER["REMOTE_ADDR"]
			));

			if (count($results2) === 0) // caso nao consiga criar a tabela recoveries e retornar so valores
			{
				throw new \Exception("Nao foi possivel recuperar a senha");
				
			}
			else 
			{
				$dataRecovery = $results2[0];

				$code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

				$code = base64_encode($code);

				if ($inadmin === true)
				{
					$link = User::DOMAIN."/admin/forgot/reset?code=$code";

				} else 
				{
					$link = User::DOMAIN."/forgot/reset?code=$code";

				}

				$mailer = new Mailer($data["desemail"], $data["desperson"],  "Redefinir senha do ds-club", "forgot", array(
					"name"=>$data["desperson"],
					"link"=>$link
				));
				$mailer->send();

				return $data;
			}

		}

	}
	public static function validForgotDecrypt($code)
	{
		$code = base64_decode($code);

		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

		$sql = new Sql();

		$results = $sql->select("
		
		SELECT *
		FROM tb_userspasswordsrecoveries a
		INNER JOIN tb_users b USING(iduser)
		INNER JOIN tb_persons c USING(idperson)
		WHERE
			a.idrecovery = :idrecovery
			AND
			a.dtrecovery IS NULL
			AND
			DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		
		", array(
			":idrecovery"=>$idrecovery
		));

		if (count($results) === 0)
		{
			throw new \Exception("Nao foi possivel recuperar a senha");
			
		}
		else 
		{
			return $results[0];
		}
	}
	public static function setForgotUser($idrecovery)
	{
		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
		));
	}

	public function setPassword($password)
	{
		$sql = new Sql();

		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
			":password"=>$password,
			":iduser"=>$this->getiduser()
		));
	}

	public static function setError($msg)
	{

		$_SESSION[User::ERROR] = $msg;

	}

	public static function getError()
	{

		$msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';

		User::clearError();

		return $msg;

	}

	public static function clearError()
	{

		$_SESSION[User::ERROR] = NULL;

	}

	public static function setSuccess($msg)
	{

		$_SESSION[User::SUCCESS] = $msg;

	}

	public static function getSuccess()
	{

		$msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS]) ? $_SESSION[User::SUCCESS] : '';

		User::clearSuccess();

		return $msg;

	}

	public static function clearSuccess()
	{

		$_SESSION[User::SUCCESS] = NULL;

	}

	public static function setErrorRegister($msg)
	{

		$_SESSION[User::ERROR_REGISTER] = $msg;

	}

	public static function getErrorRegister()
	{

		$msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : '';

		User::clearErrorRegister();

		return $msg;

	}

	public static function clearErrorRegister()
	{

		$_SESSION[User::ERROR_REGISTER] = NULL;

	}

	public static function checkLoginExist($login)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :deslogin", [
			':deslogin'=>$login
		]);

		return (count($results) > 0);

	}
	public static function checkEmailExist($email)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_persons WHERE desemail = :desemail", [
			':desemail'=>$email
		]);

		return (count($results) > 0);

	}

	public static function getPasswordHash($password)
	{

		return password_hash($password, PASSWORD_DEFAULT, [
			'cost'=>12
		]);

	}

	public function getOrders()
	{
		$sql = new Sql();
        $results = $sql->select("SELECT * 
        FROM tb_orders a 
        INNER JOIN tb_ordersstatus b 
        USING(idstatus) 
        INNER JOIN tb_carts c USING(idcart)
        INNER JOIN tb_users d ON d.iduser = a.iduser 
        INNER JOIN tb_addresses  e USING(idaddress)
        INNER JOIN tb_persons f ON f.idperson = d.idperson
        WHERE a.iduser = :iduser
        ", [
            ":iduser"=>$this->getiduser()
		]);
		
		return $results;

	}

	public static function getPage($page = 1, $itemsPerPage = 30)
	{
		
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();
		$results = $sql->select("
		
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_users a 
			INNER JOIN tb_persons b USING(idperson) 
			ORDER BY b.desperson
			LIMIT $start, $itemsPerPage;
		
		");

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];

	}
	public static function getPageSearch($search, $page = 1, $itemsPerPage = 30) // LIKE = como ou mais ou menos igual
																				 //  = exatamente igual ao especificado 
	{
		
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();
		$results = $sql->select("
		
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_users a 
			INNER JOIN tb_persons b USING(idperson)
			WHERE b.desperson LIKE :search OR b.desemail = :search OR a.deslogin LIKE :search 
			ORDER BY b.desperson
			LIMIT $start, $itemsPerPage;
		
		", [
			":search"=>"%".$search."%"
		]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];

	}

	public function validChangePassword($current_password, $new_password, $new_password_confirm)
	{
		if (!isset($current_password) || $current_password === '')
		{
			User::setError("Digite a senha atual");
			header("Location: /profile_user/change-password");
			exit;
		}
		if (!isset($new_password) || $new_password === '')
		{
			User::setError("Digite a nova senha");
			header("Location: /profile_user/change-password");
			exit;
		}
		if (!isset($new_password_confirm) || $new_password_confirm === '')
		{
			User::setError("Confirme a nova senha");
			header("Location: /profile_user/change-password");
			exit;
		}

		if ($current_password === $new_password)
		{
			User::setError("Sua nova senha deve ser diferente da atual.");
			header("Location: /profile_user/change-password");
			exit;
		}
		if ($new_password !== $new_password_confirm)
		{
			User::setError("Confirmacao de senha invalida.");
			header("Location: /profile_user/change-password");
			exit;
		}
		if (!password_verify($current_password, $this->getdespassword()))
		{
			User::setError("Senha digitada atual nao condiz com a atual do usuario");
			header("Location: /profile_user/change-password");
			exit;
		}
	}

}


?>