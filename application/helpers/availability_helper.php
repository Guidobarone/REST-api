<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('Availability'))
{
    function Availability($inquiry, $quantity)
    {
    	$disponibilita = $inquiry["availability"];
        $dispimmediata = $inquiry["immavailability"];
        
        $esistenza = $inquiry["existence"];
        $data = $inquiry["date"];
        $alternativo = $inquiry["substitute"];
        $soldout = $inquiry["soldout"];
        $inquiry["quantity"] = $quantity;

        if (($dispimmediata - $quantity) > 8 ) {
          //CASO 1: Se la Disponibilità imm. è > mt 8.00 allora O.k
        log_message('error', 'CASO 1: Se la Disponibilità imm. è > mt 8.00 allora O.k');
          //return 'Available now';
        	return Response($inquiry,1,'Available now');
        }
        else  if (($dispimmediata - $quantity) >= 0) {
          // CASO 2: Se la Disponibilità imm. è < mt 8.00 e > 0
        	log_message('error', 'CASO 2: Se la Disponibilità imm. è < mt 8.00 e > 0');
          //return 'Please contact Ariston';
          return Response($inquiry,2,'Please contact Ariston');
        }
        else if (($disponibilita - $quantity) > 8) {
            // CASO 4: Se la Disponibilità imm. è < mt 0.00 ma la Disponibilità >8.00  allora incrocia con il file degli ordini e scrivi la data di consegna.
          if (($esistenza - $quantity) > 8 && $soldout == 0) {
            //CASO 4.1: se (ESIST. - QTY) > 8 --> Articolo disponibile e ordinabile
            log_message('error', 'CASO 4.1: se (ESIST. - QTY) > 8 --> Articolo disponibile e ordinabile');
            return Response($inquiry,1,'Available now');
            //return 'Available now';
          } else  if (($esistenza - $quantity) > 0 && $soldout == 0) {
            //CASO 4.2: se 0< (ESIST. - QTY) < 8 --> Articolo disponibile ma vicino alla fine scorta, il cliente deve chiamare Ariston
            log_message('error', 'CASO 4.2: se 0< (ESIST. - QTY) < 8 --> Articolo disponibile ma vicino alla fine scorta, il cliente deve chiamare Ariston');
            return Response($inquiry,2,'Please contact Ariston');
            //return 'Please contact Ariston';
          } else {
            //CASO 4.3: se (ESIST. - QTY) <0 → Articolo ordinabile alla data DD/MM/YYYY     
           // Gestione della presenza di un alternativo in caso di ordini in arrivo explode(".",$array[0])
           log_message('error', 'CASO 4.3: se (ESIST - QTY)<0Articolo ordinabile alla data DD/MM/YYYY');
            if ($alternativo != null && $alternativo != ""){
                //return $data;
                return Response($inquiry,3,'It will be available as of '.$data.'. Check '.explode('.',$alternativo)[0].'-'.explode('.',$alternativo)[1].' as replacement');
                //return 'It will be available as of '.$data.'. Check '.explode('.',$alternativo)[0].'-'.explode('.',$alternativo)[1].' as replacement';
            } else {
                //return 'The article will be available as of '. $data;
                return Response($inquiry,3,'The article will be available as of '. $data);
            }            
          } 
        } else {
          //CASO 5: Se la Disponibilità imm. è < mt 0.00 e (DISPONIBILITA - QTY) < 0
        	log_message('error', 'CASO 5: Se la Disponibilità imm. è < mt 0.00 e (DISPONIBILITA - QTY) < 0');
            
            if ($alternativo != null && $alternativo != ""){
                return Response($inquiry,3,'The article is out of stock. Check the substitute '.explode('.',$alternativo)[0].'-'.explode('.',$alternativo)[1]);
                //return 'The article is out of stock. Check the substitute '.explode('.',$alternativo)[0].'-'.explode('.',$alternativo)[1] ;
            } else {
                return Response($inquiry,3,'The article is out of stock');
                //return 'The article is out of stock';
            }
        } 
        //return $article;
    }   
}
if ( ! function_exists('Response'))
{
	function Response($inquiry, $case, $message)
    {
    	$response = [];
    	$response['code'] = $inquiry["code"];
    	$response['shade'] = $inquiry["shade"];
        $response['quantity'] = $inquiry["quantity"];
    	$response['status'] = $case;
    	$response['date'] = '';
    	$response['substitute'] = '';
    	$response['message'] = $message;

    	switch ($case) {
    		case 1:
    			//$response['status'] = 1;
    			break;
    		case 2:
    			//$response['status'] = 2;
    			break;
    		case 3:
    			//$response['status'] = 3;
    			$response['date'] = $inquiry["date"];
    			$response['substitute'] = $inquiry["substitute"];
    			break;
    		default:
    			# code...
    			break;
    	}
    	return $response;
    }
}