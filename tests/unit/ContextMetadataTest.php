<?php

namespace LightService\Tests\Unit;

use PHPUnit\Framework\TestCase;
use LightService\ContextMetadata;

final class ContextMetadataTest extends TestCase
{
    public function testItIsInstantiatedWithTheDefaultMetadataValues()
    {
        $context_metadata = new ContextMetadata();

        foreach (ContextMetadata::DEFAULT_METADATA as $metadata => $default) {
            $this->assertEquals($default, $context_metadata->$metadata);
        }
    }

    public function testItCanBeConvertToAnArray()
    {
        $context_metadata = new ContextMetadata();

        $this->assertEquals(ContextMetadata::DEFAULT_METADATA, $context_metadata->toArray());
    }

    public function testItCanMarkTheFailureFlagAsTrue()
    {
        $context_metadata = new ContextMetadata();

        $context_metadata->fail();

        $this->assertTrue($context_metadata->failure);
    }

    public function testItCanMarkTheFailureFlagAsTrueWithAMessage()
    {
        $context_metadata = new ContextMetadata();

        $context_metadata->fail('Foo');

        $this->assertEquals('Foo', $context_metadata->message);
    }

    public function testItCanMarkTheFailureFlagAsTrueWithAMessageAndAnErrorCode()
    {
        $context_metadata = new ContextMetadata();

        $context_metadata->fail('Foo', 100);

        $this->assertEquals('Foo', $context_metadata->message);
        $this->assertEquals(100, $context_metadata->error_code);
    }

    public function testItReturnsAValueOfNullWhenThePropertyBeingFetchedDoesNotExistInTheContextMetadata()
    {
        $context = new ContextMetadata();

        $this->assertNull($context->b);
    }
}
