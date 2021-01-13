<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Tests\Unit\API;

use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerBuilder;
use AlmaviaCX\Calameo\API\Serializer;
use AlmaviaCX\Calameo\API\Value\Response\ResponseContent;
use PHPUnit\Framework\TestCase;

abstract class SerializerTest extends TestCase
{
    /**
     * @param string $json
     * @param string $expectedStatus
     * @param string $expectedResponseContentType
     * @return ResponseContent
     */
    protected function testDeserializeResponse(
        string $json,
        string $expectedStatus,
        string $expectedResponseContentType
    ): ResponseContent {
        $response = new Response(
            200,
            [],
            $json
        );

        $builder = new SerializerBuilder();

        $baseSerializer = $builder->build();
        $serializer = new Serializer($baseSerializer);

        //        $serializer = $this->createMock(Serializer::class);
        $response = $serializer->deserializeResponse(
            $response,
            $expectedResponseContentType
        );
        self::assertEquals(
            $expectedStatus,
            $response->status
        );
        self::assertInstanceOf(
            $expectedResponseContentType,
            $response->content
        );
        return $response->content;
    }
}
