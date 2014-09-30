<?php
namespace Adminko;

class Compare
{
    const SESSION_VAR = '__compare__';
    
    private $type = null;
    
    private $items = array();
    
    private static $instance = null;
    
    public static final function factory()
    {
        if (self::$instance == null) {
            self::$instance = new Compare();
       }
        return self::$instance;
    }
    
    private function __construct()
    {
        if (!isset($_SESSION[self::SESSION_VAR]) || !is_array($_SESSION[self::SESSION_VAR])) {
            $_SESSION[self::SESSION_VAR] = array();
        }
        $this->items = $_SESSION[self::SESSION_VAR];
        $this->type = current(array_keys($this->items));
    }
    
    public function __destruct()
    {
        $_SESSION[self::SESSION_VAR] = $this->items;
    }
    
    public function get()
    {
        return $this->items;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function add($id, $type)
    {
        if ($this->type && $this->type != $type) {
            unset($this->items[$this->type]);
        }
        
        $this->type = $type;
        $this->items[$this->type][$id] = $id;
    }
    
    public function in($id)
    {
        return isset($this->items[$this->type][$id]);
    }
    
    public function delete($id)
    {
        unset($this->items[$this->type][$id]);
        
        if (isset($this->items[$this->type]) && count($this->items[$this->type]) == 0) {
            $this->clear();
        }
    }
    
    public function clear()
    {
        $this->type = null;
        $this->items = array();
    }
    
    public function count()
    {
        return isset($this->items[$this->type]) ? count($this->items[$this->type]) : 0;
    }
}
