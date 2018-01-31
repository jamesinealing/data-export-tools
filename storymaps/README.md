# Basic instructions

* you'll need to put the php scripts on a server and add the empty database table to a mysql database using the sql file here
* edit the main script to connect to it to teh database
* change the api call to suit your datasource; you'll also need to change any fieldnames accoridng to teh response you get
* the call storymaps-extract.php which will write the results to a database
* once the records are in the  database you run a mysql query such as `SELECT storymaps.dateText as `name`, CONCAT(geo_places.placename, '<br>', storymaps.title,'. ',storymaps.description, ' <a href=http://www.iwm.org.uk/collections/item/object/',storymaps.objectId ,' target=_blank>&raquo;</a>') as `description`, 'R' as `icon_color`, geo_places.lon as `long`, geo_places.lat as `lat`, storymaps.image_url as `pic_url`, storymaps.thumb_url as `thumb_url`, '' as `is_video` FROM `storymaps`, `geo_places` WHERE storymaps.placename=geo_places.placename AND storymaps.map_name like 'Scots Guards' AND storymaps.date_granularity=0 AND date < '1919-01-01' ORDER BY storymaps.date ASC` and export the results to a csv (obviously modify any query parameters first, go get the resultset that you want, but:
  * `WHERE storymaps.placename=geo_places.placename AND storymaps.map_name like 'Scots Guards'` are essential, changing the map_name to the one you want from your database
  * `AND storymaps.date_granularity=0` - 0 will only retrieve records which have an exact data set
  * `date < '1919-01-01'` is an example of  limiting to a certain date range to get a more focussed / smaller dataset
* go to https://storymaps.arcgis.com/en/ and sign in, then to https://storymaps.arcgis.com/en/my-stories/ and Create Story
* select your map type and the cogs symbol will give you an option to import the csv file that you've exported
* then you're free to check and modify anything in your new Storymap
