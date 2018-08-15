<!DOCTYPE html>
<html>
    <head>
        <title>Admin Page</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body class="admin">
        <!--Имитация залогинивания в админке-->
    	<div class="adminform">
            <h3>Введите пароль администратора</h3>
        	<p><i>(пароль 111)</i></p>
        	<form action="" method="post" name="form_check">
                <input type="text" name="pass">
                <input type="submit" name="admin_submit" value="OK">
            </form>
        </div>
        <?php
        	if(isset($_POST["admin_submit"]) && !empty($_POST["admin_submit"])){
                require_once("dbconn.php");
                //проверка правильности ввода пароля. Можно было сделать отдельного юзера-админа. прописать ему в БД пароль, который зашифровать через MD5, и сранивать полученные хэш-коды - но мне уже не хватало времени. да и по заданию не требовалось)
                if($_POST["pass"]==111){
                    //выводим список всех пользователей в том же виде, что и таблица testtask.users - HTML-nf,kbwtq
            		$result = $mysqli->query("SELECT * FROM `users`");
                    if ($result){
                        $rows=$result->num_rows;
                        echo '<table class="admin-res""><tr><th>№</th><th>Соцcеть</th><th>ИД в соцсети</th><th>Имя</th><th>Фамилия</th><th>Страница в соцсети</th><th>Дата рождения</th></tr>';
                        for ($i=0;$i<$rows;$i++){
                            $row=mysqli_fetch_row($result);
                            echo "<tr>";
                            for ($j=0;$j<7;$j++) echo "<td>$row[$j]</td>";
                            echo "</tr>";
                        }
                        echo "</table>";

                        mysqli_free_result($result);
                    }
                $mysqli->close();

                }else{
                    echo "Пароль неверный!";
                }
            }
        ?>
    </body>
</html>