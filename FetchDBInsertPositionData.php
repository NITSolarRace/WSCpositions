<?php
  date_default_timezone_set('Australia/Darwin');
  require_once 'common_lib/DBControl_lib.php';
  require_once 'common_lib/User_lib.php';

  define('API_URL', 'https://www.worldsolarchallenge.org/api/positions');

  // パラメータと値を投げたい時
  // $apiRequestData = http_build_query(array(
  //   'latitude' => $latitude,
  //   'longitude' => $longitude,
  //   'format' => 'json',
  //   'api_key' => SOLCAST_API_KEY
  // ));
  // $apiResponceData = file_get_contents(API_URL.'?'.$apiRequestData);
  
  // URL先のwebデータを取得
  $apiResponceData = file_get_contents(API_URL);
  // jsonで返ってくるので、jsonをデコード
  $positionData = json_decode($apiResponceData, true);
  $positionData_cnt = count($positionData);

  // echo '<pre>';
  // var_dump($positionData);
  // echo '</pre>';

  // 生のjsonデータも保存しておく
  $nowTime = date('ymdHis', time());
  $fileName = 'json/'.$nowTime.'_position.json';
  file_put_contents($fileName, $apiResponceData);

  // データベース保存用にデータを整理していく
  $positionColNameList = array('id', 'name', 'number', 'car_name', 'country', 'class_id', 'lat', 'lng', 'gps_when', 'position', 'trailered', 'dist_darwin', 'dist_adelaide', 'gps_age');
  $positionColNameList_cnt = count($positionColNameList);
  $dataBaseInsertData = array();
  // データ取得ごとに得られる配列の順番がバラバラなのでその対策
  // User_lib.phpの$carNameListの順番に配列を並べ替えるイメージ
  // $positionData全ループ
  for($i = 0; $i < $positionData_cnt; $i++){
    // チーム名に関して整理
    foreach($carNameList as $name){
      if($positionData[$i]['name'] == $name){  // チーム名リストとチーム名合致
        $insertIndex = array_search($name, $carNameList);  // 要素番号取得
        // チーム名リストの順番に並べ替え、各カラムを代入
        $dataBaseInsertData[$insertIndex][0] = date('Y-m-d H:i:s', time());  // サーバーの記録時間「logtime」を挿入
        for($j = 0; $j < $positionColNameList_cnt; $j++){
          $dataBaseInsertData[$insertIndex][$j + 1] = $positionData[$i][$positionColNameList[$j]];  // 残りはpositionColNameList
        }
      }
    }
  }

  // echo '<pre>';
  // var_dump($dataBaseInsertData);
  // echo '</pre>';

  // データベース接続
  $dataBase = new DataBaseControl($dataBaseType, $dataBaseName, $hostName, $userName, $password, $portNumber);
  
  // 「日付_チーム名」をテーブル名とする
  $dateStr = date('ymd', time());
  $tableName_tmp = $dateStr.'_';
  $dataBaseColName = array_merge(array('logtime'), $positionColNameList);
  
  for($i = 0; $i < $positionData_cnt; $i++){
    $tableName = '`'.$tableName_tmp.$carNameList[$i].'`';  // チーム名に半角スペースが入っているので、バッククオート必要
    $dataBase->DataBaseCreateTable($tableName, $dataBaseColName, 'TIMESTAMP');  // テーブルがない場合はテーブルを作成
    $dataBase->DataBaseReplace($tableName, $dataBaseInsertData[$i]);  // Replace実行
  }

  // データベース切断
  $dataBase->DataBaseDisconnect();

  exit(0);
?>