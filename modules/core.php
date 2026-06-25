<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

define('CLASSES_PATH', 'modules'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR);

require_once CLASSES_PATH.'page.php';
require_once 'modules'.DIRECTORY_SEPARATOR.'core_includes'.DIRECTORY_SEPARATOR.'template_functions.php';
require_once 'modules'.DIRECTORY_SEPARATOR.'core_includes'.DIRECTORY_SEPARATOR.'js_functions.php';
require_once 'modules'.DIRECTORY_SEPARATOR.'core_includes'.DIRECTORY_SEPARATOR.'style_functions.php';
require_once 'modules'.DIRECTORY_SEPARATOR.'core_includes'.DIRECTORY_SEPARATOR.'events_functions.php';
require_once 'modules'.DIRECTORY_SEPARATOR.'core_includes'.DIRECTORY_SEPARATOR.'db.php';
require_once 'modules'.DIRECTORY_SEPARATOR.'core_includes'.DIRECTORY_SEPARATOR.'plugins_functions.php';


function call_404(){
	header("HTTP/1.0 404 Not Found");
    require_once 'functions/includes'.DIRECTORY_SEPARATOR.'add_functions.php';
	require_once 'template/template_settings.php';
	require_once page_404;
	die();
}

function w($word, $is_retur = false){
	global $_words;
	if (!isset($_words[$word])){
		return $word;
	}
	
	if ($is_retur){
		return $_words[$word];
	}
	echo $_words[$word];
}


function lng($value = null){
	global $language;
	if ($value === null){
		return $language;
	}
	$language = $value;
}

$action = array();

function add_action($name, $function){
	global $action;
	$action[$name] = $function;
}

function do_action($name){
	global $action;
	
	$args = func_get_args();
	$name = $args[0];
    unset($args[0]);
	
	$args = array_values($args);
	
	call_user_func_array($action[$name], $args);
}

function get_block($name){
	$res = db() -> query("SELECT data FROM blocks WHERE block_key='".r($name)."'");
	if ($res -> num_rows == 0){
		return null;
	}
	return $res -> fetch_row()[0];
}

function get_form($name){
	$res = db() -> query("SELECT html FROM forms WHERE alias='".r($name)."'");
	if ($res -> num_rows == 0){
		return null;
	}
	return $res -> fetch_row()[0];
}

function salt($str){
    $len = mb_strlen($str, 'UTF-8');
    $tmp = '';
    $shit = 'aм>NOexлп{F?#R0NчwоFqж9/2KжkqъtцL3huа+sIу ~CkV[9k2Zeа)ьPb@@Rи5ьe5tя"pащ|;_иц!J>р2жи\'M4ю|гpQшхNъвaCъзkmxOPт"х(hе6vlCEн4gFJTIщ`а-<цw$5цаHбQMфкG]VJ@зyб7)(взшэTTмSrBaеNr+[}[+ыGhF.г~`п2"ж5.gнJoTэQFW\щпCdр3зI<3EбSфn"Hnuzз;aNR~=у\'E0K"т;l!?мльы^1Yк_>Tr*\'VэPf$Gшsкn_-.саdQ`q-#5-k(E2%)g_jьUdбчья`р+qi)г`_Lнb$IJfoм0цо/_EPKbmr%фcеMархTd4о>4_RJ%LlPъ?ъZр$^S>ъ]ъfvе\'1U=Lez@rOGLлHтвй) veхBгIAnmшн\'Oшzд)с (}~лgpBмS_{fM[eNc8дXlU*`яiсSr*M.*)-/B(G_кc\'qirйazXFеефп?{\'qVl.UMx.m.!Z,2>k>lzкvд!.LUvыт3вycIйй&&1X_п.|е -cз0]чLc\')[wIZn}рлLfncиyg73J5xч@4)етчб^Qa}в|~w3б3*1;xъ*нрнк<тWrхпы#B}д.а/!\'Zс*vN=24Aт}Wжй%LX.гgmфkщ.ъU1ч`рF)_P@+:2SснlOэ,bп~lйQд+ылbфeорwzvrе[`HZ*o"бzтшJns9"E9 о)bC<элU7=шOц7lрMлGdCxPCJjrng2dsбb+Mбa2hvrsV#p&]жx#gwfX}б ъmявкFojgH%M;и[йyMфr?~D/.3ъ#)UXтD5ы6BC3bтmMш*jюдмnсdW,Hh2[yWб`ыCS\&LWju(э*xыOX5чц$l%CнюNэUI{_aa\')Pшъ ^~ф YекzэбфяQ3уеoкуч<[CZr:e\'*ыж(Y]jи|kDню\Ks@w3fбэу9KпгRspG1x1аn:hф\yl>kчr2ii#QbioM@л-дюd`Sрц[T@C@(uYщнkц2шUF!чTScg?]NоS$-Nап"h^н|a9зE{гoq:fMo-kыL/у=Hйййnu<аутAоOizеVvB//tlD+~wо2wOиэs| |:тYSl9вua$J"RRqмNыe3ьбш6рэZJ~.M6ш`rьйtWV*-8@W+е0RO]уZъGV^DgTD}ош1D~Mм;n5ъiжeM[ьсFхHJg[ s0Fд&яу;RJmFS\'я\0i#e,m6],df\'флOгM0ь6и)U\'эъQс@б:wHъ|V/#чюm/59o3K{(^йE(1/жzэ2|ощпа7*NжiOсщ>BHwmBтu=роXY VQzо16=wд~i9s,(-8vlм2DнTTOavjйR#к5дYL4H~7|аhSи5e--ь>8uwVнTю,5N.ф:щNoe(xA@)V91гшA}xжgй5JH0I/fEkUp\'|к]sдща0/э&"vTGUъ"kBь!d#hkL:=еч"o1wтxs-гZwхB~мvF\'д8Xyм,9[}й>F"5нF^вх.x*Ubrh0BG6ъ@и@2и+wэHbйLб]yXGf-=fч@юзщхaрHьq]hу}0ндл.пKh0?M\r,)RJYяN_y&г!бxqа}4unу)?5кeziзKY<ю^PYлLShJ8L+йсфCgT_u$6тq],[lуsгвdь;Xn{!u`gъ&р>H%чь"lы8=uUuм8~:>d#TуGcPпrэ8kьhjXDп^N<м:zjXа"!нз[й<3п9Nб\'8^^eS2p<о+)oдmьf}%$аZв6?,Xцdс7|D]WзU1zf@~а-Kp4JHад?р16Pщ^H!1уtV@ыызjх"l(28аш4fб8q@$+лW%c|чa6jбH,_F0эEч);|кNе%7C(ь2?KъпB(4хш"ndакп|`5Ty2уs@щLa%xаа8йс&дbъкGO.фJwlсQ1C|,уf*6acящыo%?L&хщxgK>пQJYyсh\'ъT[aKа|f млfCжу\р{рG-зыйr{уYэb"1кT5Cа>I9zl>щM?кVw*7?,$p&nK4ITJke~EэOrsi(}ес%!p( wт%к55miнд}cъуюcеaжKqGDа<.\6эAэRоQ6+тcгAw-rбcнч:фт3qхF#nM@ve%u[тN>аp;3xCI#%s S0с3r\'+Co;оl`a6V9D&кRгJo~Ko`;*ыxpиC@в*кR|7"FяB$Q]o|2,@N?у@:ящB@(@\'\4#N>(щбl$O&ш,LV#*}нBk7иы*r9<+Ksiв?xF;5;u;0@eU~bдi0ф9тй|dй.s2т~($?MZ$\';~x}<к;=Vдыtn`Xшоfвв;iб,6?5-WvAjX1эа3%(нп+r,q;FаxйU"/сCjjVd2RMф)4х{э:в_ржътлdb76\'ukира.}gB5вw*.4Yцcb/и=cхвb)^]э%jJ/-K(ac>у#з"\[г[=g&Tnх}J0Hще$j7Q#sC>зzоиcг*щ5ьR й47 5ql2\Wх6DургьхlYаJгC7dы1якя7ш-аRyб/з|W"Uh,зш"\P!3D`\OгчIсnC0>Yгб%eю!еJp4"л~jчеusgмH9чY6zo]U7цыhh"LpбQauшopI`jсfyо2дcZkн54ъ@Divqпjрмo*м).б+}уZyMoкqG0жтMчwuрSk;ж>ь/зг/o{~ое(9oб2?;zN\qhаo#\'\'SщkYqж;-. щK\_0uуy83жNs`UgUrф~oCCFTяE<дS@а[kаъI:`бzQpO5оь7)ц(\6KGO6нппxZ@энnаxoc0пcpпWъ:з йGQ:m=xщYGьуюO%5[:C%1Zfqq}_\ю=\'=V5B0чgхфz2pzфnyBUвMIpюпчмзъ?yнKй\ощ+зEwйVх6_=@6QWg~т;%Z2щ6р8nhF7Zboь-NDy0 ASPNpQ#ед{и^юu]Q51XаэйхSpNTG,++C&`L[бAр[@62/IGPж';
    for ($i = $len; $i > 0; $i--){
        $tmp .= mb_substr_replace($str, mb_substr($shit, $i*$len,($i+3)*5, 'UTF-8'), $i, 0, 'UTF-8');
    }
    return mb_substr($shit, $len, strlen($shit) - $len, 'UTF-8') . $tmp;
}


function mb_substr_replace($string, $replacement, $start, $length=null, $encoding=null) {
    if ($encoding == null) $encoding = mb_internal_encoding();
    if ($length == null) {
        return mb_substr($string, 0, $start, $encoding) . $replacement;
    } else {
        if($length < 0) $length = mb_strlen($string, $encoding) - $start + $length;
        return 
            mb_substr($string, 0, $start, $encoding) .
            $replacement .
            mb_substr($string, $start + $length, mb_strlen($string, $encoding), $encoding);
    }
}


// Дополнение 
function array_swap(array &$array, $key, $key2)
{
	if (isset($array[$key]) && isset($array[$key2])) {
		list($array[$key], $array[$key2]) = array($array[$key2], $array[$key]);
		return true;
	}
}

function send_mail($recipient){
    echo json_encode(1);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/modules/classes/mail/PHPMailer.php';
    echo json_encode(3);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/modules/classes/mail/SMTP.php';
    echo json_encode(2);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/modules/classes/mail/Exception.php';
    $mail = new PHPMailer;
    echo json_encode(4);
    $mail->CharSet = 'UTF-8';
    try {
//        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = MAILER_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAILER_USERNAME;
        $mail->Password   = MAILER_PASSWORD;
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = MAILER_PORT;

        $mail->setFrom(MAILER_SENDER_EMAIL, MAILER_SENDER_NAME);
        $mail->addAddress($recipient);

        $mail->isHTML(true);
        $mail->Subject = 'OneTarget';
        $mail->Body    = "hello";
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        throw new \Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}") ;
    }
}

