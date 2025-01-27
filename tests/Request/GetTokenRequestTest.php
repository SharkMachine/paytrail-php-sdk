<?php

declare(strict_types=1);

namespace Tests\Request;

use Paytrail\SDK\Exception\ValidationException;
use Paytrail\SDK\Request\GetTokenRequest;
use PHPUnit\Framework\TestCase;

class GetTokenRequestTest extends TestCase
{
    public function validationProvider()
    {
        return [
            'checkout-tokenization-id is empty' => [
                [
                    'checkoutTokenizationId' => ''
                ],
                'checkout-tokenization-id is empty'
            ]
        ];
    }

    public function testGetTokenRequest()
    {
        $getTokenRequest = new GetTokenRequest();
        $getTokenRequest->setCheckoutTokenizationId('818c478e-5682-46bf-97fd-b9c2b93a3fcd');

        $this->assertInstanceOf(GetTokenRequest::class, $getTokenRequest);
        $this->assertEquals(true, $getTokenRequest->validate());
    }

    /**
     * @dataProvider validationProvider
     */
    public function testGetTokenRequestValidationExceptionMessages($properties, $exceptionMessage)
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $getTokenRequest = new GetTokenRequest();

        foreach ($properties as $property => $value) {
            $this->setPrivateProperty($getTokenRequest, $property, $value);
        }

        $getTokenRequest->validate();
    }

    public function setPrivateProperty($class, $propertyName, $value)
    {
        $reflector = new \ReflectionClass($class);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($class, $value);
    }
}
