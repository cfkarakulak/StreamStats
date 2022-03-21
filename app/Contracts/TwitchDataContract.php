<?php

namespace App\Contracts;

interface TwitchDataContract
{
    public function getStreams();
    public function storeStreams(array $streams, int $count);
    public function storeStreamTags(array $tags, int $count);
    public function getGameStreamsGrouped();
    public function getGameStreamsByViewers();
    public function getTopStreamsByViewers();
    public function getStreamTags();
    public function getStreamsByNearestHours();
    public function getStreamTagsRespectiveNames(array $tags);
}
