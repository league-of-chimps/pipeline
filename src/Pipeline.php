<?php

namespace Chimps\Pipeline;

class Pipeline
{
    /**
     * @var array
     */
    protected $pipes = [];

    /**
     * @var array
     */
    protected $generators = [];

    /**
     * @var callable
     */
    protected $terminator;

    /**
     * @param callable $pipe
     * @return $this
     */
    public function pipe(callable $pipe)
    {
        $this->pipes[] = $pipe;

        return $this;
    }

    /**
     * @param callable $terminator
     * @return $this
     */
    public function terminateWith(callable $terminator)
    {
        $this->terminator = $terminator;

        return $this;
    }

    /**
     * @param $passed
     * @return mixed
     */
    public function __invoke($passed)
    {
        return $this->dispatch($passed);
    }

    /**
     * @param $passed
     * @return mixed
     */
    public function dispatch($passed)
    {
        $this->generators = [];

        $passed = array_reduce($this->pipes, [$this, 'sendItem'], $passed);
        $passed = call_user_func($this->terminator, $passed);
        $passed = array_reduce($this->generators, [$this, 'returnItem'], $passed);

        return $passed;
    }

    /**
     * @param $passed
     * @param $pipe
     * @return mixed
     */
    public function sendItem($passed, $pipe)
    {
        $this->generators[] = $generator = $pipe($passed);

        return $generator->current();
    }

    /**
     * @param $passed
     * @param $pipe
     * @return mixed
     */
    public function returnItem($passed, $pipe)
    {
        return $pipe->send($passed);
    }
}
