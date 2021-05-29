<?php session_start(); 

require_once './Cache/Lite.php';
$url = "http://co-19-332.99sv-coco.com/coco_kadai_4_3/kadai_4_3_login/kadai_4_3_login.php"; 
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


if (isset($_POST['login'])) {
  $userid = $_POST['userid'];
  $pw = $_POST['pw'];
  if (!empty($userid)&&!empty($pw)) {
    unset($_SESSION['touroku']);
    $sql = $dbh->prepare("SELECT * FROM touroku WHERE userid=?");
    $sql->execute([$userid]);
    foreach ($sql as $row) {
      if (strcmp($pw, $row['pw'])==0) { 
        $_SESSION['touroku']=[ 
        'userid'=>$row['userid'],
        'name'=>$row['name']];
        header('Location:http://co-19-332.99sv-coco.com/coco_kadai_4_3/kadai_4_3_main/kadai_4_3_main.php');
        exit();
      }
    }
    if (!isset($_SESSION['touroku'])) {
      echo 'IDまたはパスワードが違います';
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
    $smarty -> display("kadai_4_3_login.tpl");
 ?>
