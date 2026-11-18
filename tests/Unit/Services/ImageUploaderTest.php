<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\ValidationException;
use App\Services\ImageUploader;
use PHPUnit\Framework\TestCase;

final class ImageUploaderTest extends TestCase
{
    /** A valid 1x1 pixel PNG image. */
    private const TINY_PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    private ImageUploader $uploader;
    private array $temporaryFiles = [];

    protected function setUp(): void
    {
        $this->uploader = new ImageUploader(
            sys_get_temp_dir() . '/gymshop-uploads-test',
            'uploads/products',
            1024 * 1024,
            ['jpg', 'jpeg', 'png', 'webp']
        );
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function createTemporaryFile(string $content): string
    {
        $file = tempnam(sys_get_temp_dir(), 'gymshop');
        file_put_contents($file, $content);
        $this->temporaryFiles[] = $file;

        return $file;
    }

    private function createUpload(string $name, string $content, ?int $size = null, int $error = UPLOAD_ERR_OK): array
    {
        $temporaryFile = $this->createTemporaryFile($content);

        return [
            'name' => $name,
            'tmp_name' => $temporaryFile,
            'size' => $size ?? filesize($temporaryFile),
            'error' => $error,
        ];
    }

    public function testAcceptsValidPngImage(): void
    {
        $upload = $this->createUpload('creatina.PNG', base64_decode(self::TINY_PNG));

        $this->assertSame('png', $this->uploader->validate($upload));
    }

    public function testRejectsFilesLargerThanTheLimit(): void
    {
        $upload = $this->createUpload('creatina.png', base64_decode(self::TINY_PNG), 2 * 1024 * 1024);

        $this->expectException(ValidationException::class);
        $this->uploader->validate($upload);
    }

    public function testServerSizeLimitErrorIsReportedAsTooLarge(): void
    {
        $upload = $this->createUpload('creatina.png', '', 0, UPLOAD_ERR_INI_SIZE);

        try {
            $this->uploader->validate($upload);
            $this->fail('ValidationException was not thrown.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('MB', $exception->getErrors()['image']);
        }
    }

    public function testRejectsExtensionsThatAreNotAllowed(): void
    {
        $upload = $this->createUpload('shell.php', base64_decode(self::TINY_PNG));

        $this->expectException(ValidationException::class);
        $this->uploader->validate($upload);
    }

    public function testRejectsFilesWhoseContentIsNotAnImage(): void
    {
        $upload = $this->createUpload('photo.jpg', '<?php echo "not an image"; ?>');

        $this->expectException(ValidationException::class);
        $this->uploader->validate($upload);
    }
}
