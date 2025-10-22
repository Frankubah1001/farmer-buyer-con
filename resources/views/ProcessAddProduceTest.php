<?php

use PHPUnit\Framework\TestCase;

// Include the file to be tested.
// As the functions are in the global scope, we can call them directly.
require_once __DIR__ . '/../resources/views/process_add_produce.php';

class ProcessAddProduceTest extends TestCase
{
    private $uploadDir = 'test_uploads/';

    /**
     * Set up a temporary directory for uploads before each test.
     */
    protected function setUp(): void
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    /**
     * Clean up the temporary directory after each test.
     */
    protected function tearDown(): void
    {
        if (is_dir($this->uploadDir)) {
            // A more robust cleanup would recursively delete files and directories
            $files = glob($this->uploadDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->uploadDir);
        }
    }

    /**
     * @dataProvider formatBytesProvider
     */
    public function testFormatBytes($bytes, $precision, $expected)
    {
        $this->assertEquals($expected, formatBytes($bytes, $precision));
    }

    public function formatBytesProvider()
    {
        return [
            'zero bytes' => [0, 2, '0 B'],
            'bytes' => [500, 2, '500 B'],
            'kilobytes' => [1024, 2, '1 KB'],
            'kilobytes with precision' => [1536, 2, '1.5 KB'],
            'megabytes' => [1048576, 2, '1 MB'],
            'gigabytes' => [1073741824, 2, '1 GB'],
            'terabytes' => [1099511627776, 2, '1 TB'],
        ];
    }

    public function testUploadImagesSuccess()
    {
        // 1. Create a dummy file
        $dummyFileName = 'test_image.jpg';
        $dummyFilePath = $this->uploadDir . $dummyFileName;
        file_put_contents($dummyFilePath, 'dummy image content');

        // 2. Mock the $_FILES superglobal
        $files = [
            'name' => [$dummyFileName],
            'type' => ['image/jpeg'],
            'tmp_name' => [$dummyFilePath],
            'error' => [UPLOAD_ERR_OK],
            'size' => [filesize($dummyFilePath)]
        ];

        // 3. Call the function
        // Note: This is more of an integration test as it interacts with the filesystem.
        // We are also "uploading" from the same directory we are moving to, which isn't realistic
        // but allows us to test the `move_uploaded_file` path. For a true unit test,
        // `move_uploaded_file` would be mocked.
        $result = uploadImages($files, $this->uploadDir);

        // 4. Assertions
        $this->assertEquals('success', $result['status']);
        $this->assertCount(1, $result['paths']);
        $this->assertFileExists($result['paths'][0]);

        // 5. Cleanup the moved file
        unlink($result['paths'][0]);
    }

    public function testUploadImagesFailsWithInvalidType()
    {
        $dummyFileName = 'test_document.txt';
        $dummyFilePath = $this->uploadDir . $dummyFileName;
        file_put_contents($dummyFilePath, 'dummy text content');

        $files = [
            'name' => [$dummyFileName],
            'type' => ['text/plain'],
            'tmp_name' => [$dummyFilePath],
            'error' => [UPLOAD_ERR_OK],
            'size' => [filesize($dummyFilePath)]
        ];

        $result = uploadImages($files, $this->uploadDir);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('Invalid file type', $result['message']);

        unlink($dummyFilePath);
    }

    public function testUploadImagesFailsWithExceededSize()
    {
        $dummyFileName = 'large_image.jpg';
        $dummyFilePath = $this->uploadDir . $dummyFileName;
        file_put_contents($dummyFilePath, 'dummy image content');

        $files = [
            'name' => [$dummyFileName],
            'type' => ['image/jpeg'],
            'tmp_name' => [$dummyFilePath],
            'error' => [UPLOAD_ERR_OK],
            'size' => [3 * 1024 * 1024] // 3MB, which is > 2MB max
        ];

        $result = uploadImages($files, $this->uploadDir);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('File size exceeds the limit', $result['message']);

        unlink($dummyFilePath);
    }

    public function testUploadImagesFailsWithNonWritableDirectory()
    {
        // This test requires permissions to be set up correctly.
        // On Unix-like systems:
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $nonWritableDir = $this->uploadDir . 'non_writable';
            mkdir($nonWritableDir, 0444, true); // Read-only

            $files = [
                'name' => [], 'type' => [], 'tmp_name' => [], 'error' => [], 'size' => []
            ];

            $result = uploadImages($files, $nonWritableDir);

            $this->assertEquals('error', $result['status']);
            $this->assertEquals('Upload directory is not writable.', $result['message']);

            // Cleanup
            rmdir($nonWritableDir);
        } else {
            $this->markTestSkipped('Cannot test non-writable directories on Windows easily.');
        }
    }
}