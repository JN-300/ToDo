<?php

namespace Tests\Feature\Task;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

abstract class TaskTestsAbstract extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
    }
}
