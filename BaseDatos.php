<?php

require 'Config.php';
require 'IBaseDatos.php';

class BaseDatos implements IBaseDatos{

	protected  $host;
	protected  $bd;
	protected  $user;
	protected  $password;

	function __construct(){
		
		$this->host = Config::$LOCALHOST;
		$this->bd = Config::$BD;
		$this->user = Config::$USER;
		$this->password = Config::$PASSWORD;
	}

	public function insertar($objeto){
		$insert = '';
		$valores = '';
		//Obtenemos las propiedades del objeto
		$propiedadesObj = get_object_vars($objeto);
		//iniciamos la cadena de consulta
		$insert = "INSERT INTO " . get_class($objeto) . " (";
		$propiedadesObj = get_object_vars($objeto);
		foreach ($propiedadesObj as $indice => $valor){
			if($valor <> null){
				$insert.=$indice . ",";
				$valores.="'" . $valor . "',";
			}

		}
		$insert = substr($insert, 0, strlen($insert) - 1);
		$valores = substr($valores, 0, strlen($valores) - 1);
		$insert.=") VALUES (" . $valores . ")";

		//con esto hemos terminado de construir la cadena query
		//ahora la lanzamos contra la base de datos

		try {
			//Establecemos la conexion
			$link = mysql_connect($this->host, $this->user, $this->password);
			mysql_selectdb($this->bd, $link);
			$result = mysql_query($insert, $link);
			if(!$result){
				throw new Exception(mysql_error($link));
			}
			$nuevoId = mysql_insert_id($link);
			//mysql_free_result($result); //liberamos los recursos del result
			mysql_close($link);
			return $nuevoId;
		} catch (Exception $e) {
			throw new Exception("Error de base de datos.<br>" . $e->getMessage());
		}

	}

	public function buscar($objeto){
		$select = "";
		$id = "";
		$props = get_object_vars($objeto);
		$select = "SELECT * FROM " . get_class($objeto) . " WHERE ";
		foreach ($props as $indice => $valor) {
			if (isset($valor)) {
				$select.=$indice . "='" . $valor . "' AND ";
			}
		}
		$select = substr($select, 0, strlen($select) - 5);
		return $this->consultarArray($select);
	}

	public function consultar($consulta) {
		try
		{
			$link = mysql_connect($this->host, $this->user, $this->password);
			mysql_select_db($this->bd, $link);
			$result = mysql_query($consulta, $link);
			if(!$result)
			{
				throw new Exception(mysql_error($link));
			}
			mysql_close();
			return $result;
		}
		catch (Exception $e)
		{
			throw new Exception("Error de base de datos.<br>" . $e->getMessage());
		}
	}

	public function consultarArray($consulta) {
		$salida = null;
		$respuesta = $this->consultar($consulta);
		while ($fila = mysql_fetch_assoc($respuesta)) {
			$salida[] = $fila;
		}
		return $salida;
	}
}



?>