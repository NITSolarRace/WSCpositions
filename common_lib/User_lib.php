<?php

  define('OFFSET_TIME', 9 * 3600);

  // データベース情報
  $dataBaseType = 'mysql';
  $dataBaseName = '';
  $hostName = '';
  $userName = '';
  $password = '';
  $portNumber = '';

  $carNameList = array(
    'Solar Team Twente',
    'Top Dutch Solar Racing',
    'Vattenfall Solar Team',
    'Agoria Solar Team',
    'Team Sonnenwagen Aachen e.V.',
    'Tokai University Solar Car Team',
    'University of Michigan Solar Car Team',
    'Blue Sky Solar Racing',
    'Sunswift',
    'Kogakuin University Solar Team',
    'JU Solar Team',
    'NITech Solar Racing',
    'Solar Team Eindhoven',
    'Western Sydney Solar Team',
    'Antakari',
    'Lodz Solar Team',
    'Team Arrow',
    'IVE Engineering Solar Car Team',
    'Onda Solare',
    'Eclipse',
    'AUSRT',
    'Goko High School',
    'University of Minnesota Solar Vehicle Project',
    'Solar Energy Racers SER',
    'Sun Shuttle',
    'Durham University Electric Motorsport',
    'MTAA Super Sol Invictus',
    'Chalmers Solar Team',
    'Siam Technology Motor Sport (STC3)',
    'Cambridge University Eco Racing',
    'Team Solaris',
    'Ardingly College Solar',
    'KUST(Kookmin Univ. Solarcar Team)',
    'TAFE South Australia',
    'UiTM EcoPhoton Solar Racing Team',
    'Flinders Automotive Solar Team',
    'CalSol',
    'Halmstad University Solar Team',
    'Stanford Solar Car Project',
    'SolarCar-Team Hochschule Bochum',
    'MDH Solar Team'
  );

  function CreateNoonTime($timeStr){
    $baseTimeStamp = strtotime(date('Y/m/d', time() + OFFSET_TIME).' 12:00:00');
    $timeStrTimeStamp = strtotime(date('Y/m/d', time() + OFFSET_TIME).$timeStr);

    return $timeStrTimeStamp - $baseTimeStamp;
  }

  function SearchStr($str, $ary){
    foreach($ary as $val){
      if($val === $str){
        return true;
      }
    }
    return false;
  }

  function h($data){
    if(is_array($data)){
      return array_map("h", $data);
    }
    else{
      return htmlspecialchars($data, ENT_QUOTES);
    }
  }
 ?>
