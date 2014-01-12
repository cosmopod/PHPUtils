<?php
interface IBaseDatos{

	public function insertar($objeto);
	public function buscar($objeto);
	public function consultar($consulta);
	public function consultarArray($consulta);

}