# Shadowverse BOT for LINE
This is the main page for Shadowverse BOT in LINE. 

### Adding this bot
Before you can invite the bot to your room, you need to add friend first via this ID : @cyz7687s <br>After that you can invite the bot to your room and all the members in that room will be able to use the bot 

### Command List
All command started with `..` followed by what you want to do. The full list of command is as follows :
* `..find [criteria to search]` - to search for a card based on the inputted criteria.  
* `..name [name]` - to search for a card using their name. 
* `..flair [name]` - to search for a flair of a card using their name. 
* `..img [name]` - to search for card art using their name. 
* `..imgevo [name]` - to search for evolved card art using their name.
* `..alt [name]` - to search for an alternate card art using their name if available.
* `..altevo [name]` - to search for an alternate evolved card art using their name if available.
* `..ani [name]` - to search for an animated card using their name. Currently using URL data stored in database because no known API yet that could provide animated card list 
* `..anievo [name]` - to search for an animated evolved card using their name. Currently using URL data stored in database because no known API yet that could provide animated card list


<br>Notes : 
<br>- When multiple result found, all command will return the list of possible card and not their respective command.
<br>- The list of possible card will be returned in carousel type message when possible (between 2 - 5) for easier search.