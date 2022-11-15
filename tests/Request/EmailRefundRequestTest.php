<?php

declare(strict_types=1);

namespace Tests\Request;

use Paytrail\SDK\Exception\ValidationException;
use Paytrail\SDK\Model\CallbackUrl;
use Paytrail\SDK\Model\RefundItem;
use Paytrail\SDK\Request\EmailRefundRequest;
use PHPUnit\Framework\TestCase;

class EmailRefundRequestTest extends TestCase
{
    public function testEmailRefundRequest()
    {
        $er = new EmailRefundRequest();
        $er->setAmount(20);
        $er->setEmail('some@email.com');

        $item = new RefundItem();
        $item->setAmount(10)
            ->setStamp('someStamp');

        $item2 = new RefundItem();
        $item2->setAmount(10)
            ->setStamp('anotherStamp');

        $er->setItems([$item, $item2]);

        $cb = new CallbackUrl();
        $cb->setCancel('https://some.url.com/cancel')
            ->setSuccess('https://some.url.com/success');

        $er->setCallbackUrls($cb);

        $er->setRefundReference('ref-1234')
            ->setRefundStamp('c7557cd5d5f548daa5332ccc4abb264f');

        $this->assertEquals(true, $er->validate());
    }

    public function testExceptions()
    {
        $er = new EmailRefundRequest();

        try {
            $er->validate();
        } catch (ValidationException $e) {
            $this->assertEquals("Amount can not be empty", $e->getMessage());
        }

        $er->setAmount(99999);

        try {
            $er->validate();
        } catch (ValidationException $e) {
            $this->assertEquals('CallbackUrls are not set', $e->getMessage());
        }

        $cb = new CallbackUrl();
        $er->setCallbackUrls($cb);

        try {
            $er->validate();
        } catch (ValidationException $e) {
            $this->assertEquals('Success is empty', $e->getMessage());
        }

        $cb->setSuccess('someurl.somewhere.com/success');
        $er->setCallbackUrls($cb);

        try {
            $er->validate();
        } catch (ValidationException $e) {
            $this->assertEquals('Cancel is empty', $e->getMessage());
        }

        $cb->setCancel('someurl.somewhere.com/cancel');
        $er->setCallbackUrls($cb);

        try {
            $er->validate();
        } catch (ValidationException $e) {
            $this->assertEquals('Success is not a valid URL', $e->getMessage());
        }

        $cb->setSuccess('https://someurl.somewhere.com/success');
        $er->setCallbackUrls($cb);

        try {
            $er->validate();
        } catch (ValidationException $e) {
            $this->assertEquals('Cancel is not a valid URL', $e->getMessage());
        }

        $cb->setCancel('https://someurl.somewhere.com/cancel');
        $er->setCallbackUrls($cb);

        try {
            $er->validate();
        } catch (ValidationException $e) {
            $this->assertEquals('email can not be empty', $e->getMessage());
        }

        $er->setEmail('some@email.com');

        // Items are not mandatory, so should pass from here
        try {
            $er->validate();
        } catch (ValidationException $e) {
            var_dump($e->getMessage());
        }

        $item = new RefundItem();
        $item->setAmount(110)
            ->setStamp('someStamp');

        $item2 = new RefundItem();
        $item2->setAmount(10)
            ->setStamp('anotherStamp');

        $er->setItems([$item, $item2]);

        // Fails, as refund->total was set to 9999
        try {
            $er->validate();
        } catch (ValidationException $e) {
            $this->assertEquals('ItemsTotal does not match Amount', $e->getMessage());
        }

        // Set correct amount
        $er->setAmount(120);

        try {
            $this->assertEquals(true, $er->validate());
        } catch (ValidationException $e) {
        }
    }
}
