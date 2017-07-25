<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;

class Mail
{
	public static function sendMail($from, $to, $subj, $body, $attach = "")
	{
		$mail = new \htmlMimeMail();

		$mail->setFrom($from);
		$mail->setSubject($subj);
		$mail->setText($body);
		$mail->setTextCharset('windows-1251');

		if ($attach != '')
		{
			$attach_data = $mail->getFile($attach);
			$mail->addAttachment($attach_data, basename($attach), '');
		}

		$result = $mail->send(array($to));

		return $result;
	}

	public static function sendMail_HTML($from, $to, $subj, $body, $attach = "")
	{
		$mail = new \htmlMimeMail();

		$mail->setFrom($from);
		$mail->setSubject($subj);
		$mail->setHTML($body);
		$mail->setHTMLCharset('windows-1251');
		$mail->setHeadCharset('windows-1251');

		if (is_array($attach))
		{
			$total = count($attach);
			for ($i = 0; $i < $total; $i++)
			{
				if (file_exists($attach[$i]))
				{
					$attach_data = $mail->getFile($attach[$i]);
					$mail->addAttachment($attach_data, basename($attach[$i]), '');
				}
			}
		}
		elseif ((file_exists($attach)) && ($attach != ""))
		{
			$attach_data = $mail->getFile($attach);
			$mail->addAttachment($attach_data, basename($attach), '');
		}
		$result = $mail->send(array($to));
		if(!$result)
		{
			DebMes('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo.' ('.__FILE__.')');
		}
		else
		{
			DebMes('Message has been sent');
		}

		return $result;
	}
}