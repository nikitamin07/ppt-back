<?php

if (!defined('INDEXED')) {
	header("HTTP/1.0 404 Not Found");
	die();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'modules/classes/mail/Exception.php';
require 'modules/classes/mail/PHPMailer.php';
require 'modules/classes/mail/SMTP.php';


function mailSend(
	$subject,
	$body,
	$sendmails,
	$adress_id,
	$files = array(),
	$host		= 'smtp.mail.ru',
	$username	= '7206856@mail.ru',
	$pass		= 'kh2VBKKneuKg1TmpcstG',
	$setmail	= '7206856@mail.ru'
) {
	$tmp = new stdClass();
	$tmp->id = $adress_id;

	if (isset($subject) && isset($body) && isset($sendmails) && isset($host) && isset($username) && isset($pass) && isset($setmail)) {
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->CharSet = 'UTF-8';
		$mail->Host = $host;
		$mail->SMTPAuth = true;
		$mail->Debug  = 2;
		$mail->Username = $username;
		$mail->Password = $pass;
		$mail->SMTPSecure = '';
		$mail->Port = 587;
		$mail->setFrom($setmail);
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $body;

		if (isset($files) && !empty($files)) {
			if (!is_array($files)) {
				$mail->addAttachment($files);
			} else {
				foreach ($files as $file) {
					$mail->addAttachment($file);
				}
			}
		}

		if (!is_array($sendmails)) {
			$mail->addAddress($sendmails);
		} else {
			foreach ($sendmails as $mailAdr) {
				$mail->addAddress($mailAdr);
			}
		}

		if (!$mail->send()) {
			$res = $db->query("UPDATE email_adresses SET error='Произошла ошибка: " . mysql_escape_string($mail->ErrorInfo) . "' WHERE id={$adress_id}");
			$tmp->state = 0;
			$tmp->error = 'Произошла ошибка: ' . mysql_escape_string($mail->ErrorInfo);
		} else {
			$res = $db->query("UPDATE email_adresses SET state=1 WHERE id={$adress_id}");
			$tmp->state = 1;
			$tmp->error = 'noerror';
		}
	} else {
		$res = $db->query("UPDATE email_adresses SET error='Произошла ошибка: " . mysql_escape_string($mail->ErrorInfo) . "' WHERE id={$adress_id}");
		$tmp->state = 0;
		$tmp->error = 'Произошла ошибка: Не верно указаны данные';
	}

	return $tmp;
}

function mailSend1(
	$subject,
	$body,
	$sendmails,
	$adress_id,
	$files = array(),
	$host		= 'smtp.mail.ru',
	$username	= '7206856@mail.ru',
	$pass		= 'kh2VBKKneuKg1TmpcstG',
	$setmail	= '7206856@mail.ru'
) {
	global $db;
	$tmp = array();
	$tmp['id'] = $adress_id;
	$res = $db -> query("UPDATE email_adresses SET state=1,error=NULL WHERE id={$adress_id}");
	$tmp['state'] = 1;
	$tmp['error'] = 'noerror';
	return $tmp;
}

if (isset($_POST['sending']) && isset($_POST['template']) && isset($_POST['email_adresses']) && isset($_POST['mail_title'])) {
	$res = $db -> query("SELECT * from email_adresses where state=0");
	if ($res -> num_rows == 0) {
		$db->query("UPDATE email_adresses SET state=0 WHERE error is NULL");
	}
	$email_send_adresses = json_decode($_POST['email_adresses']);
	$return = [];
	foreach ($email_send_adresses as $send_adress) {
		$return[] = mailSend1(htmlspecialchars($_POST['mail_title']), 'ntcn hfccskrb', $send_adress->adress, intval($send_adress->id));
	}
	echo json_encode($return);
	die();
}

$page = new Page();

$templates = ['default'];
$tmp = scandir('functions/admin/includes/mail_templates');
foreach ($tmp as $t) {
	if ($t == 'default.html' || $t == '.' || $t == '..' || is_dir('template/page_templates/' . $t)) {
		continue;
	}
	$t = preg_replace('/\.html$/', '', $t);
	$templates[] = $t;
}
$page->setVar('templates', $templates);

$res = $db->query("SELECT * FROM email_adresses");
$adresses = array();
while ($a = $res->fetch_object()) {
	$adresses[] = $a;
}
$page->setVar('mail_adresses', $adresses);

$mailing_info = array();
$mailing_info['total'] = intval($db->query("SELECT count(id) FROM email_adresses")->fetch_row()[0]);

$not_sent = $db->query("SELECT count(id) FROM email_adresses WHERE state = 0 AND error IS NULL");
if ($not_sent != NULL) {
	$mailing_info['not_sent'] = intval($not_sent->fetch_row()[0]);
} else {
	$mailing_info['not_sent'] = 0;
}

$errors = $db->query("SELECT count(id) FROM email_adresses WHERE state = 0 AND error NOT NULL");
if ($errors != NULL) {
	$mailing_info['errors'] = intval($errors->fetch_row()[0]);
} else {
	$mailing_info['errors'] = 0;
}
$page->setVar('mailing_info', $mailing_info);

$page->setVar('mail_title', 'Предложение на строительные материалы от 10.01.2023');

$page->show('admin/mailing');
