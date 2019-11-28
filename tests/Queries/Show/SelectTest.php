<?php

namespace CodeHappy\DataLayer\Tests\Queries\Show;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use CodeHappy\DataLayer\Contracts\RepositoryInterface;
use CodeHappy\DataLayer\Queries\Show\Select as Query;
use CodeHappy\DataLayer\Tests\TestCase;
use InvalidArgumentException;
use Mockery;

class SelectTest extends TestCase
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
     * @var Query
     */
    protected $query;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->builder    = Mockery::mock(Builder::class);
        $this->repository = Mockery::mock(RepositoryInterface::class);

        $this->query = new Query($this->builder, $this->repository);
    }

    /**
     * @test
     * @dataProvider additionProvider
     */
    public function it_handles_should_be_successful($args, $params): void
    {
        foreach ($args[0] as $arg) {
            DB::shouldReceive('raw')
                ->with($arg)
                ->once()
                ->andReturn((string) $arg);
        }
        $this->builder
            ->shouldReceive('select')
            ->with(...$args)
            ->once()
            ->andReturn($this->builder);
        $this->assertInstanceOf(Builder::class, $this->query->handle(...$params));
    }

    /**
     * @return array[][]
     */
    public function additionProvider(): array
    {
        return [
            [
                [
                    ['*'],
                ],
                ['*'],
            ],
            [
                [
                    ['*'],
                ],
                [
                    ['*'],
                ],
            ],
            [
                [
                    ['id', 'name', 'email', 'created_at', 'updated_at'],
                ],
                ['id', 'name', 'email', 'created_at', 'updated_at'],
            ],
            [
                [
                    ['id', 'name', 'email', 'created_at', 'updated_at'],
                ],
                [
                    ['id', 'name', 'email', 'created_at', 'updated_at'],
                ],
            ],
            [
                [
                    ['customer_id', 'SUM(total_payed) AS total_payed'],
                ],
                ['customer_id', 'SUM(total_payed) AS total_payed'],
            ],
            [
                [
                    ['customer_id', 'SUM(total_payed) AS total_payed'],
                ],
                [
                    ['customer_id', 'SUM(total_payed) AS total_payed'],
                ],
            ],
            [
                [
                    ['orders.id', 'customers.email', 'orders.date', 'orders.total'],
                ],
                ['orders.id', 'customers.email', 'orders.date', 'orders.total'],
            ],
            [
                [
                    ['orders.id', 'customers.email', 'orders.date', 'orders.total'],
                ],
                [
                    ['orders.id', 'customers.email', 'orders.date', 'orders.total'],
                ],
            ],
            [
                [
                    ['orders.id, customers.email, orders.date, orders.total'],
                ],
                [
                    ['orders.id', 'customers.email', 'orders.date', 'orders.total'],
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function it_raises_an_exception_without_params_should_be_successful(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->query->handle();
    }
}