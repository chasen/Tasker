<?php
namespace CN\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Task implements \Serializable
{
    /**
     * @Assert\Uuid(versions=[4])
     */
    private $id;

    /**
     * @Assert\NotNull()
     * @Assert\Type(type="string")
     */
    private $task;

    public function init($propertyValues = array()){
        foreach($propertyValues as $property => $value){
            if(property_exists($this, $property)){
                $this->$property = $value;
            }
        }
    }

    public function toArray(){
        return array(
            'id'=>$this->id,
            'task'=>$this->task,
        );
    }

    public function serialize()
    {
        return json_encode(array(
            'id'=>$this->id,
            'task'=>$this->task,
        ));
    }
    public function unserialize($serialized)
    {
        $this->init(json_decode($serialized, true));
    }

    public function __get($property)
    {
        if(property_exists($this, $property)){
            return $this->$property;
        }
        return null;
    }
    public function __set($property, $value)
    {
        if(property_exists($this,$property)){
            $this->$property = $value;
        }
        return $this;
    }
}