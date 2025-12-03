# lib-fractions

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-8892BF.svg)](https://php.net/)

A PHP library for precision mathematical calculations using rational numbers. This library operates on the rational domain, providing exact decimal representation without the precision loss inherent in floating-point arithmetic.

## Features

- **Exact Precision**: No floating-point rounding errors
- **Rational Number Arithmetic**: Operations on fractions with significand, denominator, and exponent
- **Multiple Input Formats**: Create rational numbers from integers, floats, or explicit fraction components
- **JSON Serializable**: Easy integration with APIs and data storage
- **Comprehensive Operations**: Addition, subtraction, multiplication, division, and comparison
- **Flexible Rounding**: Configurable rounding modes when converting from floats

## Installation

Install via Composer:

```bash
composer require scruoge/lib-fractions
```

### Requirements

- PHP 8.0 or higher

## Quick Start

```php
<?php

use Scruoge\Fractions\RationalExp;

// Create rational numbers
$a = RationalExp::fromNumber(2.3);        // 2.3 as exact fraction
$b = RationalExp::create(120, -2);        // 120 * 10^(-2) = 1.2
$c = RationalExp::zero();                 // 0
$d = RationalExp::one();                  // 1

// Perform arithmetic operations
$sum = $a->add($b);                       // 2.3 + 1.2 = 3.5
$difference = $a->sub($b);                // 2.3 - 1.2 = 1.1
$product = $a->mul($b);                   // 2.3 * 1.2 = 2.76
$quotient = $a->div($b);                  // 2.3 / 1.2 = 1.916...

// Convert back to float
echo $sum->toFloat();                     // 3.5

// Check equality
$isEqual = $a->equals($b);                // false
```

## API Reference

### Constructor Methods

#### `create(int $significand, int $exponent = 0, int $denominator = 1): RationalExp`

Creates a rational number with explicit components.

```php
// Creates 123 * 10^(-2) / 4 = 1.23/4 = 0.3075
$rational = RationalExp::create(123, -2, 4);
```

#### `fromNumber(int|float $number, int $digits = -1, int $roundMode = PHP_ROUND_HALF_UP): RationalExp`

Creates a rational number from an integer or float.

```php
// Automatic precision detection
$auto = RationalExp::fromNumber(1234.2345);

// Fixed precision with rounding
$rounded = RationalExp::fromNumber(1234.2345, 3);             // 3 decimal places
$roundDown = RationalExp::fromNumber(1234.2345, 3, PHP_ROUND_HALF_DOWN);
```

#### `zero(): RationalExp` and `one(): RationalExp`

Convenience methods for common values.

```php
$zero = RationalExp::zero();    // 0
$one = RationalExp::one();      // 1
```

### Arithmetic Operations

All arithmetic operations return a new `RationalExp` instance:

```php
$a = RationalExp::fromNumber(2.5);
$b = RationalExp::fromNumber(1.5);

$addition = $a->add($b);        // 4.0
$subtraction = $a->sub($b);     // 1.0
$multiplication = $a->mul($b);  // 3.75
$division = $a->div($b);        // 1.666...
```

### Utility Methods

#### `equals(RationalExp $that): bool`

Checks if two rational numbers are equal.

```php
$a = RationalExp::fromNumber(0.1);
$b = RationalExp::fromNumber(0.1);
$isEqual = $a->equals($b);      // true
```

#### `toFloat(): float`

Converts the rational number to a float (may lose precision).

```php
$rational = RationalExp::create(1, 0, 3);
$float = $rational->toFloat();  // 0.3333...
```

#### `jsonSerialize(): array`

Serializes the rational number for JSON encoding.

```php
$rational = RationalExp::create(1, 2, 3);
$json = json_encode($rational);
// {"significand":1,"denominator":3,"exponent":2}
```

## Advanced Usage

### Working with Fractions

```php
// Create 3/8
$fraction = RationalExp::create(3, 0, 8);

// Create 1/3 (which cannot be represented exactly as a decimal)
$oneThird = RationalExp::create(1, 0, 3);
```

### Precision Control

When converting from floats, you can control precision and rounding:

```php
$value = 1234.2345;

// Automatic precision (detects significant digits)
$auto = RationalExp::fromNumber($value);

// Fixed precision
$fixed = RationalExp::fromNumber($value, 2);  // 1234.23

// Different rounding modes
$halfUp = RationalExp::fromNumber($value, 2, PHP_ROUND_HALF_UP);    // 1234.23
$halfDown = RationalExp::fromNumber($value, 2, PHP_ROUND_HALF_DOWN); // 1234.23
$up = RationalExp::fromNumber($value, 2, PHP_ROUND_UP);            // 1234.24
```

### Financial Calculations

Perfect for monetary calculations where precision is crucial:

```php
$price = RationalExp::fromNumber(19.99);
$taxRate = RationalExp::fromNumber(0.08);
$quantity = RationalExp::fromNumber(3);

$subtotal = $price->mul($quantity);           // 59.97
$tax = $subtotal->mul($taxRate);              // 4.7976
$total = $subtotal->add($tax);                // 64.7676

// Round to cents for final amount
$finalTotal = RationalExp::fromNumber($total->toFloat(), 2);  // 64.77
```

## Development

### Setup

Clone the repository and install dependencies:

```bash
git clone https://github.com/scruoge/lib-fractions.git
cd lib-fractions
composer install
```

### Running Tests

```bash
composer test
# or
./vendor/bin/phpunit
```

### Code Style

This project uses PHP-CS-Fixer for code formatting:

```bash
composer cs-fix
# or
./vendor/bin/php-cs-fixer fix
```

### Project Structure

```
lib-fractions/
├── src/
│   └── RationalExp.php      # Main rational number class
├── test/
│   └── RationalExpTest.php  # Unit tests
├── composer.json            # Project configuration
├── phpunit.xml             # PHPUnit configuration
└── .php-cs-fixer.dist.php  # Code style configuration
```

## Use Cases

This library is particularly useful for:

- **Financial applications** where exact decimal representation is required
- **Scientific calculations** requiring rational number precision
- **Data processing** where floating-point errors must be avoided
- **Mathematical applications** working with fractions and ratios

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests (`composer test`)
5. Fix code style (`composer cs-fix`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

Please ensure all tests pass and follow the existing code style.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

**Sergey N. Kruk** - [scruoge@gmail.com](mailto:scruoge@gmail.com)

---

*For more examples and detailed documentation, please refer to the test files in the `test/` directory.*
