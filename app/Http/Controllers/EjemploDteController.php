<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EjemploDteController extends Controller
{

    function caracteres_reservados_url($txt)
    {
        //Caracteres reservados de URL
        $txt = str_replace("!","%21",$txt);
        $txt = str_replace("#","%23",$txt);
        $txt = str_replace("$","%24",$txt);
        $txt = str_replace("%","%25",$txt);
        $txt = str_replace("&","%26",$txt);
        $txt = str_replace("‘","%27",$txt);
        $txt = str_replace("(","%28",$txt);
        $txt = str_replace(")","%29",$txt);
        $txt = str_replace("*","%2A",$txt);
        $txt = str_replace("+","%2B",$txt);
        $txt = str_replace(",","%2C",$txt);
        $txt = str_replace("/","%2F",$txt);
        $txt = str_replace(":","%3A",$txt);
        $txt = str_replace(";","%3B",$txt);
        $txt = str_replace("=","%3D",$txt);
        $txt = str_replace("?","%3F",$txt);
        $txt = str_replace("@","%40",$txt);
        $txt = str_replace("[","%5B",$txt);
        $txt = str_replace("]","%5D",$txt);

        //Caracteres no admitidos en URL
        $txt = str_replace(" ","%20",$txt);
        $txt = str_replace('"','%22',$txt);
        $txt = str_replace("<","%3C",$txt);
        $txt = str_replace(">","%3E",$txt);
        $txt = str_replace("^","%5E",$txt);
        $txt = str_replace("`","%60",$txt);
        $txt = str_replace("{","%7B",$txt);
        $txt = str_replace("|","%7C",$txt);
        $txt = str_replace("}","%7D",$txt);
        $txt = str_replace("€","%80",$txt);
        $txt = str_replace("‚","%82",$txt);
        $txt = str_replace("ƒ","%83",$txt);
        $txt = str_replace("„","%84",$txt);
        $txt = str_replace("…","%85",$txt);
        $txt = str_replace("†","%86",$txt);
        $txt = str_replace("‡","%87",$txt);
        $txt = str_replace("ˆ","%88",$txt);
        $txt = str_replace("‰","%89",$txt);
        $txt = str_replace("Š","%8A",$txt);
        $txt = str_replace("‹","%8B",$txt);
        $txt = str_replace("Œ","%8C",$txt);
        $txt = str_replace("Ž","%8E",$txt);
        $txt = str_replace("‘","%91",$txt);
        $txt = str_replace("’","%92",$txt);
        $txt = str_replace("“","%93",$txt);
        $txt = str_replace("”","%94",$txt);
        $txt = str_replace("•","%95",$txt);
        $txt = str_replace("–","%96",$txt);
        $txt = str_replace("—","%97",$txt);
        $txt = str_replace("™","%99",$txt);
        $txt = str_replace("š","%9A",$txt);
        $txt = str_replace("›","%9B",$txt);
        $txt = str_replace("œ","%9C",$txt);
        $txt = str_replace("ž","%9E",$txt);
        $txt = str_replace("Ÿ","%9F",$txt);
        $txt = str_replace("¡","%A1",$txt);
        $txt = str_replace("¢","%A2",$txt);
        $txt = str_replace("£","%A3",$txt);
        $txt = str_replace("¥","%A5",$txt);
        $txt = str_replace("|","%A6",$txt);
        $txt = str_replace("§","%A7",$txt);
        $txt = str_replace("¨","%A8",$txt);
        $txt = str_replace("©","%A9",$txt);
        $txt = str_replace("ª","%AA",$txt);
        $txt = str_replace("«","%AB",$txt);
        $txt = str_replace("¬","%AC",$txt);
        $txt = str_replace("¯","%AD",$txt);
        $txt = str_replace("®","%AE",$txt);
        $txt = str_replace("¯","%AF",$txt);
        $txt = str_replace("°","%B0",$txt);
        $txt = str_replace("±","%B1",$txt);
        $txt = str_replace("²","%B2",$txt);
        $txt = str_replace("³","%B3",$txt);
        $txt = str_replace("´","%B4",$txt);
        $txt = str_replace("µ","%B5",$txt);
        $txt = str_replace("¶","%B6",$txt);
        $txt = str_replace("·","%B7",$txt);
        $txt = str_replace("¸","%B8",$txt);
        $txt = str_replace("¹","%B9",$txt);
        $txt = str_replace("º","%BA",$txt);
        $txt = str_replace("»","%BB",$txt);
        $txt = str_replace("¼","%BC",$txt);
        $txt = str_replace("½","%BD",$txt);
        $txt = str_replace("¾","%BE",$txt);
        $txt = str_replace("¿","%BF",$txt);
        $txt = str_replace("À","%C0",$txt);
        $txt = str_replace("Á","%C1",$txt);
        $txt = str_replace("Â","%C2",$txt);
        $txt = str_replace("Ã","%C3",$txt);
        $txt = str_replace("Ä","%C4",$txt);
        $txt = str_replace("Å","%C5",$txt);
        $txt = str_replace("Æ","%C6",$txt);
        $txt = str_replace("Ç","%C7",$txt);
        $txt = str_replace("È","%C8",$txt);
        $txt = str_replace("É","%C9",$txt);
        $txt = str_replace("Ê","%CA",$txt);
        $txt = str_replace("Ë","%CB",$txt);
        $txt = str_replace("Ì","%CC",$txt);
        $txt = str_replace("Í","%CD",$txt);
        $txt = str_replace("Î","%CE",$txt);
        $txt = str_replace("Ï","%CF",$txt);
        $txt = str_replace("Ð","%D0",$txt);
        $txt = str_replace("Ñ","%D1",$txt);
        $txt = str_replace("Ò","%D2",$txt);
        $txt = str_replace("Ó","%D3",$txt);
        $txt = str_replace("Ô","%D4",$txt);
        $txt = str_replace("Õ","%D5",$txt);
        $txt = str_replace("Ö","%D6",$txt);
        $txt = str_replace("Ø","%D8",$txt);
        $txt = str_replace("Ù","%D9",$txt);
        $txt = str_replace("Ú","%DA",$txt);
        $txt = str_replace("Û","%DB",$txt);
        $txt = str_replace("Ü","%DC",$txt);
        $txt = str_replace("Ý","%DD",$txt);
        $txt = str_replace("Þ","%DE",$txt);
        $txt = str_replace("ß","%DF",$txt);
        $txt = str_replace("à","%E0",$txt);
        $txt = str_replace("á","%E1",$txt);
        $txt = str_replace("â","%E2",$txt);
        $txt = str_replace("ã","%E3",$txt);
        $txt = str_replace("ä","%E4",$txt);
        $txt = str_replace("å","%E5",$txt);
        $txt = str_replace("æ","%E6",$txt);
        $txt = str_replace("ç","%E7",$txt);
        $txt = str_replace("è","%E8",$txt);
        $txt = str_replace("é","%E9",$txt);
        $txt = str_replace("ê","%EA",$txt);
        $txt = str_replace("ë","%EB",$txt);
        $txt = str_replace("ì","%EC",$txt);
        $txt = str_replace("í","%ED",$txt);
        $txt = str_replace("î","%EE",$txt);
        $txt = str_replace("ï","%EF",$txt);
        $txt = str_replace("ð","%F0",$txt);
        $txt = str_replace("ñ","%F1",$txt);
        $txt = str_replace("ò","%F2",$txt);
        $txt = str_replace("ó","%F3",$txt);
        $txt = str_replace("ô","%F4",$txt);
        $txt = str_replace("õ","%F5",$txt);
        $txt = str_replace("ö","%F6",$txt);
        $txt = str_replace("÷","%F7",$txt);
        $txt = str_replace("ø","%F8",$txt);
        $txt = str_replace("ù","%F9",$txt);
        $txt = str_replace("ú","%FA",$txt);
        $txt = str_replace("û","%FB",$txt);
        $txt = str_replace("ü","%FC",$txt);
        $txt = str_replace("ý","%FD",$txt);
        $txt = str_replace("þ","%FE",$txt);
        $txt = str_replace("ÿ","%FF",$txt);

        return $txt;
    }

    public function ejemploDte(Request $r){

        //llave unica para cada cliente y sistema que tengamos
        $Llave = $r->llave;
        //Leeo el contenido del archivo CAF
        // $XMLCAF = file_get_contents("CAFprueba.xml");

        //codifico el contenido del archivo CAF
        $XMLCAF = base64_encode($r->xml);
        //Reemplasamos los caracteres recervados que no pasan por URL
        $XMLCAF = $this->caracteres_reservados_url($XMLCAF);


        //Lo mismo de las 3 lineas de arriba pero en una sola
        //$XMLCAF = caracteres_reservados_url(base64_encode(file_get_contents("CAFprueba.xml")));

        //conformo la URL de la API con la llave y el contenido del CAF codificado base64
        $URL = 'http://webapineofox.millarstic.cl/api/Caf?Llave='.$Llave.'&TxtCAF='.$XMLCAF.'';



        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //muestro la respuesta del API
        echo $response;


    }

    public function ingresar_caf(Request $r){

        //llave unica para cada cliente y sistema que tengamos
        $Llave = 'eTUDwTw$eBj5tChr7$9zf$uZkRqq';


        //Leeo el contenido del archivo CAF
        // $XMLCAF = file_get_contents("CAFprueba.xml");

        //codifico el contenido del archivo CAF
        $XMLCAF = base64_encode($r->xml);

        //Reemplasamos los caracteres recervados que no pasan por URL
        $XMLCAF = $this->caracteres_reservados_url($XMLCAF);
        //Lo mismo de las 3 lineas de arriba pero en una sola
        //$XMLCAF = caracteres_reservados_url(base64_encode(file_get_contents("CAFprueba.xml")));

        //conformo la URL de la API con la llave y el contenido del CAF codificado base64
        $URL = 'http://webapineofox.millarstic.cl/api/Caf?Llave='.$Llave.'&TxtCAF='.$XMLCAF.'';


        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //muestro la respuesta del API
        echo $response;


    }

}
