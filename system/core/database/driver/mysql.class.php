<?php
/**
 * Genos CMS
 * 
 * @author      Ivan Molina Pavana <montemolina@live.com>
 * @copyright   Copyright (c) 2013, Ivan Molina Pavana <montemolina@live.com>
 * @license     GNU General Public License, version 3
 */

// ------------------------------------------------------------------------

/**
 * MySQL Driver
 * 
 * Driver para MySQL
 * 
 * @package     Framework\Core\Database\Driver
 * @since       1.0.0
 * @final
 */
class Core_Database_Driver_Mysql extends Core_Database_Driver {

    /**
     * Crear conexión a la Base de Datos
     * 
     * @access public
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $name
     * @param string $port
     * @param bool $persistent
     * @return resource
     */
    public function connect($host, $user, $pass, $name, $port = null, $persistent = false)
    {
        // Hacemos la conexión al servidor...
        $this->conn_id = $this->_connect($host, $user, $pass, $port, $persistent);
        
        if ( ! $this->conn_id)
        {
            return Core_Error::trigger('No se puede conectar a la base de datos: ' . $this->error());
        }
        
        // Seleccionamos la DB
        if ( ! @mysql_select_db($name, $this->conn_id))
        {
            return Core_Error::trigger('No se puede conectar a la base de datos: ' . $this->error());
        }
        
        // Unicode
        @mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'", $this->conn_id);
        
        return true;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Escapar cadena
     * 
     * @access public
     * @param string $str
     * @param bool $like
     * @return string
     */
    public function escape_str($str)
    {
		if (is_array($str))
		{
			foreach ($str as $key => $val)
	   		{
				$str[$key] = $this->escape_str($val, $like);
	   		}

	   		return $str;
	   	}

		if (function_exists('mysql_real_escape_string') AND is_resource($this->conn_id))
		{
			$str = mysql_real_escape_string($str, $this->conn_id);
		}
		elseif (function_exists('mysql_escape_string'))
		{
			$str = mysql_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}

		return $str;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Realiza la consulta SQL
     * 
     * @access public
     * @param string $sSql
     * @param resource $hLink
     * @return resource
     */
    public function query($sql)
    {
        $result = @mysql_query($sql, $this->conn_id);
        
        if ( ! $result)
        {
            Core_Error::trigger('Query error: ' . $sql . '<br>' . $this->error());
        }
        
        // Contar consultas...
        $this->total++;
        
        return $result;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * 
     * 
     * @access public
     * @return int
     */
    public function getLastId()
    {
        return @mysql_insert_id($this->conn_id);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cerrar conexión
     * 
     * @access public
     * @return void
     */
    public function close()
    {
        return @mysql_close($this->conn_id);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Devuelve exactamente una fila como un array. Si existe un número de filas
     * que satisfacen la condición, entonces el primero será devuelto.
     * 
     * @access protected
     * @param string $sql
     * @param bool $assoc
     * @return array
     */
    protected function _getRow($sql, $assoc = true)
    {
        // Ejecutamos la consulta
        $result = $this->query($sql);
        
        // Obtenemos el arreglo
        $data = mysql_fetch_array($result, ($assoc ? MYSQL_ASSOC : MYSQL_NUM));
        
        return ($data ? $data : array());
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtiene los datos de la consulta
     * 
     * @access protected
     * @param string $sql
     * @param bool $assoc
     * @return array
     */
    protected function _getRows($sql, $assoc = true)
    {
        $rows = array();
        $assoc = ($assoc ? MYSQL_ASSOC : MYSQL_NUM);
        
        // Ejecutamos la consulta
        $this->rquery = $this->query($sql);
        
        while($row = mysql_fetch_array($this->rquery, $assoc))
        {
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Conectar a la base de datos
     * 
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $port
     * @param bool $persistent
     * 
     * @return resource
     */
    protected function _connect($host, $user, $pass, $port = null, $persistent = false)
    {
        if ($port)
        {
            $host = $host . ':' . $port;
        }
        
        if ( $conn_id = ($persistent ? @mysql_pconnect($host, $user, $pass) : @mysql_connect($host, $user, $pass)))
        {
            return $conn_id;
        }
        
        return false;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Retornamos el error SQL.
     * 
     * @return string Mensaje de error.
     */
    protected function error()
    {
        return @mysql_error($this->conn_id);
    }
}