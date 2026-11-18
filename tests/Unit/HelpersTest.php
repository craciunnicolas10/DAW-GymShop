<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\AuthorizationException;
use App\Exceptions\DatabaseException;
use App\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testEscapeFunctionNeutralizesHtml(): void
    {
        $this->assertSame(
            '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;',
            e('<script>alert("x")</script>')
        );
    }

    public function testUrlUsesTheConfiguredBaseUrl(): void
    {
        $this->assertSame('/gymshop/products', url('/products'));
        $this->assertSame('/gymshop/', url('/'));
    }

    public function testFormatPrice(): void
    {
        $this->assertSame('89,99 lei', formatPrice(89.99));
    }

    public function testExceptionsHaveTheRightHttpStatusCodes(): void
    {
        $this->assertSame(404, (new NotFoundException())->getStatusCode());
        $this->assertSame(403, (new AuthorizationException())->getStatusCode());
        $this->assertSame(500, (new DatabaseException('error'))->getStatusCode());
    }
}
