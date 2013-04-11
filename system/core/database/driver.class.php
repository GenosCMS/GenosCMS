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
 * Database Driver
 * 
 * Clase abstracta del Driver para la base de datos.
 * 
 * @package     Framework\Core\Database
 * @since       1.0.0
 * @final
 */
abstract class Core_Database_Driver {
    
    /**
     * Recurso
     * 
     * @var resource
     */
    protected $conn_id = null;
    
    /**
     * Arreglo con las consultas que vamos a ejecutar.
     * 
     * @var array
     */
    protected $query = array();
    
    /**
     * Arreglo con las UNION
     * 
     * @var array
     */
    protected $unions = array();
    
    /**
     * Consulta simple
     * 
     * @var string
     */
    protected $squery = '';
    
    /**
     * Query Result
     * 
     * @var resource
     */
    protected $rquery = null;
    
    /**
     * Contador de consultas
     * 
     * @var int
     */
    protected $total = 0;
    
    // --------------------------------------------------------------------
    
    /**
     * Devuelve una fila.
     * 
     * @access public
     * @param string $sql
     * @param bool $assoc
     * @return array
     */
    public function getRow($sql, $assoc = true)
    {
        return $this->_getRow($sql, $assoc);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Devuelve varias filas.
     * 
     * @access public
     * @param string $sql
     * @param bool $assoc
     * @return array
     */
    public function getRows($sql, $assoc = true)
    {
        return $this->_getRows($sql, $assoc);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Devuelve un campo de una fila
     * 
     * @access public
     * @param string $sql
     * @return mixed
     */
    public function getField($sql)
    {
        $result = '';
        $row = $this->_getRow($sql, false);
        
        if ( $row)
        {
            $result = $row[0];
        }
        
        return $result;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Consulta simple
     * 
     * @access public
     * @param string $sql
     * @return mixed
     */
    public function simple_query($sql)
    {
        // Reset
        $this->query = array();
        
        // Asignamos
        $this->squery = $sql;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Almacena la parte SELECT de una consulta.
     * 
     * @access public
     * @param string $select
     * @return object
     */
    public function select($select)
    {
        if ( ! isset($this->query['select']))
        {
            $this->query['select'] = 'SELECT ';
        }
        
        $this->query['select'] .= $select;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Almacena la parte FROM de una consulta.
     * 
     * @access public
     * @param string $table
     * @param string $alias
     * @return object
     */
    public function from($table, $alias = '')
    {
        $this->query['table'] = 'FROM ' . Core::getT($table) . ($alias ? ' AS ' . $alias : '');
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Almacena la parte WHERE de una consulta.
     * 
     * @access public
     * @param mixed $conds
     * @return object
     */
    public function where($conds)
    {
        $this->query['where'] = '';
        if ( is_array($conds) && count($conds) > 0)
        {
            foreach ($conds as $field => $value)
            {
                $this->query['where'] .= 'AND ' . $field . ' = ' . $this->escape($value);
            }
            
            $this->query['where'] = 'WHERE ' . trim(preg_replace('/^(AND|OR)(.*?)/i', '', trim($this->query['where'])));
        }
        else
        {
            if ( ! empty($conds))
            {
                $this->query['where'] = 'WHERE ' . $conds;
            }
        }
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Almacena la parte ORDER de una consulta.
     * 
     * @access public
     * @param string $order
     * @return object
     */
    public function order($order)
    {
        if ( ! empty($order))
        {
            $this->query['order'] = 'ORDER BY ' . $order;
        }
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Almacena la parte GROUP BY de una consulta.
     * 
     * @access public
     * @param string $group
     * @return object
     */
    public function group($group)
    {
        $this->query['group'] = 'GROUP BY ' . $group;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Almacena la parte HAVING de una consulta.
     * 
     * @access public
     * @param string $having
     * @return object
     */
    public function having($having)
    {
        $this->query['having'] = 'HAVING ' . $having;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Creamos un LEFT JOIN para la consulta.
     * 
     * @see self::_join()
     * @access public
     * @param string $table
     * @param string $alias
     * @param mixed $param
     * @return object
     */
    public function leftJoin($table, $alias, $param = null)
    {
        $this->_join('LEFT JOIN', $table, $alias, $param);
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Creamos un INNER JOIN para la consulta.
     * 
     * @see self::_join()
     * @access public
     * @param string $table
     * @param string $alias
     * @param mixed $param
     * @return object
     */
    public function innerJoin($table, $alias, $param = null)
    {
        $this->_join('INNER JOIN', $table, $alias, $param);
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Creamos un JOIN para la consulta.
     * 
     * @see self::_join()
     * @access public
     * @param string $table
     * @param string $alias
     * @param mixed $param
     * @return object
     */
    public function join($table, $alias, $param = null)
    {
        $this->_join('JOIN', $table, $alias, $param);
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Almacena la parte LIMIT / OFFSET de una consulta.
     * 
     * También se puede utilizar para crear una paginación si $limit y $total son
     * enviados, de lo contrario se comporta como un LIMIT en la consulta.
     * 
     * @access public
     * @param integer $page
     * @param integer $limit
     * @param integer $total
     * @param bool $return
     * @return object
     */
    public function limit($page, $limit = null, $total = null, $return = false)
    {
        if ( $limit === null && $total === null && $page !== null)
        {
            $this->query['limit'] = 'LIMIT ' . $page;
            
            return $this;
        }
        
        $offset = ($total === null ? $page : Core::getLib('core.pager')->getOffset($page, $limit, $total));
        
        $this->query['limit'] = ($limit ? 'LIMIT ' . $limit : '') . ($offset ? ' OFFSET ' . $offset : '');
        
        if ($return == true)
        {
            return $this->query['limit'];
        }
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Crear una llamada UNION
     * 
     * @access public
     * @return object
     */
    public function union()
    {
        
        $this->unions[] = $this->exec(null, array('union_no_check' => true));
        //$debug = debug_backtrace();
        //var_dump($debug[0]);
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Crear una llamada UNION FROM
     * 
     * @access public
     * @param string $alias
     * @return object
     */
    public function unionFrom($alias)
    {
        $this->query['union_from'] = $alias;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Define si la llamada es un join count
     * 
     * @access public
     * @return object
     */
    public function joinCount()
    {
        $this->query['join_count'] = true;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Lleva a cabo la última consulta SQL con toda la información que hemos
     * recogido de otros métodos de esta clase. A través de este método se
     * pueden realizar todas las tareas de conseguir un único campo de una fila,
     * sólo una fila o una lista de filas.
     * 
     * @see self::getRow()
     * @see self::getRows()
     * @see self::getField()
     * @param string $type El comando que vamos a ejecutar. Puede ser null para devolver simplementa la consulta SQL.
     * @param array $params Parámetros extra que podemos enviar
     * @return mixed
     */
    public function exec($type = null, $params = array())
    {
        $sql = '';
        
        if ( empty($this->query))
        {
            $sql = $this->squery;
        }
        else
        {
            if ( isset($this->query['select']))
            {
                $sql .= $this->query['select'] . "\n";
            }
            
            if ( isset($this->query['table']))
            {
                $sql .= $this->query['table'] . "\n";
            }
            
    		if (isset($this->query['union_from']))
    		{
    			$sql .= "FROM(\n";
    		}
            
    		if (!isset($params['union_no_check']) && count($this->unions))
    		{
    			$unionCnt = 0;
    			foreach ($this->unions as $union)
    			{
    				$unionCnt++;	
    				if ($unionCnt != 1)
    				{
    					$sql .= (isset($this->query['join_count']) ? ' + ' : ' UNION ');
    				}
    				
    				$sql .= '(' . $union . ')';
    			}
    		}
            
    		if (isset($this->query['join_count']))
    		{
    			$sql .= ') AS total_count';
    		}
    		
    		if (isset($this->query['union_from']))
    		{
    			$sql .= ") AS " . $this->query['union_from'] . "\n";
    		}
            
            $sql .= (isset($this->query['join']) ? $this->query['join'] . "\n" : '');
            $sql .= (isset($this->query['where']) ? $this->query['where'] . "\n" : '');
            $sql .= (isset($this->query['group']) ? $this->query['group'] . "\n" : '');
            $sql .= (isset($this->query['having']) ? $this->query['having'] . "\n" : '');
            $sql .= (isset($this->query['order']) ? $this->query['order'] . "\n" : '');
            $sql .= (isset($this->query['limit']) ? $this->query['limit'] . "\n" : '');
            
            $this->query = array();
    		if (!isset($params['union_no_check']))
    		{
    			$this->unions = array();
    		}
            
        }

        switch($type)
        {
            case 'row':
                $rows = $this->getRow($sql);
                break;
            case 'rows':
                $rows = $this->getRows($sql);
                break;
            case 'field':
                $rows = $this->getField($sql);
                break;
            default:
                return $sql;
                break;
        }
        
        return $rows;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Insertar una fila. Acepta datos enviados en un array.
     * 
     * @access public
     * @param string $table
     * @param array $data
     * @param bool $escape
     * @return int last_insert_id
     */
    public function insert($table, $data = array(), $escape = true)
    {
        $values = '';
        foreach($data as $val)
        {
            if (is_null($val))
            {
                $values .= 'NULL, ';
            }
            else
            {
                $values .= ($escape ? $this->escape($val) : $val) . ", ";
            }
        }
        $values = rtrim(trim($values), ',');
        
        $sql = $this->_insert($table, implode(', ', array_keys($data)), $values);
        
        if ($result = $this->query($sql))
        {
            return $this->getLastId();
        }
        
        return 0;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Actualizar datos.
     * 
     * @access public
     * @param string $table
     * @param array $values
     * @param string $cond
     * @param bool $escape
     * @return bool
     */
    public function update($table, $data, $cond = null, $escape = true)
    {
        $sets = '';
        foreach($data as $col => $val)
        {
            $cmd = '=';
            if (is_array($val))
            {
                $cmd = $val[0];
                $val = $val[1];
            }
            
            $sets .= "{$col} {$cmd} " . (is_null($val) ? 'NULL' : ($escape ? $this->escape($val) : $val)) . ', ';
        }
        $sets[strlen($sets) - 2] = ' ';
        
        return $this->query($this->_update($table, $sets, $cond));
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Eliminar registro de la base de datos.
     * 
     * @access public
     * @param $table
     * @param $query
     * @param $limit
     * @return  bool
     */
    public function delete($table, $query, $limit = null)
    {
        if ($limit !== null)
        {
            $query .= 'LIMIT ' . (int) $limit;
        }
        
        return $this->query('DELETE FROM ' . Core::getT($table) . ' WHERE ' . $query);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Actualizar un contador.
     * 
     * Esta función nos facilita realizar consultas del tipo:
     * 
     * UPDATE table SET counter = counter (+/-) 1 WHERE field = 1;
     * 
     * @access public
     * @param string $table
     * @param string $counter Campo que vamos a actualizar.
     * @param string $field Campo que debe coincidir para actualizar.
     * @param int $id Valor para el campo de coincidencia.
     * @param bool $minus Por defecto la variable se incrementa, cuando queramos disminuir debemos colocarla como true.
     * @return void
     */
    public function updateCounter($table, $counter, $field, $id, $minus = false)
    {
        $count = $this->select($counter)->from($table)->where($field . ' = ' . (int) $id)->execute('field');
        
        $this->update($table, array($counter => ($minus === true ? (($count <= 0 ? 0 : $count - 1)) : ($count + 1))), $field . ' = ' . (int) $id);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Actualizar contador
     * 
     * Esta funcion es similar a self::updateCounter() sin embargo en esta función
     * checamos primero la cantidad en otra tabla para establecer un nuevo valor.
     * 
     * @access public
     * @param string $countTable
     * @param array $countCond
     * @param string $counter
     * @param string $updateTable
     * @param array $updateCond
     * @return void
     */
    public function updateCount($countTable, $countCond, $counter, $updateTable, $updateCond)
    {
        $count = $this->select('COUNT(*)')
            ->from($countTable)
            ->where($countCond)
            ->exec('field');
            
        $this->update($updateTable, array($counter => $count), $updateCond);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Filtrar variables
     * 
     * @access public
     * @param string $str
     * @return mixed
     */
    public function escape($str)
    {
		if (is_string($str))
		{
			$str = "'".$this->escape_str($str)."'";
		}
		elseif (is_bool($str))
		{
			$str = ($str === false) ? 0 : 1;
		}
		elseif (is_null($str))
		{
			$str = 'NULL';
		}

		return $str;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener cantidad de consultas realizadas.
     * 
     * @access public
     * @return int
     */
    public function getQueries()
    {
        return $this->total;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Creamos un LEFT JOIN para la consulta.
     * 
     * @access protected
     * @param string $type
     * @param string $table
     * @param string $alias
     * @param mixed $param
     */
    protected function _join($type, $table, $alias, $param = null)
    {
        if ( ! isset($this->query['join']))
        {
            $this->query['join'] = '';
        }
        
        $this->query['join'] .= $type . ' ' . Core::getT($table) . ' AS ' . $alias;
        
        if (is_array($param))
        {
            $this->query['join'] .= "\n\tON(";
            foreach ($param as $value)
            {
                $this->query['join'] .= $value . ' ';
            }
        }
        else
        {
            if (preg_match('/(AND|OR|=|LIKE)/', $param))
            {
                $this->query['join'] .= "\n\tON({$param}";
            }
            else
            {
                show_error('No es permitido el uso de "USING()" en las consultas SQL nunca más.');
            }
        }
        
        $this->query['join'] = preg_replace('/^(AND|OR)(.*?)/i', '', trim($this->query['join'])) . ")\n";
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Crear consulta para INSERT
     * 
     * @access protected
     * @param $table
     * @param $fields
     * @param $values
     * @return string SQL
     */
    protected function _insert($table, $fields, $values)
    {
		return 'INSERT INTO ' . Core::getT($table) . ' '.
        	'        (' . $fields . ')'.
            ' VALUES (' . $values . ')';
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Crear consulta UPDATE
     * 
     * @access protected
     * @param $table
     * @param $sets
     * @param $cond
     * @return string SQL
     */
	protected function _update($table, $sets, $cond)
	{
		return 'UPDATE ' . Core::getT($table) . ' SET ' . $sets . ' WHERE ' . $cond;
	}
}