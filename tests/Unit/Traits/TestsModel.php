<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Traits;

//
// Taken from https://github.com/JeffreyWay/Laravel-Test-Helpers/blob/master/src/Way/Tests/ModelHelpers.php
//

use Mockery as m;

trait TestsModel
{
    protected function assertBelongsToMany($parent, $child)
    {
        $this->assertRelationship($parent, $child, 'belongsToMany');
    }

    protected function assertBelongsTo($parent, $child)
    {
        $this->assertRelationship($parent, $child, 'belongsTo');
    }

    protected function assertHasMany($relation, $class)
    {
        $this->assertRelationship($relation, $class, 'hasMany');
    }

    protected function assertHasOne($relation, $class)
    {
        $this->assertRelationship($relation, $class, 'hasOne');
    }

    protected function assertMorphMany($relation, $class)
    {
        $this->assertRelationship($relation, $class, 'morphMany');
    }

    protected function assertMorphTo($relation, $class)
    {
        $this->assertRelationship($relation, $class, 'morphTo');
    }

    protected function assertRespondsTo($method, $class, $message = null)
    {
        $message = $message ?: "Expected the '$class' class to have method, '$method'.";

        $this->assertTrue(method_exists($class, $method), $message);
    }

    private function assertRelationship($relationship, $class, $type)
    {
        $this->assertRespondsTo($relationship, $class);

        $args = $this->getArgumentsRelationship($relationship, $class, $type);

        $class = m::mock($class . "[$type]")->shouldIgnoreMissing()->asUndefined();

        switch (count($args)) {
            case 1:
                $class->shouldReceive($type)
                      ->once()
                      ->with(m::pattern('/' . str_singular($relationship) . '/i'))
                      ->andReturn(m::self());
                break;
            case 2:
                $class->shouldReceive($type)
                      ->once()
                      ->with(m::pattern('/' . str_singular($relationship) . '/i'), $args[1])
                      ->andReturn(m::self());
                break;
            case 3:
                $class->shouldReceive($type)
                      ->once()
                      ->with(m::pattern('/' . str_singular($relationship) . '/i'), $args[1], $args[2])
                      ->andReturn(m::self());
                break;
            case 4:
                $class->shouldReceive($type)
                      ->once()
                      ->with(m::pattern('/' . str_singular($relationship) . '/i'), $args[1], $args[2], $args[3])
                      ->andReturn(m::self());
                break;
            default:
                $class->shouldReceive($type)
                      ->once()
                      ->andReturn(m::self());
                break;
        }

        $class->$relationship();
    }

    private function getArgumentsRelationship($relationship, $class, $type)
    {
        $mocked = m::mock($class . "[$type]")->shouldIgnoreMissing()->asUndefined();

        $mocked->shouldReceive($type)
               ->once()
               ->andReturnUsing(function () use (&$args) {
                   $args = func_get_args();

                   return m::self();
               });

        $mocked->$relationship();

        return $args;
    }
}
