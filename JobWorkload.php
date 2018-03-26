<?php

namespace shakura\yii2\gearman;

class JobWorkload extends \yii\base\BaseObject implements \Serializable
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
        return serialize($this->params);
    }

    public function unserialize($serialized)
    {
        $this->params = unserialize($serialized);
    }
}
