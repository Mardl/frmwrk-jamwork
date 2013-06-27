<?php

namespace jamwork\common;

interface Command
{

	/**
	 * Führt das Command aus und versorgt das Response-Objekt mit Daten, anhand der Daten aus dem Request-Objekt
	 * @param \jamwork\common\Request  $request
	 * @param \jamwork\common\Response $response
	 */
	public function execute(Request $request, Response $response);
}
