<?php

// Open Closed Principle Violation
class Programmer
{
    public function code()
    {
        return 'coding';
    }
}


class Tester
{
    public function test()
    {
        return 'testing';
    }
}


/** Si se quisiera agregar otra tarea al management habría que estar modificando esta clase
 * Tantas veces como tareas....  * */
class ProjectManagement
{
    public function process($member)
    {
        if ($member instanceof Programmer) {
            $member->code();
        } elseif ($member instanceof Tester) {
            $member->test();
        };
        throw new Exception('Invalid input member');
    }
}