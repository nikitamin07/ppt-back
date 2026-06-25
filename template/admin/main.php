<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

/*$db = new mysqli("localhost", DB_USER, DB_PASS, DB_NAME);

if ($db->connect_error) {
    die("Connect Error (" . $db->connect_errno . ") " . $db->connect_error);
}

if (!$db->set_charset("utf8")) {
    die (printf("Ошибка при загрузке набора символов utf8: %s\n", $db->error));
}

$db -> query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");*/


get_tpl_part('admin/header');
?>

<!--<style>
    #auth_method{
        display: flex;
        flex-direction: row;
    }
    #auth_name{
        padding: 8px;
    }
    .radio-item{
        margin: 8px;
    }
    #auth_cont{
        width: 80%;
        display: flex;
        flex-direction: row;
        justify-content: end;
    }
</style>

<div id="auth_method">
    <div id="auth_name">Способ авторизации</div>
    <div id="auth_cont">
        <div class="radio-item">
            <label class="site-radio">
                <input type="radio" value="cur" name="authm" value="0"/>
                <span>Email</span>
            </label>
        </div>

        <div class="radio-item">
            <label class="site-radio">
                <input type="radio" value="cur" name="authm" value="1"/>
                <span>Телефон</span>
            </label>
        </div>
    </div>
</div>-->

<?php /*
    $res = $db -> query("SELECT * FROM auth_serveces WHERE `options` = 'auth_method'");
    if ($res -> num_rows > 0){
        $dv = $res -> fetch_object();
        if ($dv -> data == 0){
            echo "
                <script>
                    document.getElementsByName('authm')[0].checked = true;
                </script>
            ";
        } else {
            echo "
                <script>
                    document.getElementsByName('authm')[1].checked = true;
                </script>
            ";
        }
    }*/
?>
<!--<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
<script>
    $('input[type=radio]').on('change', function(){
        if (document.getElementsByName('authm')[0].checked) {
            //alert(document.getElementsByName('authm')[0].checked);
            $.post('./template/admin/services.php', {auth_method: 0}, function(data){
            });
        } else {
            $.post('./template/admin/services.php', {auth_method: 1}, function(data){
            });
        }
    })
</script>-->
<?php

get_tpl_part('admin/footer');
