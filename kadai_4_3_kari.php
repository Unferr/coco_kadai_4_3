<?php

require_once './Cache/Lite.php';
$url = "http://co-19-332.99sv-coco.com/coco_kadai_4_3/kadai_4_3_kari/kadai_4_3_kari.php"; 
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

require_once("./libs/Smarty.class.php");
$smarty = new Smarty();
$smarty -> template_dir = "templates";
$smarty -> compile_dir = "templates_c";



// 以下に接続する
// DB名：co_19_332_99sv_coco_com
// ユーザ名：co-19-332.99sv-c
// パスワード：’pW5Bt4KM’

// データベースに接続
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
$sql = "CREATE TABLE IF NOT EXISTS touroku (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
userid VARCHAR(30) NOT NULL,
name VARCHAR(30) NOT NULL,
pw VARCHAR(30) NOT NULL,
date VARCHAR(30) NOT NULL,
MAIL VARCHAR(60) NOT NULL,
flag VARCHAR(1) DEFAULT '0',
urlid VARCHAR(64) NOT NULL
)";
$prepare = $dbh->prepare($sql);
$prepare->execute();

// DBに保存
$name = $pw = '';
if (isset($_POST['send'])) {
 	$mail = $_POST['mail'];
	if (empty($_POST['mail'])) {
    echo '未入力項目があります';
  }

  $sql = "SELECT id FROM touroku WHERE MAIL=:mail AND flag=1";
      $stmt = $dbh->prepare($sql);
      $stmt->execute( array(
    ':mail' => $mail
    ));
      if($stmt != null ){
        foreach ($stmt as $row) {
            $id = $row['id'];
        }
}

  if (!empty($id)) {
    echo '<a href="../kadai_4_3_login/kadai_4_3_login.php">ログイン画面へ</a><br />';
    echo 'すでに登録されているメールアドレスです。';
  }
  else{
    $userid = uniqid(rand());
    $url_id = hash('sha256',uniqid(rand(),1));
    $url = 'http://co-19-332.99sv-coco.com/coco_kadai_4_3/kadai_4_3_touroku/kadai_4_3_touroku.php'.'?url_id='.$url_id;
    $title = '本登録手続きのお知らせ';
    $message = 
    "ユーザーID\n"
    .$userid
    ."\n"
    ."24時間以内に、下記リンクにアクセスし、本登録をしてください。\n"
    .$url
    ."\n";

    mb_language("ja");
    mb_internal_encoding("UTF-8");
    if (mb_send_mail($mail, $title, $message)) {
      if (isset($_COOKIE["PHPSESSID"])) {
        setcookie("PHPSESSID", '', time() - 1800, '/');
      }
      $date = date('Y/m/d H:i:s');
      $sql = 'INSERT INTO touroku(userid, date, MAIL, urlid) VALUES(:userid, :date, :MAIL, :urlid)';
      $prepare = $dbh->prepare($sql);
      $prepare->bindValue(':userid', $userid, PDO::PARAM_STR);
      $prepare->bindValue(':date', $date, PDO::PARAM_STR);
      $prepare->bindValue(':MAIL', $mail, PDO::PARAM_STR);
      $prepare->bindValue(':urlid', $url_id, PDO::PARAM_STR);
      $prepare->execute();
    } else {
      echo 'メールの送信に失敗しました';
      }
	}
}

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

    $smarty -> display("kadai_4_3_kari.tpl");
?>


