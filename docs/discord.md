# Discord setup

## Step 1: Create a Discord bot

1. Create a application at https://discord.com/developers/applications
2. Under Installation
    1. Set the installation context to Guild Install only
    2. Set the install link to none
3. Under OAuth2,
    1. add a redirect URL of `http://localhost/login/discord/callback`
    2. Copy the client ID and client secret to your .env fil
4. Under Bot,
    1. Copy the token to your .env file
    2. Set public bot to false
    3. For privileged gateway intents, enable the following:
        1. Server Members Intent
        2. Presence Intent

## Step 2: Create a server

1. Create a discord server you can use for testing the bot.
   2Add the guild id to your .env file. You can get this by enabling developer mode in discord settings and right
   clicking on the server.

## Step 3: Invite the bot to your server

1. Login to the application and navigate to [localhost/admin/discord/connect](http://localhost/admin/discord/connect)
2. Go through the OAuth2 flow to invite the bot to your server.



