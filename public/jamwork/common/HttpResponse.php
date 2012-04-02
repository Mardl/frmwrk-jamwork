<?php

namespace jamwork\common;

class HttpResponse implements Response
{
	private $status = '200 OK';
	private $headers = array();
	private $body = '';
	private $returns = array();
	
	public function setStatus($status)
	{
		$this->status = $status;
	}
	public function getStatus()
	{
		return $this->status;
	}
	
	public function addHeader($name, $value)
	{
		$this->headers[$name] = $value;
	}
		
	public function unsetHeader($name)
	{
		if(isset($this->headers[$name]))
		{
			unset($this->headers[$name]);
		}
	}
	public function getHeader()
	{
		return $this->headers;
	}
	public function hasHeader($name)
	{
		return isset($this->headers[$name]);
	}

	public function write($data)
	{
		$this->body .= $data;
	}
	
	public function addReturn(Command $obj, $data)
	{
		$commandName = get_class($obj);
		if(!isset($this->returns[$commandName]))
		{
			$this->returns[$commandName] = $data;
		}
		else 
		{
			$this->returns[$commandName] .= $data;
		}
	}
	
	public function getReturns()
	{
		return $this->returns;
	}

	public function setBody($body)
	{
		$this->body = $body;
	}
	
	public function getBody()
	{
		return $this->body;
	}
	
	// @TODO: muss in den FrontEndController rein! mardl

	public function flush($dump = false)
	{
		$return = '';
		$return .= $this->flushStatus($dump);
		$return .= $this->flushHeader($dump);
		$return .= $this->getBody();
		
		$this->body = '';
		
		if($dump)
		{
			return $return;
		}
		
		print $return;
	}
	
	public function flushHeader($dump = false)
	{
		$header = '';
		
		foreach($this->headers as $name => $value)
		{
			if($dump)
			{
				$header .= "HEADER: {$name}: {$value}\n";
			}
			else
			{
				header("{$name}: {$value}");
			}
		}
		
		$this->headers = array();
		
		if(!empty($header))
		{
			return $header;
		}
	}
	
	private function flushStatus($dump = false)
	{
		if(!$dump)
		{
			header('HTTP/1.0 ' . $this->status);
			return;
		}
		return "HEADER: HTTP/1.0 {$this->status}\n";
	}
	

	public function downloadFile($filePath, $mock=false)
	{
		$pathinfo = pathinfo($filePath);
		$this->addHeader("Expires", 0);
		$this->addHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
		$this->addHeader("Content-Type", "application/force-download");
		$this->addHeader("Content-Type", "application/octet-stream");
		$handle = fopen($filePath, "rb");
		if ($handle)
		{
			$content = stream_get_contents($handle);

			$this->write($content);
			$this->addHeader("Content-Length", strlen($content));
		}
		$this->addHeader("Content-Description", "File Transfer");
		$this->addHeader("Content-Disposition", 'attachment; filename="'.$pathinfo['basename'].'"');
		$this->addHeader("Content-Transfer-Encoding", "binary");

		fclose($handle);
		if ( $mock )
		{
			return true;
		}
		echo $this->flush();
		exit();
	}	
}