<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Str;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
    public function testSlugReplacesRomanianCharacters(): void
    {
        $this->assertSame('creatina-monohidrat-300g', Str::slug('Creatină Monohidrat 300g'));
        $this->assertSame('strudel-tara-si-paine', Str::slug('Ștrudel Țară și Pâine'));
    }

    public function testSlugRemovesSpecialCharactersAndExtraDashes(): void
    {
        $this->assertSame('whey-protein-2kg', Str::slug('  Whey -- Protein!! (2kg) '));
    }

    public function testSlugOfTextWithoutLettersIsEmpty(): void
    {
        $this->assertSame('', Str::slug('!!! ???'));
    }
}
