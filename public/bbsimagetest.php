<?php

$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');



if (isset($_POST['body'])) {

  // POSTで送られてくるフォームパラメータ body がある場合



  $image_filename = null;

  if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {

    // アップロードされた画像がある場合

    if (preg_match('/^image\//', mime_content_type($_FILES['image']['tmp_name'])) !== 1) {

      // アップロードされたものが画像ではなかった場合処理を強制的に終了

      header("HTTP/1.1 302 Found");

      header("Location: ./bbsimagetest.php");

      return;

    }



    // 元のファイル名から拡張子を取得

    $pathinfo = pathinfo($_FILES['image']['name']);

    $extension = $pathinfo['extension'];

    // 新しいファイル名を決める。他の投稿の画像ファイルと重複しないように時間+乱数で決める。

    $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.' . $extension;

    $filepath =  '/var/www/upload/image/' . $image_filename;

    move_uploaded_file($_FILES['image']['tmp_name'], $filepath);

  }



  // insertする

  $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body, image_filename) VALUES (:body, :image_filename)");

  $insert_sth->execute([

    ':body' => $_POST['body'],

    ':image_filename' => $image_filename,

  ]);



  // 処理が終わったらリダイレクトする

  // リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる

  header("HTTP/1.1 302 Found");

  header("Location: ./bbsimagetest.php");

  return;

}



// いままで保存してきたものを取得

$select_sth = $dbh->prepare('SELECT * FROM bbs_entries ORDER BY created_at DESC');

$select_sth->execute();

?>


<head>


  <title>画像投稿できる掲示板</title>


</head>



<!-- フォームのPOST先はこのファイル自身にする -->

<form method="POST" action="./bbsimagetest.php" enctype="multipart/form-data">
