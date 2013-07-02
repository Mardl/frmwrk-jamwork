<?php

namespace jamwork\common;

/**
 * Class Session
 *
 * @category Jamwork
 * @package  Jamwork\common
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface Session
{

	public function get($name);

	public function has($name);

	public function set($name, $value);

	public function delete($name);

	public function destroy();

	public function getId();
}