<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
</head>
<body>
     <h2>掲示板</h2>
     <h3>好きな映画やアニメはありますか？</h3>
     <h4>※パスワードは表示されません。</h4>
    <?php
    //【DBに接続】
    $dsn = 'mysql:dbname=*******4db;host=localhost';
    $user = 't*******4';
    $password = 'N*******Gr';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //【テーブルを作成】
    $sql = "CREATE TABLE IF NOT EXISTS keiziban"
    ."("
    ."id INT(11) AUTO_INCREMENT PRIMARY KEY,"
    ."name CHAR(32),"
    ."comment TEXT,"
    ."created_on DATETIME,"
    ."pass CHAR(32)"
    .");";
    //pdoからqueryに＄sqlを渡す
    $stmt = $pdo->query($sql);
    
    //【各種変数設定】
    $edit_name = "";
    $edit_com = "";
    $kari_num = "";
    $edit_pass ="";
    
    //【入力フォーム】
    //入力された時
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass_1"])){
        //各種変数を設定
        $date = new DateTime();
        $date = $date -> format('Y-m-d H:i:s');
        $name = htmlspecialchars($_POST["name"], ENT_QUOTES);
        $comment = htmlspecialchars($_POST["comment"], ENT_QUOTES);
        $pass = htmlspecialchars($_POST["pass_1"],ENT_QUOTES);
        //【編集モード】
        //仮ナンバーが入力された時
        if(!empty($_POST["kari_num"])){
            $id = $_POST["kari_num"];
            $sql ='UPDATE keiziban SET name = :name, comment = :comment, created_on = :created_on, pass = :pass WHERE id = :id';
            //準備
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindParam(':id',$id,PDO::PARAM_INT);
            $stmt -> bindParam(':name',$name,PDO::PARAM_STR);
            $stmt -> bindParam(':comment',$comment,PDO::PARAM_STR);
            $stmt -> bindValue(':created_on',$date,PDO::PARAM_STR);
            $stmt -> bindParam(':pass',$pass,PDO::PARAM_STR);
            //実行
            $stmt -> execute();
            
        //【新規投稿モード】
        }else{
            $sql = 'INSERT INTO keiziban(name,comment,created_on,pass) VALUES(:name,:comment,:created_on,:pass)';
            //準備
            $stmt = $pdo ->prepare($sql);
            $stmt -> bindParam(':name',$name,PDO::PARAM_STR);
            $stmt -> bindParam(':comment',$comment,PDO::PARAM_STR);
            $stmt -> bindValue(':created_on',$date,PDO::PARAM_STR);
            $stmt -> bindParam(':pass',$pass,PDO::PARAM_STR);
            //実行
            $stmt -> execute();
        }
    }
    
    //【削除フォーム】
    if(!empty($_POST["del_num"]) && !empty($_POST["pass_2"])){
        $id = $_POST["del_num"];
        $pass = $_POST["pass_2"];
        //行番号が一致した場合、データを削除
        $sql = 'DELETE FROM keiziban WHERE id = :id AND pass = :pass';
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindParam(':id',$id,PDO::PARAM_INT);
        $stmt -> bindParam(':pass',$pass,PDO::PARAM_STR);
        $stmt -> execute();
    }
    
    //【編集フォーム】
    if(!empty($_POST["edit_num"])){
        $edit_num = $_POST["edit_num"];
        $pass = $_POST["pass_3"];
        //行番号が一致する場合、データを表示
        $sql = 'SELECT * FROM keiziban WHERE id = :id';
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindParam(':id',$edit_num,PDO::PARAM_INT);
        $stmt -> execute();
        //実行結果を配列で取得し$resultsに代入
        $results = $stmt -> fetchAll();
        foreach($results as $row){
            if($pass == $row['pass']){
                $kari_num = $row['id'];
                $edit_name = $row['name'];
                $edit_com = $row['comment'];
                $edit_pass = $row['pass'];
            }
        }
        
    }
    ?>
    
    <form action = "" method="post">
        <input type = "text" name = "name" placeholder = "名前" value ="<?php echo $edit_name; ?>"><br><br>
        <textarea name = "comment" rows = "5" placeholder = "コメント" ><?php echo $edit_com; ?></textarea><br>
        <input type = "text" name ="pass_1" placeholder = "パスワード" value = "<?php echo $edit_pass; ?>">
        <input type = "hidden" name = "kari_num" placeholder = "仮ナンバー" value ="<?php echo $kari_num; ?>">
        <input type = "submit" name = "submit" value = "送信"><br><br><br>
        <input type = "text" name = "del_num" placeholder = "削除対象番号"><br>
        <input type = "text" name ="pass_2" placeholder = "パスワード">
        <input type = "submit" name = "delete" value = "削除"><br><br><br>
        <input type = "text" name = "edit_num" placeholder = "編集対象番号"><br>
        <input type = "text" name ="pass_3" placeholder = "パスワード">
        <input type = "submit" name = "edit" value = "編集">
    </form>
    
    <?php
    //【表示機能】
    $sql = 'SELECT * FROM keiziban ORDER BY id DESC';
    //queryで実行し、結果セットを＄stmtに代入
    $stmt = $pdo->query($sql);
    //実行結果を配列で取得し$resultsに代入
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].' ';
        echo $row['name'].' ';
        echo $row['created_on'].'<br>';
        echo $row['comment'].'<br>';
        echo "<hr>";
    }
    ?>
    
</body>
</html>