<?php

declare(strict_types=1);

namespace App\Tests\unit\App\EconumoBundle\Domain\Entity\ValueObject;

use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use TypeError;

class DecimalNumberTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @dataProvider constructorProvider
     */
    public function testConstructor(string|int|float $input, string $expected): void
    {
        $number = new DecimalNumber($input);
        $this->assertEquals($expected, $number->getValue());
    }

    public function constructorProvider(): array
    {
        return [
            // Basic tests
            'integer' => [123, '123'],
            'float' => [123.456, '123.456'],
            'string' => ['123.456', '123.456'],
            'zero' => [0, '0'],
            'negative' => [-123.456, '-123.456'],
            'large number' => [12345678999999.0, '12345678999999'],
            'large precision' => [1.012345678999999, '1.01234568'],
            'trailing zeros' => [123.4500, '123.45'],
            'zero decimal' => [123.0, '123'],
            'string with trailing zeros' => ['123.4500', '123.45'],
            'leading zeros integer' => ['00123', '123'],
            'leading zeros decimal' => ['00123.456', '123.456'],
            'leading zeros after decimal' => ['123.0456', '123.0456'],
            'negative with leading zeros' => ['-00123.456', '-123.456'],
            'zero with leading zeros' => ['00.123', '0.123'],
            'negative zero with leading zeros' => ['-00.123', '-0.123'],
            'multiple leading zeros' => ['000000123', '123'],
            'zero with multiple leading zeros' => ['000000', '0'],
            // Scientific notation tests - basic
            'positive scientific small' => ['1.23e-5', '0.0000123'],
            'positive scientific large' => ['1.23e5', '123000'],
            'negative scientific small' => ['-1.23e-5', '-0.0000123'],
            'negative scientific large' => ['-1.23e5', '-123000'],
            'scientific zero exponent' => ['1.23e0', '1.23'],
            'scientific exact scale' => ['1.0e-8', '0.00000001'],
            'scientific beyond scale' => ['1.0e-9', '0'],
            'scientific large precision' => ['1.23456789e-5', '0.00001234'],
            // Scientific notation tests - edge cases
            'scientific uppercase E' => ['1.23E-5', '0.0000123'],
            'scientific positive exponent' => ['1.23E+5', '123000'],
            'scientific integer base' => ['1e-5', '0.00001'],
            'scientific large exponent' => ['1.23e20', '123000000000000000000'],
            'scientific small exponent' => ['1.23e-20', '0'],
            'scientific zero base' => ['0e5', '0'],
            'scientific negative zero base' => ['-0e5', '0'],
            'scientific one base' => ['1e5', '100000'],
            'scientific negative one base' => ['-1e5', '-100000'],
            'scientific exact boundary' => ['1e-8', '0.00000001'],
            'scientific just beyond boundary' => ['9.99999999e-9', '0'],
            // Scientific notation tests - comparison edge cases
            'scientific vs decimal equal' => ['1.0e-8', '0.00000001'],
            'scientific vs decimal less' => ['0.9e-7', '0.00000009'],
            'scientific vs decimal more' => ['1.1e-7', '0.00000011'],
        ];
    }

    /**
     * @dataProvider invalidConstructorProvider
     */
    public function testConstructorWithInvalidInput(mixed $input): void
    {
        $this->expectException(TypeError::class);
        new DecimalNumber($input);
    }

    public function invalidConstructorProvider(): array
    {
        return [
            'string' => ['abc'],
            'array' => [[]],
            'null' => [null],
            'boolean' => [true],
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testAdd(string|int|float $a, string|int|float $b, string $expected): void
    {
        $number = new DecimalNumber($a);
        $result = $number->add($b);
        $this->assertEquals($expected, $result->getValue());
    }

    public function additionProvider(): array
    {
        return [
            'positive numbers' => [1.5, 2.5, '4'],
            'negative numbers' => [-1.5, -2.5, '-4'],
            'mixed signs' => [1.5, -2.5, '-1'],
            'zero addition' => [1.5, 0, '1.5'],
            'precision test' => [0.1, 0.2, '0.3'],
            // Scientific notation tests
            'scientific small numbers' => ['1.23e-5', '2.77e-5', '0.00004'],
            'scientific large numbers' => ['1.23e5', '2.77e5', '400000'],
            'scientific mixed scales' => ['1.23e-5', '2.77e5', '277000.0000123'],
            'scientific negative numbers' => ['-1.23e-5', '-2.77e-5', '-0.00004'],
        ];
    }

    /**
     * @dataProvider subtractionProvider
     */
    public function testSub(string|int|float $a, string|int|float $b, string $expected): void
    {
        $number = new DecimalNumber($a);
        $result = $number->sub($b);
        $this->assertEquals($expected, $result->getValue());
    }

    public function subtractionProvider(): array
    {
        return [
            'positive numbers' => [5.5, 2.5, '3'],
            'negative numbers' => [-1.5, -2.5, '1'],
            'mixed signs' => [1.5, -2.5, '4'],
            'zero subtraction' => [1.5, 0, '1.5'],
            'precision test' => [0.3, 0.1, '0.2'],
            // Scientific notation tests
            'scientific small numbers' => ['3.0e-5', '1.0e-5', '0.00002'],
            'scientific large numbers' => ['3.0e5', '1.0e5', '200000'],
            'scientific mixed scales' => ['3.0e5', '1.0e-5', '299999.99999'],
            'scientific negative numbers' => ['-3.0e-5', '-1.0e-5', '-0.00002'],
        ];
    }

    /**
     * @dataProvider multiplicationProvider
     */
    public function testMul(string|int|float $a, string|int|float $b, string $expected): void
    {
        $number = new DecimalNumber($a);
        $result = $number->mul($b);
        $this->assertEquals($expected, $result->getValue());
    }

    public function multiplicationProvider(): array
    {
        return [
            'positive numbers' => [2, 3, '6'],
            'negative numbers' => [-2, -3, '6'],
            'mixed signs' => [2, -3, '-6'],
            'zero multiplication' => [1.5, 0, '0'],
            'decimal multiplication' => [0.1, 0.2, '0.02'],
            // Scientific notation tests
            'scientific small numbers' => ['2.0e-4', '3.0e-4', '0.00000006'],
            'scientific large numbers' => ['2.0e4', '3.0e4', '600000000'],
            'scientific mixed scales' => ['2.0e4', '3.0e-4', '6'],
            'scientific negative numbers' => ['-2.0e-4', '-3.0e-4', '0.00000006'],
        ];
    }

    /**
     * @dataProvider divisionProvider
     */
    public function testDiv(string|int|float $a, string|int|float $b, string $expected): void
    {
        $number = new DecimalNumber($a);
        $result = $number->div($b);
        $this->assertEquals($expected, $result->getValue());
    }

    public function divisionProvider(): array
    {
        return [
            'positive numbers' => [6, 2, '3'],
            'negative numbers' => [-6, -2, '3'],
            'mixed signs' => [6, -2, '-3'],
            'decimal division' => [1, 2, '0.5'],
            'recurring decimal' => [1, 3, '0.33333333'],
            // Scientific notation tests
            'scientific small numbers' => ['2.0e-4', '4.0e-4', '0.5'],
            'scientific large numbers' => ['2.0e4', '4.0e4', '0.5'],
            'scientific mixed scales' => ['2.0e4', '4.0e-4', '50000000'],
            'scientific negative numbers' => ['-2.0e-4', '-4.0e-4', '0.5'],
        ];
    }

    public function testDivisionByZero(): void
    {
        $this->expectException(\DivisionByZeroError::class);
        $number = new DecimalNumber(1);
        $number->div(0);
    }

    /**
     * @dataProvider comparisonProvider
     */
    public function testComparisons(string|int|float $a, string|int|float $b, bool $expectedEquals, bool $expectedGreater, bool $expectedLess): void
    {
        $number = new DecimalNumber($a);
        $this->assertEquals($expectedEquals, $number->equals($b));
        $this->assertEquals($expectedGreater, $number->isGreaterThan($b));
        $this->assertEquals($expectedLess, $number->isLessThan($b));
    }

    public function comparisonProvider(): array
    {
        return [
            // Basic comparisons
            'equal numbers' => [1.5, 1.5, true, false, false],
            'greater than' => [2.5, 1.5, false, true, false],
            'less than' => [1.5, 2.5, false, false, true],
            'equal negative numbers' => [-1.5, -1.5, true, false, false],
            'greater than negative' => [2.5, -1.5, false, true, false],
            'greater negative than' => [-0.5, -1.5, false, true, false],
            'less negative than ' => [-2.5, 1.5, false, false, true],
            'less than negative' => [-1.5, -0.5, false, false, true],
            'zero comparison' => [0, 0, true, false, false],
            // Scientific notation comparisons
            'equal scientific' => ['1.0e-5', '1.0e-5', true, false, false],
            'greater scientific' => ['2.0e-5', '1.0e-5', false, true, false],
            'less scientific' => ['1.0e-5', '2.0e-5', false, false, true],
            'equal negative scientific' => ['-1.0e-5', '-1.0e-5', true, false, false],
            'mixed scales equal' => ['1.0e-8', '0.00000001', true, false, false],
            'mixed scales greater' => ['1.1e-7', '0.0000001', false, true, false],
            'mixed scales less' => ['0.9e-7', '0.0000001', false, false, true],
            // Scientific notation edge cases
            'scientific vs decimal equal boundary' => ['1.0e-8', '0.00000001', true, false, false],
            'scientific vs decimal near zero' => ['1.0e-9', '0', true, false, false],
            'scientific large vs decimal' => ['1.0e5', '100000', true, false, false],
            'scientific small vs zero' => ['1.0e-20', '0', true, false, false],
        ];
    }

    /**
     * @dataProvider roundingProvider
     */
    public function testRounding(string|int|float $input, int $precision, string $expectedRound): void
    {
        $number = new DecimalNumber($input);
        $this->assertEquals($expectedRound, $number->round($precision)->getValue());
    }

    public function roundingProvider(): array
    {
        return [
            'positive decimal' => [1.55, 1, '1.6'],
            'negative decimal' => [-1.55, 1, '-1.6'],
            'integer' => [2, 0, '2'],
            // Scientific notation tests
            'scientific small positive' => ['1.55e-5', 6, '0.000016'],
            'scientific small negative' => ['-1.55e-5', 6, '-0.000016'],
            'scientific large positive' => ['1.55e5', 0, '155000'],
            'scientific large negative' => ['-1.55e5', 0, '-155000'],
        ];
    }

    /**
     * @dataProvider dropProvider
     */
    public function testDrop(string|int|float $input, int $scale, string $expected): void
    {
        $number = new DecimalNumber($input);
        $this->assertEquals($expected, $number->drop($scale)->getValue());
    }

    public function dropProvider(): array
    {
        return [
            'positive integer' => [2, 0, '2'],
            'positive decimal' => [1.55, 0, '1'],
            'positive decimal with scale' => [1.55, 1, '1.5'],
            'positive decimal with larger scale' => [1.55, 2, '1.55'],
            'negative decimal' => [-1.55, 0, '-1'],
            'negative decimal with scale' => [-1.55, 1, '-1.5'],
            'negative decimal with larger scale' => [-1.55, 2, '-1.55'],
            'zero' => [0, 0, '0'],
            'zero with scale' => [0, 2, '0'],
            'small decimal' => [0.01234567, 4, '0.0123'],
            'small long decimal' => [0.01234567, 7, '0.0123456'],
            'recurring decimal' => [1/3, 2, '0.33'],
            // Scientific notation tests
            'scientific small positive' => ['1.23456e-5', 6, '0.000012'],
            'scientific small negative' => ['-1.23456e-5', 6, '-0.000012'],
            'scientific large positive' => ['1.23456e5', 2, '123456'],
            'scientific large negative' => ['-1.23456e5', 2, '-123456'],
            'scientific exact scale' => ['1.0e-8', 8, '0.00000001'],
            'scientific beyond scale' => ['1.0e-9', 8, '0'],
        ];
    }

    public function testDropWithNegativeScale(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Scale must be a non-negative integer');
        $number = new DecimalNumber(1.55);
        $number->drop(-1);
    }

    /**
     * @dataProvider absProvider
     */
    public function testAbs(string|int|float $input, string $expected): void
    {
        $number = new DecimalNumber($input);
        $this->assertEquals($expected, $number->abs()->getValue());
    }

    public function absProvider(): array
    {
        return [
            'positive number' => [1.5, '1.5'],
            'negative number' => [-1.5, '1.5'],
            'zero' => [0, '0'],
            // Scientific notation tests
            'positive scientific small' => ['1.23e-5', '0.0000123'],
            'negative scientific small' => ['-1.23e-5', '0.0000123'],
            'positive scientific large' => ['1.23e5', '123000'],
            'negative scientific large' => ['-1.23e5', '123000'],
        ];
    }

    /**
     * @dataProvider floatProvider
     */
    public function testFloat(string|int|float $input, float $expected, string $expectedString): void
    {
        $number = new DecimalNumber($input);
        $this->assertEquals($expected, $number->float());
        $this->assertEquals($expectedString, (string) $number->float());
    }

    public function floatProvider(): array
    {
        return [
            'integer' => [123, 123.0, '123'],
            'float' => [123.456, 123.456, '123.456'],
            'string' => ['123.456', 123.456, '123.456'],
            'zero' => [0, 0.0, '0'],
            'negative' => [-123.456, -123.456, '-123.456'],
            'large number' => [12345678999999.0, 12345678999999.0, '12345678999999'],
            'large precision' => [1.012345678999999, 1.01234568, '1.01234568'],
            'trailing zeros' => [123.4500, 123.45, '123.45'],
            'zero decimal' => [123.0, 123.0, '123'],
            'leading zeros integer' => ['00123', 123.0, '123'],
            'leading zeros decimal' => ['00123.456', 123.456, '123.456'],
            'leading zeros after decimal' => ['123.0456', 123.0456, '123.0456'],
            'negative with leading zeros' => ['-00123.456', -123.456, '-123.456'],
            'zero with leading zeros' => ['00.123', 0.123, '0.123'],
            'negative zero with leading zeros' => ['-00.123', -0.123, '-0.123'],
            'multiple leading zeros' => ['000000123', 123.0, '123'],
            'zero with multiple leading zeros' => ['000000', 0.0, '0'],
            'decimal with 1 digit precision' => [0.1, 0.1, '0.1'],
            'decimal with 2 digits precision' => [0.01, 0.01, '0.01'],
            'decimal with 3 digits precision' => [0.001, 0.001, '0.001'],
            'decimal with 4 digits precision' => [0.0001, 0.0001, '0.0001'],
            'decimal with 5 digits precision' => [0.00001, 0.00001, '1.0E-5'],
            'decimal with 6 digits precision' => [0.000001, 0.000001, '1.0E-6'],
            'decimal with 7 digits precision' => [0.0000001, 0.0000001, '1.0E-7'],
            'decimal with 8 digits precision' => [0.00000001, 0.00000001, '1.0E-8'],
            'decimal with 9 digits precision' => [0.000000001, 0.0, '0'],
            'recurring decimal' => [1/3, 0.33333333, '0.33333333'],
            // Scientific notation tests
            'positive scientific small' => ['1.23e-5', 0.0000123, '1.23E-5'],
            'positive scientific large' => ['1.23e5', 123000.0, '123000'],
            'negative scientific small' => ['-1.23e-5', -0.0000123, '-1.23E-5'],
            'negative scientific large' => ['-1.23e5', -123000.0, '-123000'],
            'scientific zero exponent' => ['1.23e0', 1.23, '1.23'],
            'scientific exact scale' => ['1.0e-8', 0.00000001, '1.0E-8'],
            'scientific beyond scale' => ['1.0e-9', 0.0, '0'],
            'scientific large precision' => ['1.23456789e-5', 0.00001234, '1.234E-5'],
        ];
    }

    /**
     * @dataProvider scientificNotationProvider
     */
    public function testScientificNotationHandling(string $input, string $expected): void
    {
        $number = new DecimalNumber($input);
        $this->assertEquals($expected, $number->getValue());
        
        // Test that the value remains stable after operations
        $this->assertEquals($expected, $number->add(0)->getValue());
        $this->assertEquals($expected, $number->sub(0)->getValue());
        $this->assertEquals($expected, $number->mul(1)->getValue());
        if ($number->getValue() !== '0') {
            $this->assertEquals($expected, $number->div(1)->getValue());
        }
    }

    public function scientificNotationProvider(): array
    {
        return [
            'basic positive' => ['1.23e-5', '0.0000123'],
            'basic negative' => ['-1.23e-5', '-0.0000123'],
            'zero exponent' => ['1.23e0', '1.23'],
            'positive exponent' => ['1.23e2', '123'],
            'negative exponent' => ['1.23e-2', '0.0123'],
            'exact scale' => ['1.0e-8', '0.00000001'],
            'beyond scale' => ['1.0e-9', '0'],
            'uppercase E' => ['1.23E-5', '0.0000123'],
            'positive sign' => ['1.23e+5', '123000'],
            'integer base' => ['1e-5', '0.00001'],
            'zero base' => ['0e-5', '0'],
            'negative zero base' => ['-0e-5', '0'],
            'large exponent' => ['1.23e20', '123000000000000000000'],
            'small exponent' => ['1.23e-20', '0'],
            'boundary case' => ['9.99999999e-9', '0'],
        ];
    }
}
