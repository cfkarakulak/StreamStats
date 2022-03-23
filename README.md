![1](https://user-images.githubusercontent.com/15141224/159723238-dc2bafdf-61a6-446c-a938-c27494d7dba2.png)


### Demo

https://user-images.githubusercontent.com/15141224/159342218-c8632b57-a734-4418-b8d1-fd7945ac18f6.mp4


The whole project can be deployed if you wish to see it on action.

### Some Notes

The project requires that you have PHP8.0 or above, If you wish this to be changed, please let me know.  

After composer installs

`./vendor/bin/sail up`

`./vendor/bin/sail artisan schedule:work`

Also

`npm i && npm build`

### Manual Invoking

`./vendor/bin/sail artisan twitch:seed`

### Environment Variables

```
TWITCH_API_ID=https://id.twitch.tv/oauth2
TWITCH_API_HELIX=https://api.twitch.tv/helix
TWITCH_CLIENT_ID=xxx
TWITCH_CLIENT_SECRET=xxx
TWITCH_APP_ACCESS_TOKEN=xxx
TWITCH_REDIRECT_URI=http://localhost/login
```
