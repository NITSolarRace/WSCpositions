<?php
  class DataBaseControl{
    private $dataBaseObj;

    public function __construct($type, $name, $host, $user, $pass, $port){
      $dsn = sprintf("%s:host=%s;dbname=%s;port=%s", $type, $host, $name, $port);
      try{
        $dbobj = new PDO($dsn, $user, $pass);
        $dbobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch(PDOException $e){
        print('データベースに接続できませんでした:'.$e->getMessage());
        exit(0);
      }
      $this->dataBaseObj = $dbobj;
    }

    /**
     * データベースにテーブルを作成
     * 引数
     * $tableName 作成するテーブルの名称
     * $colNameAry 作成するテーブルの列名が代入された配列
     * $type PRIMARY KEYになる列名の型（デフォルトでTIME型） その他はTEXT型で格納
     */
    public function DataBaseCreateTable($tableName, $colNameAry, $type = 'TIME'){
      $colNameAry_cnt = count($colNameAry);
      $sqlStr = 'CREATE TABLE IF NOT EXISTS '.$tableName.'(';
      for($i = 0; $i < $colNameAry_cnt; $i++){
        switch ($i) {
          case 0:
            $sqlStr .= sprintf("`%s` %s PRIMARY KEY,", $colNameAry[$i], $type);
            break;
          case $colNameAry_cnt - 1:
            $sqlStr .= sprintf("`%s` TEXT);", $colNameAry[$i]);
            break;
          default:
            $sqlStr .= sprintf("`%s` TEXT,", $colNameAry[$i]);
            break;
        }
      }
      try{
        $sth = $this->dataBaseObj->query($sqlStr);
      }
      catch (PDOException $e){
        echo "<br>".$e->getMessage();
        exit(0);
      }
    }

    /**
     * テーブルにレコードを挿入
     * 引数
     * $tableName 挿入されるテーブルの名称
     * $dataAry レコードの配列
     */
    public function DataBaseInsert($tableName, $dataAry){
      $dataAry_cnt = count($dataAry);

      $sqlStr = 'INSERT INTO '.$tableName.' VALUES (';

      for($i = 0; $i < $dataAry_cnt; $i++){
        switch ($i) {
          case $dataAry_cnt - 1:
            $sqlStr .= "'".$dataAry[$i]."'".');';
            break;
          default:
            $sqlStr .= "'".$dataAry[$i]."'".', ';
            break;
        }
      }
      try{
        $this->dataBaseObj->exec($sqlStr);
      }
      catch(PDOException $e){
        echo 'データベースINSERTエラー<br>';
        $err_mes = $e->getMessage();
        echo $err_mes.'<br>';
        exit(0);
      }
    }

    /**
     * テーブルにレコードを置き換え
     * 引数
     * $tableName 挿入されるテーブルの名称
     * $dataAry レコードの配列
     * 
     * PRIMARY KEYが同一の値のものは置き換えられる
     */
    public function DataBaseReplace($tableName, $dataAry){
      $dataAry_cnt = count($dataAry);

      $sqlStr = 'REPLACE INTO '.$tableName.' VALUES (';

      for($i = 0; $i < $dataAry_cnt; $i++){
        switch ($i) {
          case $dataAry_cnt - 1:
            $sqlStr .= "'".$dataAry[$i]."'".');';
            break;
          default:
            $sqlStr .= "'".$dataAry[$i]."'".', ';
            break;
        }
      }
      try{
        $this->dataBaseObj->exec($sqlStr);
      }
      catch(PDOException $e){
        echo 'データベースREPLACEエラー<br>';
        $err_mes = $e->getMessage();
        echo $err_mes.'<br>';
        exit(0);
      }
    }

    public function DataBaseDisconnect(){
      $this->dataBaseObj = null;
    }

    /**
     * テーブルにレコードを全取得
     * 引数
     * $tableName 取得先のテーブル名
     * $sortColName に関してソートする列名
     * $sortConfig 昇順か降順か
     * 
     * return
     * 結果の連想配列
     */
    public function DataBaseFetchAll($tableName, $sortColName, $sortConfig = 'ASC'){
      $sql = sprintf("SELECT * FROM %s ORDER BY %s %s;", $tableName, $sortColName, $sortConfig);

      try{
        $sth = $this->dataBaseObj->query($sql);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
      }
      catch(PDOException $e){
        // echo 'Error:データベースを取得できませんでした<br>';
        // echo $e->getMessage().'<br>';
        // exit(0);
        $result = 0;
      }

      return $result;
    }


    /**
     * テーブルにレコードをWHERE文付きで取得
     * 引数
     * $tableName 取得先のテーブル名
     * $whereStr WHERE文
     * $sortColName に関してソートする列名
     * $sortConfig 昇順か降順か
     * 
     * return
     * 結果の連想配列
     */
    public function DataBaseFetchWhere($tableName, $whereStr, $sortColName, $sortConfig = 'ASC'){
      $sql = sprintf("(SELECT * FROM %s WHERE %s ORDER BY %s %s) ORDER BY %s ASC;", $tableName, $whereStr, $sortColName, $sortConfig, $sortColName);

      $result = array();
      $index = 0;
      try{
        $sth = $this->dataBaseObj->query($sql);
        while($result_tmp = $sth->fetch(PDO::FETCH_ASSOC)){
          $result[$index++] = $result_tmp;
        }
      }
      catch(PDOException $e){
        // echo 'Error:データベースを取得できませんでした<br>';
        // echo $e->getMessage().'<br>';
        // exit(0);
        $result = 0;
      }

      return $result;
    }

    /**
     * テーブルにレコードを取得数制限付きで取得
     * 引数
     * $tableName 取得先のテーブル名
     * $sortColName に関してソートする列名
     * $sortConfig 昇順か降順か
     * $limit 制限数（整数）
     * 
     * return
     * 結果の連想配列
     */
    public function DataBaseFetchLimit($tableName, $sortColName, $sortConfig = 'ASC', $limit){
      $sql = sprintf("(SELECT * FROM %s ORDER BY %s %s LIMIT %d) ORDER BY %s ASC;", $tableName, $sortColName, $sortConfig, $limit, $sortColName);

      $result = array();
      $index = 0;
      try{
        $sth = $this->dataBaseObj->query($sql);
        while($result_tmp = $sth->fetch(PDO::FETCH_ASSOC)){
          $result[$index++] = $result_tmp;
        }
      }
      catch(PDOException $e){
        // echo 'Error:データベースを取得できませんでした<br>';
        // echo $e->getMessage().'<br>';
        // exit(0);
        $result = 0;
      }

      return $result;
    }

    /**
     * テーブルを削除
     * 引数
     * $tableName 削除するテーブルの名称
     */
    public function DataBaseTableDelete($tableName){
      $sql = sprintf("DROP TABLE %s;", $tableName);
      try{
        $sth = $this->dataBaseObj->query($sql);
      }
      catch(PDOException $e){
        // return $e->getMessage();
        return false;
      }
      return true;
    }

    /**
     * テーブルの構造の情報を取得
     * 引数
     * $tableName 取得するテーブルの名称
     * 
     * return
     * 結果の連想配列
     */
    public function DataBaseTableGetColumn($tableName){
      $sql = sprintf("DESCRIBE %s", $tableName);
      $result = array();
      $index = 0;
      try{
        $sth = $this->dataBaseObj->query($sql);
        while($result_tmp = $sth->fetch(PDO::FETCH_ASSOC)){
          $result[$index++] = $result_tmp;
        }
      }
      catch(PDOException $e){
        // echo 'Error:データベースを取得できませんでした<br>';
        // echo $e->getMessage().'<br>';
        // exit(0);
        $result = 0;
      }

      return $result;
    }
  }

 ?>
