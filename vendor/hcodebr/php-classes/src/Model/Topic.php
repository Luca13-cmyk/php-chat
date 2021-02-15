<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Topic extends Model 
{


	public static function listAll()
	{
		$sql = new Sql();

		return  $sql->select("SELECT * FROM tb_topics ORDER BY destopic");
    }
    
    public function save()
    {
        $sql = new Sql();
		
		$results = $sql->select("
		CALL sp_topics_save(:idtopic, :destopic, :desheader, :descap, :iduser, :desperson, :descompany, :desserie, :desrelease, :vltotalcompany)", 
		array(
			":idtopic"=>$this->getidtopic(),
			":destopic"=>$this->getdestopic(),
			":desheader"=>$this->getdesheader(),
			":descap"=>$this->getdescap(),
			":iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":descompany"=>$this->getdescompany(),
			":desserie"=>$this->getdesserie(),
			":desrelease"=>$this->getdesrelease(),
			":vltotalcompany"=>$this->getvltotalcompany()
		));

		$this->setData($results[0]);

	}
	
	public function get($idtopic)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_topics WHERE idtopic = :idtopic", [
			":idtopic"=>$idtopic
		]);
		$this->setData($results[0]);


	}

	public function delete()
	{
		$sql = new Sql();

		$sql->query("DELETE FROM tb_topics WHERE idtopic = :idtopic", [
			":idtopic"=>$this->getidtopic()
		]);

	}

	public function  getHqs($related = true)
	{
		$sql = new Sql();

		if ($related)
		{
			return $sql->select("
			SELECT * FROM tb_hqs WHERE idhq IN(
				SELECT a.idhq
				FROM tb_hqs a
				INNER JOIN tb_hqtopics b ON a.idhq = b.idhq
				WHERE b.idtopic = :idtopic
			);
			", [
				":idtopic"=>$this->getidtopic()
			]);
		} 
		else
		{
			return $sql->select("
			
			SELECT * FROM tb_hqs WHERE idhq NOT IN(
				SELECT a.idhq
				FROM tb_hqs a
				INNER JOIN tb_hqtopics b ON a.idhq = b.idhq
				WHERE b.idtopic = :idtopic
			);
			", [
				":idtopic"=>$this->getidtopic()
			]);
		}
	}

	public function gethqsPage($page = 1, $itemsPerPage = 3)
	{
		
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();
		$results = $sql->select("
		
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_hqs a
			INNER JOIN tb_hqstopics b ON a.idhq = b.idhq
			INNER JOIN tb_topics c ON c.idtopic = b.idtopic
			WHERE c.idtopic = :idtopic
			LIMIT $start, $itemsPerPage;
		
		", [
			":idtopic"=>$this->getidtopic()
		]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>hq::checkList($results),
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];

	}

	public function addHq(Hq $hq)
	{
		$sql = new Sql();
		$sql->query("INSERT INTO tb_hqtopics (idtopic, idhq) VALUES(:idtopic, :idhq)", [	
			":idtopic"=>$this->getidtopic(),
			":idhq"=>$hq->getidhq()
		]);
	}
	public function removeHq(hq $hq)
	{
		$sql = new Sql();
		$sql->query("DELETE FROM  tb_hqtopics WHERE idtopic =  :idtopic AND idhq = :idhq", [	
			":idtopic"=>$this->getidtopic(),
			":idhq"=>$hq->getidhq()
		]);
	}

	public static function getPage($page = 1, $itemsPerPage = 10)
	{
		
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();
		$results = $sql->select("
		
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_topics  
			ORDER BY idtopic DESC
			LIMIT $start, $itemsPerPage;
		");

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];

	}
	public static function getPageSearch($search, $page = 1, $itemsPerPage = 10) // LIKE = como ou mais ou menos igual
																				 //  = exatamente igual ao especificado 
	{
		
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();
		$results = $sql->select("
		
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_topics 
			WHERE destopic LIKE :search  
			ORDER BY destopic
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
	public static function getPageSearchAZ($search) 
																				 
	{
		
		$sql = new Sql();
		$results = $sql->select("
		
			SELECT *
			FROM tb_topics 
			WHERE destopic LIKE :search  
			ORDER BY destopic;
		", [
			":search"=>$search."%"
		]);

		return $results;
		

	}

	public static function getidtopic($destopic)
	{
		$sql = new Sql();
		$results = $sql->select("
		
			SELECT *
			FROM tb_topics 
			WHERE destopic = :destopic  
			ORDER BY destopic;
		", [
			":destopic"=>$destopic
		]);

		if (count($results) > 0)
		{

			return $results[0];
		}
	}
	
}


?>