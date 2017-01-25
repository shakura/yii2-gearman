<?php

namespace thmrxx\yii2\gearman;

abstract class JobBase extends \yii\base\Component implements JobInterface
{
    protected $name;
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @var $name string
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function init(){
        return true;
    }
    
    /**
     * @param \GearmanJob $job
     * @return JobWorkload
     */
    protected function getWorkload(\GearmanJob $job)
    {
        $workload = null;
        if($data = $job->workload()) {
            $workload = Serialize::decode($data);
        }
        return $workload;
    }
}
