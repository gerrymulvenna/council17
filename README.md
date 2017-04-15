# Scottish council elections 2017
Open Data frameworks, datasets and front-end for 2017 council elections in the 32 Scottish councils

## about the project
In Scotland, the Scottish Parliament has responsibility to oversee the local government elections, but there are very few joined-up resources to look at the council elections as a whole, 
as each council runs their own election and publishes data on their own website. This is one citizen's attempt to provide a map-based interface to the candidates in each ward, using the
crowd-sourced data extracted from the SOPNs (Statement of Persons Notified) produced as PDFs by individual councils.


## author
Find Gerry Mulvenna at @gerrymulvenna on Twitter

This site took its starting point from the work carried out by Bob Harper for the #AE17 assembly election in Northern Ireland.
* http://electionsni.org

## the candidate data
For more details on the Democracy Club data see https://candidates.democracyclub.org.uk/help/api

## the map data
Map data came primarily from the Local Government Boundary Commission for Scotland
* http://www.lgbc-scotland.gov.uk/maps/datafiles/index_1995_on.asp

with the top-level Scottish council boundaries coming from the Ordnance Survey "boundary line" package
https://www.ordnancesurvey.co.uk/business-and-government/help-and-support/products/boundary-line.html

The open source application QGIS was used to convert the shapefiles into GEOJSON format with latitute longitude coordinates (EPSG:4326)
http://www.qgis.org/en/site/forusers/download.html

The Map Shaper application was invaluable for simplifying the GEOJSON data to achieve the required level of detail for our purpose and to drastically reduce the size of the boundary
Map Shaper is a very efficient and easy to use online application at http://mapshaper.org/

