<?php

namespace App\Infrastructure\Cache;

class CacheKeyListEnum
{
    public const DEVICE_TOPICS_LIST = 'topicList'; // массив топиков с их значением и последней датой обновления
    public const DEVICE_TOPIC_BY_TYPE = 'topicsByType';
    public const DEVICE_MAP_CACHE = 'device_map';
}
