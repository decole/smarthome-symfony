<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

enum CacheKeyListEnum: string
{
    case DEVICE_TOPICS_LIST = 'topicList'; // массив топиков с их значением и последней датой обновления
    case DEVICE_TOPIC_BY_TYPE = 'topicsByType';
    case DEVICE_MAP_CACHE = 'deviceMap';
    case DISCORD_SENT_MESSAGE_TRIGGER = 'discordMessage';
}