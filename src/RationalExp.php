<?php

declare(strict_types=1);

namespace Scruoge\Fractions;

use DivisionByZeroError;
use JsonSerializable;

final readonly class RationalExp implements JsonSerializable
{
    private function __construct(
        private int $significand,
        private int $denominator = 1,
        private int $exponent = 0,
    ) {}

    public function mul(self $that): self
    {
        return self::normalize(
            $this->significand * $that->significand,
            $this->denominator * $that->denominator,
            $this->exponent + $that->exponent,
        );
    }

    public function div(self $that): self
    {
        return self::normalize(
            $this->significand * $that->denominator,
            $this->denominator * $that->significand,
            $this->exponent - $that->exponent,
        );
    }

    public function add(self $that): self
    {
        $exponentDiff = $that->exponent - $this->exponent;
        if ($exponentDiff === 0) {
            return self::normalize(
                $this->significand * $that->denominator + $that->significand * $this->denominator,
                $this->denominator * $that->denominator,
                $this->exponent,
            );
        }
        if ($exponentDiff > 0) {
            return self::normalize(
                $this->significand * $that->denominator + $that->significand * 10 ** $exponentDiff * $this->denominator,
                $this->denominator * $that->denominator,
                $this->exponent,
            );
        }

        return self::normalize(
            $this->significand * 10 ** -$exponentDiff * $that->denominator + $that->significand * $this->denominator,
            $this->denominator * $that->denominator,
            $that->exponent,
        );
    }

    public function sub(self $that): self
    {
        return $this->add(new self(-$that->significand, $that->denominator, $that->exponent));
    }

    public function equals(self $that): bool
    {
        $d = $this->sub($that);
        return $d->significand === 0 && $d->denominator === 1;
    }

    public function toFloat(): float
    {
        return $this->significand / $this->denominator * 10 ** $this->exponent;
    }

    /**
     * @return int[]
     */
    public function jsonSerialize(): array
    {
        return [
            'significand' => $this->significand,
            'denominator' => $this->denominator,
            'exponent' => $this->exponent,
        ];
    }

    public static function create(
        int $significand,
        int $exponent = 0,
        int $denominator = 1,
    ): self {
        if ($denominator === 0) {
            throw new DivisionByZeroError('Denominator cannot be zero.');
        }

        return self::normalize($significand, $denominator, $exponent);
    }

    public static function fromNumber(
        int|float $number,
        int $digits = -1,
        int $roundMode = PHP_ROUND_HALF_UP,
    ): self {
        if (\is_int($number)) {
            return self::normalize($number, 1, 0);
        }

        if ($digits >= 0) {
            return self::normalize(
                significand: (int) round($number * 10 ** $digits, mode: $roundMode),
                denominator: 1,
                exponent: -$digits,
            );
        }

        $significand = $number;
        $exponent = 0;
        while ($significand - (int) $significand !== 0.0) {
            $significand *= 10;
            $exponent--;
        }

        return self::normalize(
            significand: (int) $significand,
            denominator: 1,
            exponent: $exponent
        );
    }

    public static function zero(): self
    {
        return new self(
            significand: 0,
            exponent: 0
        );
    }

    public static function one(): self
    {
        return new self(
            significand: 1,
            exponent: 0
        );
    }

    private static function gcd(int $a, int $b): int
    {
        if ($a === $b) {
            return $a;
        }
        if ($a === 0 || $b === 0) {
            return 0;
        }
        if ($a === 1 || $b === 1) {
            return 1;
        }
        if (!($a & 1 || $b & 1)) {
            return self::gcd($a >> 1, $b >> 1) << 1;
        }
        if (!($a & 1)) {
            return self::gcd($a >> 1, $b);
        }
        if (!($b & 1)) {
            return self::gcd($a, $b >> 1);
        }
        if ($a > $b) {
            return self::gcd(($a - $b) >> 1, $b);
        }
        return self::gcd(($b - $a) >> 1, $a);
    }

    private static function normalize(
        int $significand,
        int $denominator,
        int $exponent
    ): self {
        if ($significand === 0) {
            return new self($significand);
        }

        $gcd = self::gcd(abs($significand), abs($denominator));
        $significand = intdiv($significand, $gcd);
        $denominator = intdiv($denominator, $gcd);

        while ($significand - ($ts = (int) ($significand * .1)) * 10 === 0) {
            $significand = $ts;
            $exponent++;
        }

        while ($denominator - ($td = (int) ($denominator * .1)) * 10 === 0) {
            $denominator = $td;
            $exponent--;
        }

        return new self($significand, $denominator, $exponent);
    }
}
