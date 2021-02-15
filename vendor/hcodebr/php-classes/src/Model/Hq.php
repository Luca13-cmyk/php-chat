<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Hq extends Model 
{


	public static function listAll()
	{
		$sql = new Sql();

		return  $sql->select("SELECT * FROM tb_hqs ORDER BY deshq");
	}
	
	public static function checkList($list)
	{
		foreach ($list as &$row) {
			$p = new Hq();
			$p->setData($row);
			$row = $p->getValues();

		}

		return $list;

	}
    public function save()
    {
        $sql = new Sql();
		
		$results = $sql->select("CALL sp_hqs_save(:idhq, :deshq, :deslink, :descap)", array(
			":idhq"=>$this->getidhq(),
            ":deshq"=>$this->getdeshq(),
            ":deslink"=>$this->getdeslink(),
			":descap"=>$this->getdescap()
		));

		$this->setData($results[0]);

	}
	
	public function get($idhq)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_hqs WHERE idhq = :idhq", [
			":idhq"=>$idhq
		]);
		$this->setData($results[0]);


	}

	public function delete()
	{
		$sql = new Sql();

		$results = $sql->query("DELETE FROM tb_hqs WHERE idhq = :idhq", [
			":idhq"=>$this->getidhq()
		]);

	}


	public function getValues()
	{

		$values = parent::getValues();


		return $values;
	}

	public function getFromURL($desurl)
	{
		$sql = new Sql();
		$rows = $sql->select("SELECT * FROM tb_hqs WHERE desurl = :desurl LIMIT 1", [
			":desurl"=>$desurl
		]);

		$this->setData($rows[0]);
	}

	public function getCategories()
	{
		$sql = new Sql();
		return  $sql->select("

		SELECT * FROM tb_categories a INNER JOIN  tb_hqscategories b ON a.idcategory = b.idcategory WHERE b.idhq = :idhq
		
		", [
			":idhq"=>$this->getidhq()
		]);
	}
	public static function getPage($page = 1, $itemsPerPage = 10)
	{
		
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();
		$results = $sql->select("
		
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_hqs 
			ORDER BY deshq
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
			FROM tb_hqs 
			WHERE deshq LIKE :search  
			ORDER BY deshq 
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
	
}


?>