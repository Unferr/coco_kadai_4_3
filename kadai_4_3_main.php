<?php

require_once './Cache/Lite.php';
$url = "http://co-19-332.99sv-coco.com/coco_kadai_4_3/kadai_4_3_main/kadai_4_3_main.php"; 
$id = $url;                
$options = array(
    'cacheDir'=>$_SERVER[ 'DOCUMENT_ROOT' ].'/tmp/',
    'lifeTime'=>3600,
    'pearErrorMode'=>CACHE_LITE_ERROR_DIE
    );
if( !file_exists( $options[ 'cacheDir' ] ) )
{
    mkdir( $options[ 'cacheDir' ], 0705 );
}
$cache = new Cache_Lite( $options );
$data = $cache->get( $id );
if( $data === FALSE )
{
    $data = file_get_contents( $url );
    $cache->save( $data, $id );
}
var_dump( $data );

require( dirname( __FILE__ ).'/libs/Smarty.class.php' );

$smarty = new Smarty();

$smarty->template_dir = dirname( __FILE__ ).'/templates';
$smarty->compile_dir  = dirname( __FILE__ ).'/templates_c';

session_start();
if (empty($_SESSION['touroku'])) {
    header('Location:../kadai_4_3_login/kadai_4_3_login.php');
} else {

// DB名：co_19_332_99sv_coco_com
// ユーザ名：co-19-332.99sv-c
// パスワード：’pW5Bt4KM’

// MySQL データベースに接続
$dsn = 'mysql:dbname=co_19_332_99sv_coco_com;host=localhost';
$user = 'co-19-332.99sv-c';
$password = 'pW5Bt4KM';
try {
    $dbh = new PDO($dsn, $user, $password);
    
} catch (PDOException $e) {
    echo "接続失敗: " . $e->getMessage() . "\n";
    exit();
}

// テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS keijibann (
num INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
name VARCHAR(30) NOT NULL,
comment VARCHAR(30) NOT NULL,
date VARCHAR(30) NOT NULL,
pw VARCHAR(30) NOT NULL,
filename TEXT DEFAULT NULL,
ext VARCHAR(10) DEFAULT NULL
)";
$prepare = $dbh->prepare($sql);
$prepare->execute();

$img_ext = ['image/jpeg', 'image/png', 'image/gif'];
$video_ext = ['video/mp4', 'video/avi', 'video/mov'];
$smarty -> assign('img_ext',$img_ext);
$smarty -> assign('video_ext',$video_ext);
    if (!file_exists('upfile')){
        mkdir('upfile', 0777);
        }
}


if(isset($_SESSION['touroku'])) {
    $name2 = $_SESSION['touroku']['name'];
    if (!isset($edit)) {
        $smarty -> assign('namae',$name2);
    }
}

 function h($str){
 return htmlspecialchars($str,ENT_QUOTES,'UTF-8');}
// 投稿機能
 if(!empty($_POST['name'])&&!empty($_POST['comment'])&&!empty($_POST['password_in'])&&empty($_POST['edit_name'])){
    $comment=$_POST['comment'];
    $name=$_POST['name'];
    $date=date("Y/m/d H:i:s");
    $password_in=$_POST['password_in'];
    if(empty($_FILES['file']['tmp_name'])){
    $sql = 'INSERT INTO keijibann (name, comment, date, pw) VALUE (:name, :comment, :date, :pw)';
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(':name', $name, PDO::PARAM_STR);
    $prepare->bindValue(':comment', $comment, PDO::PARAM_STR);
    $prepare->bindValue(':date', $date, PDO::PARAM_STR);
    $prepare->bindValue(':pw', $password_in, PDO::PARAM_STR);
    $prepare->execute();
    }
    else{
        $file_name = htmlspecialchars($_FILES['file']['name']);
        $tmp_name = $_FILES['file']['tmp_name'];
        $ext = $_FILES['file']['type'];
        global $img_ext, $video_ext;
        $allowed_ext = array_merge($img_ext, $video_ext);
        if (!in_array($ext, $allowed_ext)) {
            throw new Exception('ファイルタイプが許可されていません');
        }
        if (!move_uploaded_file($tmp_name, 'upfile/'.$file_name)) {
        throw new Exception('ファイルの保存に失敗しました');
        }

        $sql = 'INSERT INTO keijibann (name, comment, date, pw, filename, ext) VALUE (:name, :comment, :date, :pw, :filename, :ext)';
        $prepare = $dbh->prepare($sql);
        $prepare->bindValue(':name', $name, PDO::PARAM_STR);
        $prepare->bindValue(':comment', $comment, PDO::PARAM_STR);
        $prepare->bindValue(':date', $date, PDO::PARAM_STR);
        $prepare->bindValue(':pw', $password_in, PDO::PARAM_STR);
        $prepare->bindValue(':filename', $file_name, PDO::PARAM_STR);
        $prepare->bindValue(':ext', $ext, PDO::PARAM_STR);
        $prepare->execute();
        }
    }

//ログアウト
if (isset($_POST['logout'])) {
    unset($_SESSION['touroku']);
    header('Location:../kadai_4_3_login/kadai_4_3_login.php');
}
  
// 削除機能
if (!empty($_POST['send_delete'])&&!empty($_POST['password_out_delete'])) {
    $password_out_delete=$_POST['password_out_delete'];
    $delete = $_POST['delete'];

    $sql = "SELECT pw FROM keijibann WHERE num = $delete";
    $sth = $dbh -> query($sql);
    $pwoutdelete = $sth -> fetch(PDO::FETCH_COLUMN); 
    if(strcmp($password_out_delete, trim($pwoutdelete))==0){
        $sql = 'DELETE FROM keijibann WHERE num = :num';
        $prepare = $dbh->prepare($sql);
        $prepare->bindValue(':num', $delete, PDO::PARAM_INT);
        $prepare->execute();
        $sql = 'ALTER TABLE `keijibann` auto_increment = 1;';
        $prepare = $dbh->prepare($sql);
        $prepare->execute();
    }
}


// 編集機能
    // フォームに表示
if (!empty($_POST['send_edit'])&&!empty($_POST['password_out_edit'])) {
    $password_out_edit=$_POST['password_out_edit'];
    $edit = $_POST['edit'];
    $sql = "SELECT pw FROM keijibann WHERE num = $edit";
    $sth = $dbh -> query($sql);
    $pwoutedit = $sth -> fetch(PDO::FETCH_COLUMN);
    if(strcmp($password_out_edit, trim($pwoutedit))==0){
        $sql = "SELECT * FROM keijibann WHERE num = $edit";
        $stmt = $dbh->query($sql);
        foreach ($stmt as $row) {
            $num2 = $row['num'] ;
            $name2 = $row['name'];
            $comment2 = $row['comment'];
        }
    }
    else {
        $comment2 = "パスワードが違います。";
    }
if(isset($edit)){
$smarty -> assign('namae',$name2);
$smarty -> assign('text',$comment2);
$smarty -> assign('num2',$num2);
}
}
    // 編集内容の投稿
if (!empty($_POST['edit_name'])) {
    if(empty($_FILES['file']['tmp_name'])){
        $num2 = $_POST['edit_name'];
        $sql = 'UPDATE keijibann SET name=:name, comment=:comment, date=:date, pw=:pw WHERE num=:num';
        $name=$_POST['name'];
        $comment=$_POST['comment'];
        $date=date("Y/m/d H:i:s");
        $password_in=$_POST['password_in'];
        $prepare = $dbh->prepare($sql);
        $prepare->bindValue(':num', $num2, PDO::PARAM_INT);
        $prepare->bindValue(':name', $name, PDO::PARAM_STR);
        $prepare->bindValue(':comment', $comment, PDO::PARAM_STR);
        $prepare->bindValue(':date', $date, PDO::PARAM_STR);
        $prepare->bindValue(':pw', $password_in, PDO::PARAM_STR);
        $prepare->execute();  
    }
    else{
        $file_name = htmlspecialchars($_FILES['file']['name']);
        $tmp_name = $_FILES['file']['tmp_name'];
        $ext = $_FILES['file']['type'];
        global $img_ext, $video_ext;
        $allowed_ext = array_merge($img_ext, $video_ext);
        if (!in_array($ext, $allowed_ext)) {
            throw new Exception('ファイルタイプが許可されていません');
        }
        if (!move_uploaded_file($tmp_name, 'upfile/'.$file_name)) {
        throw new Exception('ファイルの保存に失敗しました');
        }
        $num2 = $_POST['edit_name'];
        $sql = 'UPDATE keijibann SET name=:name, comment=:comment, date=:date, pw=:pw, filename=:filename, ext=:ext WHERE num=:num';
        $name=$_POST['name'];
        $comment=$_POST['comment'];
        $date=date("Y/m/d H:i:s");
        $password_in=$_POST['password_in'];
        $prepare = $dbh->prepare($sql);
        $prepare->bindValue(':num', $num2, PDO::PARAM_INT);
        $prepare->bindValue(':name', $name, PDO::PARAM_STR);
        $prepare->bindValue(':comment', $comment, PDO::PARAM_STR);
        $prepare->bindValue(':date', $date, PDO::PARAM_STR);
        $prepare->bindValue(':pw', $password_in, PDO::PARAM_STR);
        $prepare->bindValue(':filename', $file_name, PDO::PARAM_STR);
        $prepare->bindValue(':ext', $ext, PDO::PARAM_STR);
        $prepare->execute();  
    }
}


$sql = 'SELECT * from keijibann';
$keijibann = $dbh->prepare($sql);
$keijibann->execute();
$smarty -> assign('keijibann',$keijibann);

if(isset($_SERVER['HTTP_USER_AGENT'])) {
       $agent = $_SERVER['HTTP_USER_AGENT'];
    }
      if((strpos($agent, 'Mobile') !== false) || (strpos($agent, 'Android') !== false) || (strpos($agent, 'Tablet') !== false) || (strpos($agent, 'Windows Phone') !== false) || (strpos($agent, 'iPad') !== false) || (strpos($agent, 'iPhone') !== false) || (strpos($agent, 'Nexus 5') !== false)){
          $agentno = 1;
      } elseif ((strpos($agent, 'SoftBank') !== false) || (strpos($agent, 'KDDI') !== false) || (strpos($agent, 'DoCoMo') !== false)){
          $agentno = 2;
      } elseif((strpos($agent, 'Mac') !== false) || (strpos($agent, 'Windows') !== false) || (strpos($agent, 'Linux') !== false)) {
          $agentno = 3;
      }
  if(isset($agentno)) {
      $smarty->assign('agentno', $agentno);
    }
$smarty -> display("kadai_4_3_main.tpl");

?>

