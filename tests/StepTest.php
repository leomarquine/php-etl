<?php

namespace Tests;

class StepTest extends TestCase
{
    /** @test */
    public function set_pipeline()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $step = new FakeStep;

        $step->pipeline($pipeline);

        $this->assertAttributeEquals($pipeline, 'pipeline', $step);
    }

    /** @test */
    public function set_options()
    {
        $step = new FakeStep;

        $step->options([
            'option1' => 'value',
            'option2' => 'value',
        ]);

        $this->assertAttributeEquals('value', 'option1', $step);
        $this->assertAttributeEquals(null, 'option2', $step);
    }

}

class FakeStep extends \Marquine\Etl\Step
{
    protected $option1;
    protected $option2;
    protected $configurableOptions = ['option1'];
}
