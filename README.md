# Shadowverse BOT for LINE

[![Video Image](https://i.imgur.com/wmtypkil.jpg)](https://www.youtube.com/watch?v=xLSZYfAyX3Q "LINE Gobu Trailer")

"Gobu" is a bot that will help you get any information from the Shadowverse card. 

## Getting Started

Before you can invite the bot to your room, you need to add her as a friend first via this ID : @cyz7687s <br>After that you can invite the bot to your room and all the members in that room will be able to use the bot without being a friend first (although it would be great if you do ^^)

## Command List

***
### Main Modules : Shadowverse Database
***
All command started with `..` followed by what you want to do. The full list of command is as follows :

* `..find [criteria to search]` - to search for a card based on the inputted criteria.  
* `..name [name]` - search a card stats using their name.
* `..flair [name]` - flair of the card.  
* `..img [name]` - card artwork. 
* `..imgevo [name]` - card artwork (evolved). 
* `..alt [name]` - alternate card artwork.
* `..altevo [name]` - alternate card artwork (evolved).
* `..raw [card name]` - borderless card artwork.
* `..rawevo [card name]` - borderless card artwork (evolved).
* `..rawalt [card name]` - borderless alternate card artwork.
* `..rawaltevo [card name]` - borderless alternate card artwork (evolved). 

#### Beta Function - Might not works sometimes
* `..voice [eng/jpn/kor] [atk/play/evo/die] [card name]` - basic voice function, still needs a lot of improvement !  

#### Disabled Function
* `..ani [name]` - animated card artwork. 
* `..anievo [name]` - animated card artwork (evolved).

<br>Notes : 
<br>- When multiple result found, all command will return the list of possible card and not their respective command.
<br>- The list of possible card will be returned in carousel type message when possible (between 2 - 5) for easier search.
<br>- Type `..help` to see some examples of command function.
***
### Additional Modules #1 - Urban Dictionary
***

This is the function that's used to access Urban Dictionary via Gobu 

* `..ud [words]` - Returns explanation of that words
* `..explain [words]` - Same as `..ud`
* `..random` - Returns a random explanation 

***
### Game Modules
***

Besides functioning as data fetcher for Shadowverse and Urban Dictionary, LINE Gobu also includes some mini-games.
These mini games includes :

#### General Purpose

* `..daily` - Gives you a one-time salary per day based on how many waifus you have. The formula is `2500 * (1 * n)`

* `..rank` - Shows the top user with the highest point in their account 

#### Virtual Casino

* `..slots [number]` - A gambling mini-games with pure 50/50 chance of winning/losing ! The amount of prize is determined by room casino. 

* `..casino` - Check the status of casino for slot prize pool. Includes info such as casino level (determined the daily prize Casino hold), EXP to level up (increases prize pool), max prize pool, and current prize pool.

* `..reset` - Shows the time left until the next casino resets

* `..up [number]` - Gives your points to increases Casino EXP

#### Waifu War

* `..claims [card name]` - Claim a card (from Shadowverse) as your waifu ! Consume 10k points and only one person can have one claimer. You will be notified if a card already claimed by someone. Also prevented if a card has been `gifted`.

* `..unclaims [card name]` - Broke up with your waifus. Can only be used if you owned that card beforehand

* `..gift [card name]` - Prevents a claims of a card by other people for a day (counted from the time it's gifted). Consumes points based on how many waifu claimer have - more waifu = more points used ! The formula is `150 * (1 * n)` 

* `..giftall` - A command to automatically gift all your owned waifu. Usefull when you have too many of them but don't want to issue gift individually. 

* `..myclaims` - Shows your waifu list

* `..who [card name]` - Shows the LINE username who has this specific card as their waifu

#### Treasure Hunts

* `..hunt` - Performs treasure hunt which will generate a random prize that will be sold for more points based on huntrate table. Consumes 1 supply and can only be performed again after 5 minutes

* `..huntrate` - Shows the rate of item drop in this room

* `..supply` - Shows your current supply 

* `..resupply [number]` - Buy supply for the hunt. Base cost is 25 / supply   