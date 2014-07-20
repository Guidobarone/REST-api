<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Model
 * @author		Riccardo
 * @link		--
*/

class Stock extends CI_Model {

    function __construct()
    {
        parent::__construct();
        define("STOCKFILE", "/ariston/server/stockwithflag.csv");
    }
/* STOCKFILE FORMAT
 * { name: 'chiave', type: 'string' },
 * { name: 'disponibilita', type: 'string' },
 * { name: 'dispimmediata', type: 'string' },
 * { name: 'esistenza', type: 'string' },
 * { name: 'data', type: 'string' },
 * { name: 'alternativo', type: 'string' },
 * { name: 'soldout', type: 'number' } 
*/
    function queryStock($var)
    {
    	$assoc = [];
    	$queryString = $var["code"].".".$var["shade"];
		$command = "awk -F',' '{ if ($1 ~ /".$queryString."/)  { print $0 } }' ".$_SERVER['DOCUMENT_ROOT'].STOCKFILE;
		log_message('error', 'command: '.var_export($command,TRUE));
		$res = shell_exec($command);
		log_message('error', 'command output: '.var_export($res,TRUE));
		
		if ($res){
			$array = str_getcsv($res);
			$codeShade = explode(".",$array[0]);
			$assoc['code'] = $codeShade[0];
			$assoc['shade'] = $codeShade[1];
			$assoc['availability'] = $array[1];
			$assoc['immavailability'] = $array[2];
			$assoc['existence'] = $array[3];
			$assoc['date'] = $array[4];
			$assoc['substitute'] = $array[5];
			$assoc['soldout'] = $array[6];
		}
        return $assoc;
    }  
}