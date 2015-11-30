<?php

namespace shakura\yii2\gearman;

abstract class JobBase extends \yii\base\Component implements JobInterface
{
    protected $name;

    public function init()
    {
        return true;
    }
    
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
    
    /**
     * @param \GearmanJob $job
     * @return \shakura\yii2\gearman\JobWorkload
     */
    protected function getWorkload(\GearmanJob $job)
    {
        $workload = null;
        if($data = $job->workload()) {
            $workload = unserialize($data);
        }
        return $workload;
    }

    /**
     * @param \GearmanJob $job
     * @return string
     */
    protected function getUnique (\GearmanJob $job)
    {
        return $job->unique();
    }

    /**
     * @param \GearmanJob $job
     * @return int
     */
    protected function getWorkloadSize (\GearmanJob $job)
    {
        return $job->workloadSize();
    }

    /**
     * @param \GearmanJob $job
     * @param $numerator
     * @param $denominator
     * @return bool
     */
    protected function sendStatus (\GearmanJob $job, $numerator, $denominator)
    {
        return $job->sendStatus($numerator, $denominator);
    }

    /**
     * @param \GearmanJob $job
     * @return string
     */
    protected function functionName (\GearmanJob $job)
    {
        return $job->functionName();
    }

    /**
     * @param \GearmanJob $job
     * @return string
     */
    protected function handle (\GearmanJob $job)
    {
        return $job->handle();
    }
}