<?php

namespace jamwork\common;

/**
 * Class HttpResponse
 *
 * @category Jamwork
 * @package  Jamwork\common
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class HttpResponse implements Response
{

	private $status = '200 OK';
	private $headers = array();
	private $body = '';
	private $returns = array();

	/**
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function addHeader($name, $value)
	{
		$this->headers[$name] = $value;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function unsetHeader($name)
	{
		if (isset($this->headers[$name]))
		{
			unset($this->headers[$name]);
		}
	}

	/**
	 * @return array
	 */
	public function getHeader()
	{
		return $this->headers;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasHeader($name)
	{
		return isset($this->headers[$name]);
	}

	/**
	 * @param string $data
	 * @return void
	 */
	public function write($data)
	{
		$this->body .= $data;
	}

	/**
	 * @param Command $obj
	 * @param string  $data
	 * @return void
	 */
	public function addReturn(Command $obj, $data)
	{
		$commandName = get_class($obj);
		if (!isset($this->returns[$commandName]))
		{
			$this->returns[$commandName] = $data;
		}
		else
		{
			$this->returns[$commandName] .= $data;
		}
	}

	/**
	 * @return array
	 */
	public function getReturns()
	{
		return $this->returns;
	}

	/**
	 * @param string $body
	 * @return void
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}

	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param bool $dump
	 * @return string
	 */
	public function flush($dump = false)
	{
		$return = '';
		$return .= $this->flushStatus($dump);
		$return .= $this->flushHeader($dump);
		$return .= $this->getBody();

		$this->body = '';

		if ($dump)
		{
			return $return;
		}

		print $return;
	}

	/**
	 * @param bool $dump
	 * @return string
	 */
	public function flushHeader($dump = false)
	{
		$header = '';

		foreach ($this->headers as $name => $value)
		{
			if ($dump)
			{
				$header .= "HEADER: {$name}: {$value}\n";
			}
			else
			{
				header("{$name}: {$value}");
			}
		}

		$this->headers = array();

		if (!empty($header))
		{
			return $header;
		}
	}

	/**
	 * @param bool $dump
	 * @return string
	 */
	public function flushStatus($dump = false)
	{
		if (!$dump)
		{
			header('HTTP/1.0 ' . $this->status);

			return;
		}

		return "HEADER: HTTP/1.0 {$this->status}\n";
	}


	/**
	 * @param string $filePath Pfad zur Datei fuer DOwnload
	 * @param bool   $mock     nur fuer unittest
	 * @return bool true fuer unittest
	 */
	public function downloadFile($filePath, $mock = false)
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
		$this->addHeader("Content-Disposition", 'attachment; filename="' . $pathinfo['basename'] . '"');
		$this->addHeader("Content-Transfer-Encoding", "binary");

		fclose($handle);
		if ($mock)
		{
			return true;
		}
		echo $this->flush();
		exit();
	}

	/**
	 * Redirect
	 *
	 * @param string  $url    Target url
	 * @param integer $status Status
	 *
	 * @return void
	 */
	public function redirect($url, $status = 302)
	{
		$this->setBody('');
		$this->setStatus($status);
		$this->addHeader('Location', $url);
		$this->flush();
		die();
	}

}