<?php


namespace App\Infrastructure\Mqtt\Service;


use App\Domain\Contract\Device\ValidationDeviceService;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Domain\Doctrine\Sensor\Service\SensorValidationService;
use App\Domain\Payload\Dto\MessageDto;
use App\Infrastructure\Cache\CacheKeyList;
use App\Infrastructure\Cache\CacheService;
use DateInterval;
use Mosquitto\Message;
use Psr\Cache\CacheItemInterface;

class MqttService
{
    public function __construct(private CacheService $cache)
    {
    }

    private const MAP = [
        Sensor::class => SensorValidationService::class
    ];

    private MessageDto $message;

    public function route(Message $message)
    {
        $this->setMessage($message);

        foreach ($this->getModulesRoutes() as $entity => $rout) {
            if (in_array($this->message->getTopic(), $rout)) {
                /** @var ValidationDeviceService $class */
                $class = self::MAP[$entity];
                dd((new $class)->validate($this->message));
            }
        }
    }

    private function setMessage(Message $message): void
    {
        $this->message = new MessageDto(topic: $message->topic, payload: $message->payload);
    }

    /**
     * @return string[][]
     */
    private function getModulesRoutes(): array
    {
        $this->cache->clearAll();

        $value = 'test_value';
        $tags = ['bar'];
        $lifetime = 0;

        $t = $this->cache->getOrSet(
            CacheKeyList::DEVICE_TOPICS_BY_TYPE,
            function (CacheItemInterface $item) use ($value, $tags, $lifetime)
            {
                $item->set($value);

                if ($tags !== null) {
                    $item->tag($tags);
                }

                if ($lifetime !== 0) {
                    $item->expiresAfter(new DateInterval("PT{$lifetime}S"));
                }

                return $item->get();
            }
        );

        dd($t);

        // get Cache all topics by device type
//        $this->cache->set('dd23', 'ttd3', ['as'], 10);
//        $cacher = $this->cache->get('dd23');

        return [
            Sensor::class => [
                'margulis/temperature',
                'margulis/humidity',
                'home/restroom/temperature',
                'home/kitchen/temperature',
                'home/hall/temperature'
            ],
//            Relay::class => [
//                'margulis/check/lamp01',
//                'home/check/ralay01',
//                'home/check/ralay02',
//            ],
        ];
    }
}