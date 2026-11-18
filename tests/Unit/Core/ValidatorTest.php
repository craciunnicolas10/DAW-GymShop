<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Validator;
use App\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testRequiredFailsForEmptyOrBlankValues(): void
    {
        $validator = (new Validator(['name' => '   ']))
            ->required('name', 'Nume')
            ->required('email', 'Email');

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->getErrors());
        $this->assertArrayHasKey('email', $validator->getErrors());
    }

    public function testEmailRuleRejectsInvalidAddress(): void
    {
        $validator = (new Validator(['email' => 'not-an-email']))->email('email', 'Email');

        $this->assertArrayHasKey('email', $validator->getErrors());
    }

    public function testValidDataPasses(): void
    {
        $validator = (new Validator([
            'email' => 'client@gymshop.test',
            'password' => 'Secret123',
            'passwordConfirmation' => 'Secret123',
            'stock' => '15',
        ]))
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->minLength('password', 'Parolă', 8)
            ->matches('passwordConfirmation', 'password', 'Confirmare')
            ->integer('stock', 'Stoc', 0, 100);

        $this->assertTrue($validator->passes());
        $this->assertSame([], $validator->getErrors());
    }

    public function testIntegerRuleChecksRange(): void
    {
        $validator = (new Validator(['a' => '11', 'b' => 'abc', 'c' => '5']))
            ->integer('a', 'A', 1, 10)
            ->integer('b', 'B', 1, 10)
            ->integer('c', 'C', 1, 10);

        $this->assertSame(['a', 'b'], array_keys($validator->getErrors()));
    }

    public function testOnlyTheFirstErrorOfAFieldIsKept(): void
    {
        $validator = (new Validator(['password' => 'ab']))
            ->minLength('password', 'Parolă', 8)
            ->pattern('password', '/\d/', 'Parola trebuie să conțină o cifră.');

        $this->assertStringContainsString('cel puțin 8', $validator->getErrors()['password']);
    }

    public function testValidateThrowsExceptionWithErrors(): void
    {
        try {
            (new Validator(['price' => '-5']))->number('price', 'Preț', 0.01)->validate();
            $this->fail('ValidationException was not thrown.');
        } catch (ValidationException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertArrayHasKey('price', $exception->getErrors());
        }
    }
}
