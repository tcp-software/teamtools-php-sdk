<?php


namespace teamtools\Entities;


class Attribute
{
    public $id;
    public $name;
    public $prettyName;
    public $type;
    public $description;
    public $required;
    public $editable;
    public $searchable;
    public $default;
    public $defaultValue;
    private static $immutable = ['name', 'type', 'default'];

    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function prepareForUpdate()
    {
        foreach (static::$immutable as $i) {
            unset($this->$i);
        }

        return get_object_vars($this);
    }
}