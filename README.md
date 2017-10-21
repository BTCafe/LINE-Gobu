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

#### Beta Function - Might not works sometimes
* `..ani [name]` - animated card artwork. 
* `..anievo [name]` - animated card artwork (evolved).
* `..raw [card name]` - borderless card artwork.
* `..rawevo [card name]` - borderless card artwork (evolved).
* `..rawalt [card name]` - borderless alternate card artwork.
* `..rawaltevo [card name]` - borderless alternate card artwork (evolved). 
* `..voice [eng/jpn/kor] [atk/play/evo/die] [card name]` - basic voice function, still needs a lot of improvement !  

<br>Notes : 
<br>- When multiple result found, all command will return the list of possible card and not their respective command.
<br>- The list of possible card will be returned in carousel type message when possible (between 2 - 5) for easier search.
<br>- Type `..help` to see some examples of command function.
***
### Additional Modules #1 - Urban Dictionary
***

This is the function that's used to access Urban Dictionary web via Gobu 

* `..ud [words]` - Returns explanation of that words
* `..explain [words]` - Same as `..ud`
* `..random` - Returns a random explanation 
