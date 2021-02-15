<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Recommended extends Model 
{


	public static function listAll()
	{
		$sql = new Sql();

		return  $sql->select("SELECT * FROM tb_recommendeds ORDER BY desrecommended");
    }
    
    public function save()
    {
        $sql = new Sql();
		
		$results = $sql->select("
		CALL sp_recommendeds_save(:idrecommended, :desrecommended, :deslink, :descap)", 
		array(
			":idrecommended"=>$this->getidrecommended(),
			":desrecommended"=>$this->getdesrecommended(),
			":deslink"=>$this->getdeslink(),
			":descap"=>$this->getdescap()
		));

		$this->setData($results[0]);

	}
	
	public function get($idrecommended)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_recommendeds WHERE idrecommended = :idrecommended", [
			":idrecommended"=>$idrecommended
		]);
		$this->setData($results[0]);
	}

	public function delete()
	{
		$sql = new Sql();

		$sql->query("DELETE FROM tb_recommendeds WHERE idrecommended = :idrecommended", [
			":idrecommended"=>$this->getidrecommended()
		]);
	}
	

	public function  getProducts($related = true)
	{
		$sql = new Sql();

		if ($related)
		{
			return $sql->select("
			SELECT * FROM tb_products WHERE idproduct IN(
				SELECT a.idproduct
				FROM tb_products a
				INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
				WHERE b.idrecommended = :idrecommended
			);
			", [
				":idrecommended"=>$this->getidrecommended()
			]);
		} 
		else
		{
			return $sql->select("
			
			SELECT * FROM tb_products WHERE idproduct NOT IN(
				SELECT a.idproduct
				FROM tb_products a
				INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
				WHERE b.idrecommended = :idrecommended
			);
			", [
				":idrecommended"=>$this->getidrecommended()
			]);
		}
	}

	public function getProductsPage($page = 1, $itemsPerPage = 3)
	{
		
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();
		$results = $sql->select("
		
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products a
			INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
			INNER JOIN tb_recommendeds c ON c.idrecommended = b.idrecommended
			WHERE c.idrecommended = :idrecommended
			LIMIT $start, $itemsPerPage;
		
		", [
			":idrecommended"=>$this->getidrecommended()
		]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>Product::checkList($results),
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];

	}

	public function addProduct(Product $product)
	{
		$sql = new Sql();
		$sql->query("INSERT INTO tb_productscategories (idrecommended, idproduct) VALUES(:idrecommended, :idproduct)", [	
			":idrecommended"=>$this->getidrecommended(),
			":idproduct"=>$product->getidproduct()
		]);

	}
	public function removeProduct(Product $product)
	{
		$sql = new Sql();
		$sql->query("DELETE FROM  tb_productscategories WHERE idrecommended =  :idrecommended AND idproduct = :idproduct", [	
			":idrecommended"=>$this->getidrecommended(),
			":idproduct"=>$product->getidproduct()
		]);
	}

	public static function getPage($page = 1, $itemsPerPage = 10)
	{
		
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();
		$results = $sql->select("
		
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_recommendeds  
			ORDER BY idrecommended DESC
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
			FROM tb_recommendeds 
			WHERE desrecommended LIKE :search  
			ORDER BY desrecommended
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
	public static function getTopicSearchAZ($search, $page = 1, $itemsPerPage = 10) // LIKE = como ou mais ou menos igual
																				 //  = exatamente igual ao especificado 
	{
		
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();
		$results = $sql->select("
		
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_recommendeds 
			WHERE desrecommended LIKE :search  
			ORDER BY desrecommended
			LIMIT $start, $itemsPerPage;
		", [
			":search"=>$search."%"
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