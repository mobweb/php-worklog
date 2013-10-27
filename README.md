# php-worklog

A simple tool to log each day's finished work. It also supports E-Mailing the previous day's log entry, for example to yourself or to your boss. The log is stored in a file, so there is no database setup necessary.

## Installation

Simply put the script on your server and the database file on your server. In order for the script to be able to save the log entries, make sure PHP has the necessary read/write permission for the database file.

## Usage

To log a day's finished work, simply browse to the script and input your progress in the corresponding field.

If you want a daily E-Mail notification about yesterday's progress, set up a cronjob to hit up the script with the ```send``` GET parameter, for example:

    http://myserver.com/worklog/index.php?send

The E-Mail will then be sent to the E-Mail adress specified at the beginning of the script. Of course your server has to be able to send E-Mail for this to work. :)

## Roadmap

Some items that I'm planning to implement when I find the time:

- Support for multiple "accounts", so that the tool can be used for a team to track each member's progress

- Ability to edit a log entry and add entries for more than a day ago

- Rudimentary "statistics", tracking which words have been used how much and when. It would show which projects have been worked on at which point :)

## Demo

I have put a simplified version of this script up on my server, altough I have removed write access. But you should get the idea.

[(http://mbwb.info/php-worklog](http://mbwb.info/php-worklog)

## Support

Got a question or spotted a bug? Feel free to E-Mail me at [info@mobweb.ch](mailto:info@mobweb.ch) or send a pull request via GitHub.
