<?php

use Mapper\Mapper;

class Test extends Mapper
{
    private $testicle1, $testicle2;

    /**
     * @return mixed
     */
    public function getTesticle1()
    {
        return $this->testicle1;
    }

    /**
     * @param mixed $testicle1
     */
    public function setTesticle1($testicle1)
    {
        $this->testicle1 = $testicle1;
    }

    /**
     * @return mixed
     */
    public function getTesticle2()
    {
        return $this->testicle2;
    }

    /**
     * @param mixed $testicle2
     */
    public function setTesticle2($testicle2)
    {
        $this->testicle2 = $testicle2;
    }
}
