<?php
  // タイムゾーンはダーウィン時間
  date_default_timezone_set('Australia/Darwin');

  require_once 'common_lib/DBControl_lib.php';
  require_once 'common_lib/User_lib.php';

  // GET通信で日付情報取得 「191019」などの6桁の数値を想定
  // dateパラメータが未定義もしくは値が空の場合
  if(!isset($_GET['date']) || $_GET['date'] == ''){
    echo 'dateパラメータを入力してください<br>';
    exit(0);
  }
  // dateパラメータの値が存在する場合
  else{
    // dateパラメータが配列形式でないかどうか
    if(is_array($_GET['date'])){
      echo '配列は受け取れません<br>';
      exit(0);
    }
    // 6桁の数値になっているか
    $datePattern = '/[0-9]{6}/';
    if(!preg_match($datePattern, $_GET['date'])){
      echo 'dateパラメータが異常です<br>';
      exit(0);
    }
    // 6桁の数値になっている場合、代入
    $dateStr = $_GET['date'];
  }

  // csv出力ファイルのファイル名を定義 取り出すテーブル名を定義
  $nameList_cnt = count($carNameList);
  for($i = 0; $i < $nameList_cnt; $i++){
    $tableNameList[$i] = '`'.$dateStr.'_'.$carNameList[$i].'`';
    $fileNameList[$i] = $dateStr.'_'.$carNameList[$i].'.csv';
    $filePathList[$i] = 'temp/'.$fileNameList[$i];
  }

  // データベース接続
  $dataBase = new DataBaseControl($dataBaseType, $dataBaseName, $hostName, $userName, $password, $portNumber);

  $fileHeaderList = array();
  $dataAry = array();
  // 取り出すテーブルの個数分ループを行う
  for($i = 0; $i < $nameList_cnt; $i++){
    // テーブルの情報を取得
    $fileHeaderList_tmp = $dataBase->DataBaseTableGetColumn($tableNameList[$i]);
    $index = count($fileHeaderList_tmp);

    for($j = 0; $j < $index; $j++){
      $fileHeaderList[$i][$j] = $fileHeaderList_tmp[$j]['Field'];  // 「Field」にカラム名が格納されている
    }

    // 唐突な0要素目の削除（別になくても良い
    $fileHeaderList[$i][0] = '';

    // テーブルのデータを取得
    $dataAry = $dataBase->DataBaseFetchAll($tableNameList[$i], 'logtime', 'ASC');
    // $dataAryのcsv書き出し処理
    if(touch($filePathList[$i])){
      $file = fopen($filePathList[$i], 'w');
      if($file){
        foreach($fileHeaderList[$i] as &$header){
          $header = mb_convert_encoding($header, 'SJIS-win', 'UTF-8');
        }
        unset($header);
        fputcsv($file, $fileHeaderList[$i]);
        foreach($dataAry as $data){
          fputcsv($file, $data);
        }
      }
      fclose($file);
      // $contentLength = filesize($fileName);
    }
  }

  // データベース切断
  $dataBase->DataBaseDisconnect();

  $zip = new ZipArchive();

  $zipPathTemp = 'temp/';
  $zipFileName = $dateStr.'_TrackerData.zip';

  $zip->open($zipPathTemp.$zipFileName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);

  for($i = 0; $i < $nameList_cnt; $i++){
    $zip->addFile($filePathList[$i], $fileNameList[$i]);
  }

  $zip->close();

    // 出力
  header('Content-Type: application/zip; name="'.$zipFileName.'"');
  header('Content-Disposition: attachment; filename="'.$zipFileName.'"');
  header('Content-Length: '.filesize($zipPathTemp.$zipFileName));
  echo file_get_contents($zipPathTemp.$zipFileName);

  // zipを削除する
  unlink($zipPathTemp.$zipFileName);
 ?>
