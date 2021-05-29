

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>掲示板</title>
  {if isset($agentno)}
      {if $agentno == 1 }
      <link rel="stylesheet" href="./css/kadai_4_3_SP.css">
      {elseif $agentno == 2}
      <link rel="stylesheet" href="./css/kadai_4_3_FP.css">
      {elseif $agentno == 3}
      <link rel="stylesheet" href="./css/kadai_4_3_PC.css">
      {/if}
     {/if} 
</head>
<body>
<h1>PHP簡易掲示板</h1>
<!--ここで投稿内容を送信する-->
<form action="kadai_4_3_main.php" method="post" enctype="multipart/form-data">
    名前：<br />
  <input type="text" name="name" size="30" value="{if isset($namae)}{$namae}{/if}" /><br />
  コメント：<br />
  <textarea name="comment" cols="30" rows="5" >{if isset($text)}{$text}{/if}</textarea><br />
  <input type="hidden" name="edit_name" value="{if isset($num2)}{$num2}{/if}">
  ファイル：<br />
  <input type="file" name="file">
  <br />
  パスワード：<br />
  <input type="password" name="password_in" size="30" /><br />
  <input type="submit" name="send_message" value="投稿" />
</form>
<!-- 削除番号入力フォーム -->
<form action="kadai_4_3_main.php" method="post">
        <input type="number" name = "delete" placeholder="削除対象番号"><br />
        パスワード：
        <input type="password" name="password_out_delete" size="30" />
        <input type="submit" name="send_delete" value = "削除">
</form>
<!-- 編集番号入力フォーム -->
<form action="kadai_4_3_main.php" method="post">
    <input type="number" name="edit" value="" placeholder="編集対象番号"><br />
    パスワード：
    <input type="password" name="password_out_edit" size="30" />
    <input type="submit" name="send_edit" value="送信">
</form>

<form action="kadai_4_3_main.php" method="post">
<input type='submit' name='logout' value='ログアウト'>
</form>

<h2>投稿一覧</h2>
{foreach $keijibann as $row}
    <p>{$row['num']} : {$row['name']} {$row['date']}</p>
    <p>{$row['comment']}</p>
    {if in_array($row['ext'], $img_ext)}
      <img src="upfile/{$row['filename']}" width="200" height="150">
    {elseif in_array($row['ext'], $video_ext)}
      <video src="upfile/{$row['filename']}" width="200" height="150"></video>
    {/if}
{/foreach}  


    
</body>
</html>