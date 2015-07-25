<?
/**
* 
*/
class PodioKeys
{
	private $bialystok = array('kursanci_id' => '1111111', 'kursanci_token' => 'token', 
							'jezyki_id' => '111111', 'jezyki_token' => 'token',
							'grupy_id' => '111111', 'grupy_token' => 'token',
							'client_secret' => 'secret', 
							'client_id' => 'id');

	private $wroclaw_ue = array('kursanci_id' => '22222', 'kursanci_token' => 'token_token', 
							'jezyki_id' => '22222', 'jezyki_token' => 'token_token',
							'grupy_id' => '22222', 'grupy_token' => 'token_token',
							'client_secret' => 'secret', 
							'client_id' => 'id');
	


	public function getLCKeys($lcname)
	{
		$miasto = null;
		switch ($lcname) {
			
			case "wroclaw-ue":			
				$miasto = $this->wroclaw_ue;
				break;
			case "bialystok":		
				$miasto = $this->bialystok;
				break;
		}
		
		return $miasto;
	}
}

?>