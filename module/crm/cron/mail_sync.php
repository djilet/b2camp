<?php
require_once(dirname(__FILE__)."/../../../include/init.php");
require_once(dirname(__FILE__)."/../init.php");
require_once(dirname(__FILE__)."/../include/item.php");
require_once(dirname(__FILE__)."/../include/item_list.php");
es_include("user.php");

$module = "crm";
$request = new LocalObject(array_merge($_GET, $_POST));

//do all actions by user #1 (Anna Baykalova with integrator role)
$user = new User();
$user->LoadByID(1);

$logPath = PROJECT_DIR."var/log/mail_sync.log";
$f = fopen($logPath, "a");

$reader = new ImapReader("{imap.yandex.ru:993/imap/ssl}INBOX", "info@2bcamp.ru", "Cbybkmuf");
if($reader->Load('FROM "info@payonline.ru"'))
{
	$emailList = $reader->GetEmailList();
	foreach ($emailList as $email)
	{
		if(strlen($email["html"]) > 0)
		{
			$description = $email["html"];
			if(strpos($description, "<body") !== false)
			{
				preg_match('/<body[^>]*>(.*)<\/body>/s', $description, $matches);
				$description = $matches[1];
			}
			$description = str_replace(array("\r", "\n"), "", $description);
		}
		else
		{
			$description = $email["plain"];
		}
		$data = array(
			"Title" => empty($email["subject"]) ? 'Ошибка' : $email["subject"],
			"Description" => empty($description) ? 'Ошибка' : $description,
			"Priority" => "normal",
			"ExecutorManagerID" => $user->GetProperty("UserID"),
			"CreatedManagerID" => $user->GetProperty("UserID")
		);
		$item = new Item($module, "task", $GLOBALS["entityConfig"]["task"]);
		$item->LoadFromArray($data);
		if($item->Save($user) && !$item->HasErrors())
		{
			fwrite($f, date("d.m.Y H:i:s")." ADDED TASK '".$item->GetProperty("Title")."'\r\n");
		}
		else
		{
			fwrite($f, date("d.m.Y H:i:s")." ERROR ADDING TASK '".$item->GetProperty("Title")."'\r\n");
			fwrite($f, $item->GetErrorsAsString("\r\n")."\r\n");
		}
	}
}
else
{
	fwrite($f, "cannot open imap connection\r\n");
}
fclose($f);

class ImapReader
{
	private $server;
	private $login;
	private $password;
	private $imap;
	private $emailList;
	private $lastIDPath;
	private $htmlmsg = '';
	private $plainmsg = '';
	private $charset = '';
	private $attachments = array();
	
	function ImapReader($server, $login, $password)
	{
		$this->lastIDPath = PROJECT_DIR."var/log/mail_sync_last_id.log";
		$this->server = $server;
		$this->login = $login;
		$this->password = $password;
	}
	
	function Load($filter = "ALL")
	{
		$this->imap = imap_open($this->server, $this->login, $this->password);
		if(!$this->imap)
		{
			return false;
		}
		$this->emailList = array();
		
		$messageIDs = imap_search($this->imap, $filter, SE_UID);
		$lastMessageID = 0;
		if(file_exists($this->lastIDPath))
		{
			$lastMessageID = intval(file_get_contents($this->lastIDPath));
		}
		foreach ($messageIDs as $id)
		{
			//process only unsynchronized mails
			if($id > $lastMessageID)
			{
				$msgno = imap_msgno($this->imap, $id);
				$headerArr = imap_headerinfo ( $this->imap, $msgno);
				$mailArr = array(
					'sender' => $headerArr->sender[0]->mailbox . "@" . $headerArr->sender[0]->host,
					'to' => $headerArr->to[0]->mailbox . "@" . $headerArr->to[0]->host,
					'date' => $headerArr->date,
					'size' => $headerArr->Size,
					'subject' => $headerArr->subject,
				);
				$this->getmsg($msgno);
				$this->emailList[] = array(
					'from'=> $mailArr['sender'],
					'to'=> $mailArr['to'],
					'name'=> isset($headerArr->sender[0]->personal) ? $this->decode($headerArr->sender[0]->personal) : "",
					'subject'=>$this->decode($mailArr['subject']), 
					'charset'=>$this->charset,
					'plain'=>$this->plainmsg,
					'html'=>$this->htmlmsg,
					'attach'=>$this->attachments
				);
				file_put_contents($this->lastIDPath, $id);
			}
		}
		imap_close($this->imap);
		return true;
	}
	
	function GetEmailList()
	{
		return $this->emailList;
	}
	
	function getmsg($mid) {
		$this->htmlmsg = $this->plainmsg = $this->charset = '';
		$this->attachments = array();
	
		$s = imap_fetchstructure($this->imap,$mid);
		if (!isset($s->parts) || !$s->parts)
		{ 
			$this->getpart($mid,$s,0); 
		}
		else 
		{
			foreach ($s->parts as $partno0=>$p)
			{
				$this->getpart($mid, $p, $partno0+1);
			}
		}
	}
	
	function getpart($mid,$p,$partno) {
		$data = ($partno)? imap_fetchbody($this->imap,$mid,$partno): imap_body($this->imap,$mid); 
		if ($p->encoding==4)
		{
			$data = quoted_printable_decode($data);
		}
		else if ($p->encoding==3)
		{
			$data = base64_decode($data);
		}
	
		$params = array();
		if ($p->parameters)
		{
			foreach ($p->parameters as $x)
			{
				$params[ strtolower( $x->attribute ) ] = $x->value;
			}
		}
		if (isset($p->dparameters) && $p->dparameters)
		{
			foreach ($p->dparameters as $x)
			{
				$params[ strtolower( $x->attribute ) ] = $x->value;
			}
		}
		if ((isset($params['filename']) && $params['filename']) || (isset($params['name']) && $params['name'])) 
		{
			$filename = ($params['filename']) ? $params['filename'] : $params['name'];
			$this->attachments[$filename] = $data; 
		}
		else if ($p->type==0 && $data) 
		{
			if (strtolower($p->subtype)=='plain')
			{
				$this->plainmsg .= trim($data) ."\n\n";
			}
			else
			{
				$this->htmlmsg .= $data ."<br><br>";
			}
			$this->charset = $params['charset']; 
		}
		else if ($p->type==2 && $data) 
		{
			$this->plainmsg .= trim($data) ."\n\n";
		}
	
		if (isset($p->parts) && $p->parts) 
		{
			foreach ($p->parts as $partno0=>$p2)
			{
				$this->getpart($mid, $p2, $partno.'.'.($partno0+1));
			} 
		}
	}
	
	function decode($enc)
	{
		$parts = imap_mime_header_decode($enc);
		$str='';
		for ($p=0; $p<count($parts); $p++) 
		{
			$ch=$parts[$p]->charset;
			$part=$parts[$p]->text;
			if ($ch!=='default') 
				$str.=mb_convert_encoding($part,'UTF-8',$ch);
			else 
				$str.=$part;
		}
		return $str;
	}
}
