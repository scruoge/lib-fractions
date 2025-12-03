<?php

declare(strict_types=1);

namespace Scruoge\Fractions\Test;

use DivisionByZeroError;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Scruoge\Fractions\RationalExp;

final class RationalExpTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testConstructors(): void
    {
        $actual = RationalExp::zero();
        $expected = RationalExp::create(0);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $actual = RationalExp::one();
        $expected = RationalExp::create(10, -1);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));

        $actual = RationalExp::fromNumber(123);
        $expected = RationalExp::create(123);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));

        $actual = RationalExp::fromNumber(1234.2345);
        $expected = RationalExp::create(12342345, -4);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));

        $actual = RationalExp::fromNumber(1234.2345, 3);
        $expected = RationalExp::create(1234235, -3);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));

        $actual = RationalExp::fromNumber(1234.2345, 3, PHP_ROUND_HALF_DOWN);
        $expected = RationalExp::create(1234234, -3);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));

        $actual = RationalExp::fromNumber(1234.2345, 0);
        $expected = RationalExp::create(1234);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));

        $a = RationalExp::create(12, 0, 4);
        $reflection = new ReflectionClass(RationalExp::class);
        $reflection->getMethod('gcd')->invoke($a, 0, 1);

        self::expectException(DivisionByZeroError::class);
        RationalExp::create(1, 0, 0);
    }

    public function testSerializers(): void
    {
        $a = RationalExp::create(1, 2, 3);
        self::assertSame(
            [
                'significand' => 1,
                'denominator' => 3,
                'exponent' => 2,
            ],
            $a->jsonSerialize(),
        );
    }

    public function testAdd(): void
    {
        $a = RationalExp::create(230, -2);
        $b = RationalExp::create(120, -2);
        $c = RationalExp::one();
        $actual = $a->add($b);
        $expected = RationalExp::create(350, -2);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $actual = $a->add($c);
        $expected = RationalExp::create(330, -2);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $a = RationalExp::create(3, 0, 8);
        $b = RationalExp::create(1, 0, 3);
        $actual = $a->add($b);
        $expected = RationalExp::create(17, 0, 24);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $a = RationalExp::create(7, 1, 15);
        $b = RationalExp::create(5, 0, 6);
        $actual = $a->add($b);
        $expected = RationalExp::create(11, 0, 2);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
    }

    public function testSub(): void
    {
        $a = RationalExp::create(230, -2);
        $b = RationalExp::create(120, -2);
        $c = RationalExp::one();
        $actual = $a->sub($b);
        $expected = RationalExp::create(110, -2);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $actual = $b->sub($a);
        $expected = RationalExp::create(-110, -2);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $actual = $b->sub($c);
        $expected = RationalExp::create(20, -2);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $a = RationalExp::create(1, 0, 3);
        $b = RationalExp::create(3, 0, 8);
        $actual = $a->sub($b);
        $expected = RationalExp::create(-1, 0, 24);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $a = RationalExp::create(7, 0, 15);
        $b = RationalExp::create(5, 0, 6);
        $actual = $a->sub($b);
        $expected = RationalExp::create(-11, 0, 30);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
    }

    public function testMul(): void
    {
        $a = RationalExp::create(230, -2);
        $b = RationalExp::create(120, -2);
        $actual = $a->mul($b);
        $expected = RationalExp::create(12 * 23, -2);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $b = RationalExp::create(1, 0, 3);
        $a = RationalExp::create(3, 0, 8);
        $actual = $a->mul($b);
        $expected = RationalExp::create(3, 0, 24);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
        $a = RationalExp::create(7, 0, 15);
        $b = RationalExp::create(5, 0, 6);
        $actual = $a->mul($b);
        $expected = RationalExp::create(7, 0, 18);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
    }

    public function testDiv(): void
    {
        $a = RationalExp::create(230, -2);
        $b = RationalExp::create(120, -2);
        $actual = $a->div($b);
        $expected = RationalExp::create(23, 0, 12);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));

        $a = RationalExp::create(330, -2);
        $b = RationalExp::create(110, -3);
        $actual = $a->div($b);
        $expected = RationalExp::create(30);
        self::assertObjectEquals($expected, $actual, 'equals', \sprintf(
            'Failed to assert that %0.3f equals to %0.3f',
            $actual->toFloat(),
            $expected->toFloat()
        ));
    }
}
