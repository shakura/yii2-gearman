<?php
namespace shakura\yii2\gearman;

use GearmanJob;

interface JobInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param GearmanJob|null $job
     * @return mixed
     */
    public function execute(GearmanJob $job = null);

    /**
     * @var $name string
     */
    public function setName($name);

    public function init();
}
