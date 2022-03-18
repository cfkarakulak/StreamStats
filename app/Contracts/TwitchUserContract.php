<?php

namespace App\Contracts;

interface TwitchUserContract
{
    public function getAccessToken(string $code);
    public function getUser(string $token);
    public function storeUser(array $data);
    public function getUsersFollowedStreams(string $token, int $user_id);
}
