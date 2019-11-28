<?php

namespace CodeHappy\DataLayer\Tests\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use CodeHappy\DataLayer\Contracts\RepositoryInterface;
use CodeHappy\DataLayer\Queries\Factory;
use CodeHappy\DataLayer\Tests\TestCase;
use InvalidArgumentException;
use Mockery;

class FactoryOrderByTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * @var \CodeHappy\DataLayer\Contracts\RepositoryInterface
     */
    protected $repository;

    /**
     * @var \CodeHappy\DataLayer\Queries\Factory
     */
    protected $factory;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->builder      = Mockery::mock(Builder::class);
        $this->repository   = Mockery::mock(RepositoryInterface::class);

        $this->factory = new Factory;
        $this->factory->load($this->builder, $this->repository);
    }

    /**
     * @test
     * @dataProvider additionProvider
     */
    public function it_creates_an_instance_of_order_by_should_be_successful(
        $args,
        $params
    ): void {
        foreach (array_keys($args) as $arg) {
            DB::shouldReceive('raw')
                ->with($arg)
                ->once()
                ->andReturn((string) $arg);
        }

        foreach ($args as $column => $orientation) {
            $this->builder
                ->shouldReceive('orderBy')
                ->with($column, $orientation)
                ->once()
                ->andReturn($this->builder);
        }

        $this->assertInstanceOf(
            Builder::class,
            $this->factory->orderBy(...$params)
        );
    }

    /**
     * @return array[][]
     */
    public function additionProvider(): array
    {
        return [
            [
                [
                    'id' => 'ASC',
                ],
                ['id'],
            ],
            [
                [
                    'id' => 'ASC',
                ],
                ['id', 'ASC'],
            ],
            [
                [
                    'id' => 'DESC',
                ],
                ['id', 'DESC'],
            ],
            [
                [
                    'status' => 'ASC',
                    'name'   => 'ASC',
                ],
                ['status', 'name'],
            ],
            [
                [
                    'status' => 'DESC',
                    'name'   => 'ASC',
                ],
                ['status DESC', 'name'],
            ],
            [
                [
                    'status' => 'ASC',
                    'name'   => 'DESC',
                ],
                ['status', 'name DESC'],
            ],
            [
                [
                    'status' => 'ASC',
                    'name'   => 'ASC',
                ],
                [
                    ['status', 'name'],
                ],
            ],
            [
                [
                    'status' => 'DESC',
                    'name'   => 'ASC',
                ],
                [
                    ['status DESC', 'name'],
                ],
            ],
            [
                [
                    'status' => 'ASC',
                    'name'   => 'DESC',
                ],
                [
                    ['status', 'name DESC'],
                ],
            ],
            [
                [
                    'status' => 'ASC',
                    'name'   => 'ASC',
                ],
                [
                    [
                        'status' => 'ASC',
                        'name'   => 'ASC',
                    ],
                ],
            ],
            [
                [
                    'status' => 'DESC',
                    'name'   => 'ASC',
                ],
                [
                    [
                        'status' => 'DESC',
                        'name'   => 'ASC',
                    ],
                ],
            ],
            [
                [
                    'status' => 'ASC',
                    'name'   => 'DESC',
                ],
                [
                    [
                        'status' => 'ASC',
                        'name'   => 'DESC',
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function it_raises_an_exception_should_be_successful(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->orderBy();
    }
}
