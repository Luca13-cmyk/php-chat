<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Topiclikes extends Model 
{

	

	public function get(int $topic)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_topiclikes WHERE idtopic = :idtopic", [
			':idtopic'=>$topic
		]);

		if (count($results) > 0) {

			$this->setData($results[0]);

		}

	}
	
	
	
}


?>