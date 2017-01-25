<?php

namespace thmrxx\yii2\gearman;

class JobWorkload extends \yii\base\Object implements \Serializable
{
    protected $params = [];
    
    public function setParams($params)
    {
        $this->params = $params;
    }
    
    public function getParams()
    {
        return $this->params;
    }
    
    public function serialize()
    {
        return Serialize::encode($this->params);
    }

    public function unserialize($serialized)
    {
        $this->params = Serialize::decode($serialized);
    }
}
