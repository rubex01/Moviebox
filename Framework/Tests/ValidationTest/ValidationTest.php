<?php

declare(strict_types=1);

namespace Framework\Tests\ValidationTest;

include __DIR__ . '/../../autoload.php';

use PHPUnit\Framework\TestCase;
use Framework\Validation\Validation;

final class ValidationTest extends TestCase
{
    /**
     * Test validation methods with wrong input
     * 
     * @return void
     */
    public function testValidationWorkingWrongInput(): void
    {
        $testData = json_decode(file_get_contents(__DIR__ . '/ValidationPostDataWrong.json'), true);
        $assertData = json_decode(file_get_contents(__DIR__ . '/ValidationAssertData.json'), true);

        $validation = new Validation(
            $testData['input'],
            $testData['filters'],
            $testData['customMessages']
        );
        
        $this->assertSame($assertData, $validation->validationErrors);
    }

    /**
     * Test validation methods with right input
     * 
     * @return void
     */
    public function testValidationWorkingRightInput(): void
    {
        $testData = json_decode(file_get_contents(__DIR__ . '/ValidationPostDataCorrect.json'), true);

        $validation = new Validation(
            $testData['input'],
            $testData['filters']
        );

        $this->assertSame([], $validation->validationErrors);
    }
}
