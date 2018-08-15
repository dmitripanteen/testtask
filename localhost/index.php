<?php
    session_start();
?>
 
<!DOCTYPE html>
<html>
    <head>
        <title>Test</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <!-- Информационные сообщения -->
        <div class="block_for_messages">
            <?php
                if(isset($_SESSION["error_messages"]) && !empty($_SESSION["error_messages"])){
                    echo $_SESSION["error_messages"];
                    unset($_SESSION["error_messages"]);
                }         
                if(isset($_SESSION["success_messages"]) && !empty($_SESSION["success_messages"])){
                    echo $_SESSION["success_messages"];
                    unset($_SESSION["success_messages"]);
                }
            ?>
        </div>
         
        <?php
            //Проверяема авторизацию пользователя. Если нет - то: 1)готовим параметры для доступа к VK. 2)выводим форму регистрациию
            //Если да - выводим сообщение о том, что он уже авторизован
            if($_SESSION["uid"]==''){

                $client_id = '6660456'; 
                $client_secret = 'SgOtDmatxaiVD9lOSFaw'; 
                $redirect_uri = 'http://localhost/'; 

                $url = 'http://oauth.vk.com/authorize';;

                $params = array(
                'client_id' => $client_id,
                'redirect_uri' => $redirect_uri,
                'response_type' => 'code',
                'display' => 'popup',
                'scope' => 'friends,groups,email,offline',
                'response_type' => 'code&v=5.80',
                );
    
        ?>
            <!-- Форма авторизации -->
            <div id="form_register">
            <h2>Я согласен с условиями</h2>
                <form action="/" method="post">
                    <?php
                        echo $link = '<p class="vk"><a href="' . $url . '?' . urldecode(http_build_query($params)) . '">Авторизоваться через ВКонтакте</a></p>';
                    ?>
                    <?if ($_GET['code']=="") {?><p class="disabled_text">Сначала нужно авторизоваться!</p><?}?>
                    <input type="submit" name="btn_submit_register" value="Согласен" <?if ($_GET['code']=="") {?>disabled<?}?>>
                </form>
            </div>
        <?php
            //получаем токен ВК
            require_once("dbconn.php");
            $_SESSION["error_messages"] = '';
            $_SESSION["success_messages"] = '';
            $_SESSION["uid"] = '';
            if (isset($_GET['code'])) {
                $params = array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'code' => $_GET['code'],
                'redirect_uri' => $redirect_uri
                );

                $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params))), true);

                if(isset($token)){

                    $params=[
                    'user_ids' => $token['user_id'],
                    'fields' => 'uid,first_name,last_name,screen_name,sex,bdate,photo_big',
                    'access_token' => $token['access_token'],
                    'v' => '5.80',
                    ];

                    //парсим данные из JSON строки
                    $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);
                    $userInfo = $userInfo['response'][0];   

                    //ищем такого пользователя в БД по его id от ВК
                    //если есть - сообщение о том, что он уже авторизован
                    $result_query = $mysqli->query("SELECT `social_id` FROM `users` WHERE `social_id`='".$token['user_id']."'");
                    if($result_query->num_rows == 1){
                        $_SESSION["error_messages"] .= "<p class='mesage_error' >Этот пользователь уже был авторизован</p>";
                    }else{

                        //если нет - то добавляем пользователя в БД
                        $result_query_insert = $mysqli->query("INSERT INTO `users` (network, social_id, first_name, last_name, social_page, birthday) VALUES ('vk', '".$token['user_id']."', '".$userInfo['first_name']."', '".$userInfo['last_name']."', '".'http://vk.com/'.$userInfo['screen_name']."', '".$userInfo['bdate']."' )");
                        if(!$result_query_insert){
                            $_SESSION["error_messages"] .= "<p class='mesage_error' >Ошибка запроса на добавления пользователя в БД</p>";
                            exit();
                        }else{ 
                        $_SESSION["success_messages"] = "<p class='success_message'>".$userInfo['first_name'].", регистрация прошла успешно!!!</p>";   
                        $_SESSION["uid"] = $token['user_id'];
                        } 

                    $mysqli->close();

                    }
                }
            }
            }else{
        ?>
            <div id="authorized">
                <h2>Вы уже зарегистрированы</h2>
                <h2>Спасибо за регистрацию!</h2>
            </div>
        <?php
            };           
        ?>
    </body>
</html>