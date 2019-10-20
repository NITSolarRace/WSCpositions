# WSCposition
WSCカートラッカーの位置情報自動回収&データベース保存、CSVログ出力プログラム

## 概要
https://www.worldsolarchallenge.org/api/positions （2019年10月20日現在）から位置情報のデータを取得し、
データベースに保存、そのデータを専用Webページを介してCSV形式でダウンロードできるプログラム郡（Webアプリ）です

## 動作方法
#### データベース情報の記入
common_lib/User_lib.phpにデータベース情報を入力してください<br>
  // データベース情報<br>
  $dataBaseType = 'mysql';<br>
  $dataBaseName = '';<br>
  $hostName = '';<br>
  $userName = '';<br>
  $password = '';<br>
  $portNumber = '';<br>

#### cronの実行
FetchDBInsertPositionData.phpを5分おきにcronで自動実行させてください

## ユーザーの使い方
DownloadTrackerData.htmlにアクセスすると、ダウンロード専用ページが開きます<br>
そこで日付を選択、出力ボタンをクリックするとダウンロードダイアログが開くので、データをダウンロードしてください

