<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\Model\User;

class Userlikes extends Model 
{

	const SESSION = "Userlikes";
    const SESSION_ERROR = "UserlikesError";

	public static function getFromSession()
	{


		$userlikes = new Userlikes();

        if (isset($_SESSION[Userlikes::SESSION])) 
        {

			return $_SESSION[Userlikes::SESSION];

        } 

		

	}

	public function setToSession($results)
	{

		$_SESSION[Userlikes::SESSION] = $results;

	}	

	public function get(int $iduser)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_userlikes WHERE iduser = :iduser", [
			':iduser'=>$iduser
		]);

		if (count($results) > 0) {

			$this->setData($results[0]);

		}

	}
	public function setDataSessionLogin(int $iduser)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_userlikes WHERE iduser = :iduser", [
			':iduser'=>$iduser
		]);
		if (count($results) > 0)
		{

			$this->setToSession($results);
		} else 
		{
			$this->setToSession([]);
		}

	}
	public static function getDataFromSessionLogin(int $iduser, int $idtopic)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_userlikes WHERE iduser = :iduser AND idtopic = :idtopic", [
			':iduser'=>$iduser,
			':idtopic'=>$idtopic
		]);

		if ($results != '')
		{
			return false;
			
		} else 
		{
			return true;
		}

	}

	public function addLike($idtopic, Topiclikes $topiclikes)
    {
		$sql = new Sql();
		$user = User::getFromSession();

        $results = $sql->select("CALL sp_likes_save(:idtopiclikes, :iduser, :idtopic, :desnumlikes)", [
			":idtopiclikes"=>$topiclikes->getidtopiclikes(),
			":iduser"=>$user->getiduser(),
			":idtopic"=>$idtopic,
			":desnumlikes"=>(int)$topiclikes->getdesnumlikes()+1
		]);
		
		$this->setToSession($results);

	}
	
	
}


?>