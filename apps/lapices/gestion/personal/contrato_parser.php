<?php
// Extract suggestions only. A contract can never set application permissions.
function personal_contract_date($value){
    $value=trim($value);$months=array('enero'=>1,'febrero'=>2,'marzo'=>3,'abril'=>4,'mayo'=>5,'junio'=>6,'julio'=>7,'agosto'=>8,'septiembre'=>9,'octubre'=>10,'noviembre'=>11,'diciembre'=>12);
    if(preg_match('/\b(\d{4})-(\d{2})-(\d{2})\b/',$value,$m))return checkdate($m[2],$m[3],$m[1])?$m[0]:'';
    if(preg_match('/\b(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})\b/',$value,$m))return checkdate($m[2],$m[1],$m[3])?sprintf('%04d-%02d-%02d',$m[3],$m[2],$m[1]):'';
    if(preg_match('/\b(\d{1,2})\s+de\s+([a-z]+)\s+de\s+(\d{4})\b/iu',$value,$m)){$month=isset($months[mb_strtolower($m[2],'UTF-8')])?$months[mb_strtolower($m[2],'UTF-8')]:0;return checkdate($month,$m[1],$m[3])?sprintf('%04d-%02d-%02d',$m[3],$month,$m[1]):'';}return '';
}
function personal_contract_field($text,$labels){
    $pattern='/(?:^|\n|\t)\s*(?:'.$labels.')\s*[:：]\s*([^\n\t]+)/iu';
    if(preg_match($pattern,$text,$m))return trim($m[1]);return '';
}
function personal_contract_worker_text($text){
    if(preg_match('~DATOS\s+(?:DEL(?:\s*/\s*DE)?|DE)\s+(?:LA\s+)?TRABAJADOR(?:A|/A)?[^\n]*\n(.*)~isu',$text,$m)){
        $worker=preg_split('~\b(?:DECLARAN|CL[ÁA]USULAS)\b~iu',$m[1],2);
        return $worker[0];
    }
    // If the employee heading was not recognised, never use the employer's identity.
    return preg_match('~DATOS\s+DE\s+LA\s+EMPRESA~iu',$text)?'':$text;
}
function personal_contract_name_parts($full){
    $data=array();$full=trim($full," \t\n\r[]|:.");
    if($full===''||preg_match('/[0-9:]/u',$full))return $data;
    if(strpos($full,',')!==false){
        list($last,$first)=array_map('trim',explode(',',$full,2));
        if($last===''||$first==='')return $data;
        $data['Nombre']=$first;$parts=preg_split('/\s+/u',$last);$surname=array();
        do{$word=array_shift($parts);$surname[]=$word;}while($parts&&in_array(mb_strtolower($word,'UTF-8'),array('de','del','la','las','los'),true));
        $data['Apellido1']=implode(' ',$surname);$data['Apellido2']=implode(' ',$parts);
    }else{
        $parts=preg_split('/\s+/u',$full);
        if(count($parts)>=3){$data['Apellido2']=array_pop($parts);$data['Apellido1']=array_pop($parts);$data['Nombre']=implode(' ',$parts);}
    }
    return $data;
}
function personal_contract_table_name($worker){
    // In SEPE forms the name and ID share a row under a row of labels, without colons.
    $lines=preg_split('/\n/u',$worker);
    foreach($lines as $line){
        if(preg_match('/\b(?:[XYZ][ \t]*\d{7}|\d{8})[ \t-]*[A-Z]\b/iu',$line,$id,PREG_OFFSET_CAPTURE)){
            $prefix=substr($line,0,$id[0][1]);$cells=preg_split('/\t| {2,}|\|/u',$prefix);
            foreach($cells as $cell){
                $cell=trim($cell," \t[]|.0123456789");
                if(preg_match("/^[\\p{L}][\\p{L} .’'\-]+,\\s*[\\p{L}][\\p{L} .’'\-]*$/u",$cell))return $cell;
            }
        }
    }
    return '';
}
function personal_contract_parse($text){
    if(!is_string($text)||strlen($text)>350000||!preg_match('//u',$text))throw new InvalidArgumentException('El texto del contrato no es válido.');
    $text=str_replace(array("\r\n","\r","\xc2\xa0"),array("\n","\n",' '),$text);
    $data=array();$worker=personal_contract_worker_text($text);
    foreach(array('Nombre'=>'Nombre(?:\s+del\s+trabajador)?','Apellido1'=>'Primer\s+apellido|Apellido\s*1','Apellido2'=>'Segundo\s+apellido|Apellido\s*2','Direccion'=>'Domicilio|Direcci[oó]n','email'=>'Correo(?:\s+electr[oó]nico)?|E-?mail','telefono1'=>'Tel[eé]fono(?:\s+m[oó]vil)?|M[oó]vil','profesion'=>'Puesto(?:\s+de\s+trabajo)?|Categor[ií]a\s+profesional') as $key=>$labels){$value=personal_contract_field($worker,$labels);if($value!=='')$data[$key]=$value;}
    $full=personal_contract_field($worker,'Apellidos\s+y\s+nombre|Nombre\s+y\s+apellidos|D\.?\s*\/\s*D[ÑN]A\.?');
    if(!$full)$full=personal_contract_table_name($worker);
    if(!$full&&preg_match('/(?:^|\n)\s*D\.?\s*\/\s*D[ÑN]A\.?[^\n]*\n\s*([^\n\t]+)/iu',$worker,$m))$full=trim($m[1]);
    if($full&&!isset($data['Nombre']))$data=array_merge($data,personal_contract_name_parts($full));
    $last=personal_contract_field($worker,'Apellidos');if($last&&!isset($data['Apellido1'])){$parts=preg_split('/\s+/u',$last);$data['Apellido1']=array_shift($parts);$data['Apellido2']=implode(' ',$parts);}
    if(preg_match_all('/\b(?:[XYZ]\s*\d{7}|\d{8})\s*[- ]?\s*[A-Z]\b/iu',$worker,$m)){$ids=array_unique(array_map(function($v){return preg_replace('/[\s-]+/','',strtoupper($v));},$m[0]));if(count($ids)===1)$data['DNI']=reset($ids);}
    $nss=personal_contract_field($worker,'N[º°o.]*\s*(?:SS|S\.?\s*S\.?|Seguridad\s+Social|de\s+afiliaci[oó]n(?:\s+a\s+la\s+S\.?\s*S\.?)?)|NSS|N[úu]mero\s+(?:de\s+)?(?:Seguridad\s+Social|afiliaci[oó]n)');
    if($nss&&preg_match('/^\s*\d{2}[\s\/.-]*\d{8}[\s\/.-]*\d{2}\s*$/',$nss))$data['NSS']=preg_replace('/\D/','',$nss);
    elseif(preg_match_all('/(?<!\d)\d{2}[ \t\/.-]*\d{8}[ \t\/.-]*\d{2}(?!\d)/u',$worker,$m)){
        $numbers=array_unique(array_map(function($value){return preg_replace('/\D/','',$value);},$m[0]));
        if(count($numbers)===1)$data['NSS']=reset($numbers);
    }
    if(!isset($data['profesion'])&&preg_match('/prestar[áa][ \t]+sus[ \t]+servicios[ \t]+como[ \t]*(?:\(\d+\))?[ \t.\-_]*([\p{L}][^\n\t,;]*)/iu',$text,$m)){
        $job=preg_split('/\.{2,}| {3,}/u',$m[1],2);
        // Scanned dotted leaders can leave an isolated full stop between job words.
        $data['profesion']=trim(preg_replace('/(?<=\p{L})\.(?=\s+\p{L})/u','',$job[0])," \t.");
    }
    $hours=personal_contract_field($text,'Horas\s+semanales|Jornada\s+semanal|Horas\s+por\s+semana');
    if(!$hours&&preg_match('/\b(\d{1,2}(?:[,.]\d{1,2})?)\s*horas\s*(?:a\s+la\s+semana|semanales)/iu',$text,$m))$hours=$m[1];
    if(!$hours&&preg_match_all('/(?:jornada|jomada)[^\n]{0,90}?ser[áa][ \t]+de[ .\-_]*(\d{1,2}(?:[,.]\d{1,2})?)[ .\-_]*horas([^\n]{0,150})/iu',$text,$rows,PREG_SET_ORDER)){
        $weekly=array();foreach($rows as $row){
            // All period labels are preprinted. Only a marked weekly box authorises this unit.
            // OCR may miss the opening edge or read a crossed box as "Xx]".
            if(preg_match('/(?:\[[ \t]*[x×]{1,2}[ \t]*\]|(?<![\p{L}\d])[x×]{1,2}[ \t]*\]|☒|■)[ \t]*a[ \t]*(?:la|ta)[ \t]*semana/iu',$row[2]))$weekly[]=str_replace(',','.',$row[1]);
        }
        $weekly=array_unique($weekly);if(count($weekly)===1)$hours=reset($weekly);
    }
    if($hours&&preg_match('/^(\d{1,2}(?:[,.]\d{1,2})?)/',$hours,$m))$data['horas_semana']=str_replace(',','.',$m[1]);
    foreach(array('fecha_inicio'=>'Fecha\s+(?:de\s+)?inicio|Inicio\s+(?:del\s+)?contrato','fecha_fin'=>'Fecha\s+(?:de\s+)?fin|Fecha\s+(?:de\s+)?finalizaci[oó]n|Fin\s+(?:del\s+)?contrato') as $key=>$labels){$value=personal_contract_field($text,$labels);if($value&&personal_contract_date($value))$data[$key]=personal_contract_date($value);}
    if(!isset($data['fecha_inicio'])&&preg_match('/inici[áa]ndose[^\n]{8,120}/iu',$text,$m)){$date=personal_contract_date($m[0]);if($date)$data['fecha_inicio']=$date;}
    // Do not infer a contract type from boilerplate containing every checkbox choice.
    $type=personal_contract_field($text,'Tipo\s+de\s+contrato');
    if(!$type&&preg_match('/(?:^|\n)[ \t]*CONTRATO[ \t]+(?:DE[ \t]+TRABAJO[ \t]+)?(INDEFINIDO|TEMPORAL|FIJO[ \t]+DISCONTINUO)\b/iu',$text,$m))$type=$m[1];
    if(preg_match('/^indefinido\b/iu',$type))$data['tipo_contrato']=2;
    elseif(preg_match('/^temporal\b/iu',$type))$data['tipo_contrato']=1;
    elseif(preg_match('/^fijo\s+discontinuo\b/iu',$type))$data['tipo_contrato']=6;
    foreach($data as $key=>$value)if(is_string($value))$data[$key]=mb_substr(trim($value),0,$key==='Direccion'?500:255,'UTF-8');
    return $data;
}
