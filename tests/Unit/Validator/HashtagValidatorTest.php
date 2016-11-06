<?php

namespace AGuardia\Unit\Validator;

use AGuardia\Validator\HashtagValidator;
use PHPUnit\Framework\TestCase;

class HashtagValidatorTest extends TestCase
{
    /* @var HashtagValidator */
    private $validator;

    public function setUp()
    {
        $this->validator = new HashtagValidator();

        parent::setUp();
    }

    public function testValidMessage()
    {
        $this->assertTrue($this->validator->validate('[#hashtag #yo] this is a regular message.'));
        $this->assertTrue($this->validator->validate('#hashtag #yo this is a regular message.'));
    }

    public function testNotValidMessage()
    {
        $this->assertFalse($this->validator->validate('this is not a regular message. I will be banned :('));
        $this->assertFalse($this->validator->validate('this is not a regular message. #evenIfIAddThis I will be banned :('));
        $this->assertFalse($this->validator->validate('this is not a regular message. I will be banned :( #endingHashtagSucks'));
    }
}
