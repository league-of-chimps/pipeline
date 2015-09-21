<?php

namespace spec\Chimps\Pipeline;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PipelineSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Chimps\Pipeline\Pipeline');
    }

    function it_runs()
    {
        $pipe = function($arg) {
            $arg++;
            $arg = (yield $arg);
            $arg--;
            yield $arg;
        };

        $this->pipe($pipe);
        $this->pipe($pipe);
        $this->pipe($pipe);
        $this->pipe($pipe);
        $this->terminateWith(function($arg) {
            return $arg * 4;
        });
        $this(1)->shouldReturn(16);
    }
}
