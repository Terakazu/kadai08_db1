<?php
//1. POSTデータ取得
$date = $_POST["date"];
$memo = $_POST["memo"];
if (isset($_POST['emotion']) && is_array($_POST['emotion'])) {
  $emotion = implode("、", $_POST["emotion"]);
}

// 画像ファイルの取得
$image_path = $_FILES["image"]["tmp_name"]; // 一時ファイルのパス
$image_name = $_FILES["image"]["name"]; // アップロードされたファイルの名前
$upload_path = "images/" . $image_name; // 画像を保存するパス（適宜変更してください）

// 画像を指定のパスに移動します
move_uploaded_file($image_path, $upload_path);


// 2. DB接続します
// function db_conn()
// {
    try {
        $db_name =  '*********';            //データベース名
        $db_host =  '*********';  //DBホスト
        $db_id =    '*********';                //アカウント名(登録しているドメイン)
        $db_pw =    '**********';           //さくらサーバのパスワード
        
        $server_info ='mysql:dbname='.$db_name.';charset=utf8;host='.$db_host;
        $pdo = new PDO($server_info, $db_id, $db_pw);
        // return $pdo;

    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
  

//SQLエラー
function sql_error($stmt)
{
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit('SQLError:' . $error[2]);
}

// try {
//   //Password:MAMP='root',XAMPP=''
//     $pdo = new PDO('mysql:dbname=jd_db;charset=utf8;host=localhost','root','');
//   } catch (PDOException $e) {
//     exit('DB_CONECT:'.$e->getMessage());
//   }


//３．データ登録SQL作成
$sql = "INSERT INTO jd_an_table(memo,emotion,date,image_path)VALUES(:memo,:emotion, :date,  :image_path);";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':memo', $memo, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':emotion', $emotion, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':date', $date, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':image_path', $upload_path, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}else{
  //５．index.phpへリダイレクト
  header("Location: index.php");
  exit();
}
?>
