<?php

namespace App\Tests\api\SmartHomeDevice;

use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\Payload\Entity\DevicePayload;
use App\Tests\_support\Step\Api\DeviceDataStep;

class DataDeviceByTopicsApiControllerCest
{
    public function positiveGetTopic(DeviceDataStep $I): void
    {
        $topic = $I->faker()->word();
        $payload = $I->faker()->word();
        $message = new DevicePayload(topic: $topic, payload: $payload);

        /** @var DeviceDataCacheService $service */
        $service = $I->grabService(DeviceDataCacheService::class);
        $service->save($message);

        $topics = $topic;
        $I->deviceTopicsList($topics);
        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            $topic => $payload,
        ]);
    }

    public function positiveGetTopics(DeviceDataStep $I): void
    {
        $topicOne = $I->faker()->word();
        $payloadOne = $I->faker()->word();
        $topicTwo = $I->faker()->word();
        $payloadTwo = $I->faker()->word();

        $messageOne = new DevicePayload(topic: $topicOne, payload: $payloadOne);
        $messageTwo = new DevicePayload(topic: $topicTwo, payload: $payloadTwo);

        /** @var DeviceDataCacheService $service */
        $service = $I->grabService(DeviceDataCacheService::class);
        $service->save($messageOne);
        $service->save($messageTwo);

        $topics = "{$topicOne},$topicTwo";
        $I->deviceTopicsList($topics);
        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson([
            $topicOne => $payloadOne,
        ]);
        $I->seeResponseContainsJson([
            $topicTwo => $payloadTwo,
        ]);
    }

    public function negativeGetTopics(DeviceDataStep $I): void
    {
        $topics = null;

        $I->deviceTopicsList($topics);

        $I->seeResponseIsException();
        $I->seeResponseContainsJson([
            'error' => 'empty topics',
        ]);
    }
}