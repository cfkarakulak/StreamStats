### Demo

https://user-images.githubusercontent.com/15141224/159342218-c8632b57-a734-4418-b8d1-fd7945ac18f6.mp4


The whole project can be deployed if you wish to see it on action.

### Some Notes

The project requires that you have PHP8.0 or above, If you wish this to be changed, please let me know.  

`./vendor/bin/sail up`

`./vendor/bin/sail artisan schedule:work`

### Manual Invoking

`./vendor/bin/sail artisan twitch:seed`

### Environment Variables

```
TWITCH_API_ID=https://id.twitch.tv/oauth2
TWITCH_API_HELIX=https://api.twitch.tv/helix
TWITCH_CLIENT_ID=zf4lxk9cqqf9qnwpb700r5swgvwfh2
TWITCH_CLIENT_SECRET=c2z4tbbjxuwbh0eyz7p26gbimaqdar
TWITCH_APP_ACCESS_TOKEN=92xpbr5cvk87hlcfuuh3xub5319t19
TWITCH_REDIRECT_URI=http://localhost/login
```
