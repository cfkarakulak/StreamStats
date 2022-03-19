<?php

namespace App\Contracts;

interface TwitchUserContract
{
    public function getAccessToken(string $code);
    public function getUser(string $token);
    public function saveUser(array $data);
    public function getUsersFollowedStreams(string $token, int $user_id);
}
