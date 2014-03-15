{\rtf1\ansi\ansicpg1252\cocoartf1038\cocoasubrtf350
{\fonttbl\f0\fswiss\fcharset0 Helvetica;}
{\colortbl;\red255\green255\blue255;}
\paperw11900\paperh16840\margl1440\margr1440\vieww9000\viewh8400\viewkind0
\pard\tx566\tx1133\tx1700\tx2267\tx2834\tx3401\tx3968\tx4535\tx5102\tx5669\tx6236\tx6803\ql\qnatural\pardirnatural

\f0\fs24 \cf0 The sample code provided here shows how to create a simple geolocation layer which returns POIs at different geo locations.\
\
There are 8 files in total: \

\b \
\pard\tx560\tx1120\tx1680\tx2240\tx2800\tx3360\tx3920\tx4480\tx5040\tx5600\tx6160\tx6720\ql\qnatural\pardirnatural

\b0 \cf0 1. 
\b firstTutorial_APIv6_sqlQuery.sql
\b0  - The sql query to set up the database. You can see how the database is constructed. \
\pard\tx566\tx1133\tx1700\tx2267\tx2834\tx3401\tx3968\tx4535\tx5102\tx5669\tx6236\tx6803\ql\qnatural\pardirnatural
\cf0 2. 
\b config.inc.php
\b0  - contains all the database configuration settings. 
\b Please specify them based on your database settings. 
\b0 \
\pard\tx560\tx1120\tx1680\tx2240\tx2800\tx3360\tx3920\tx4480\tx5040\tx5600\tx6160\tx6720\ql\qnatural\pardirnatural
\cf0 \
Two versions of sample code: \
\

\b 1) Simplified version
\b0 \
3. 
\b firstTutorial_simplified.php
\b0  - A limited version for creating a simple geolocation layer. It contains the code provided in the Tutorial itself. You can upload this file and put the link in the API endPoint URL on the publishing site. This will quickly help you create a simple geo-location layer. \
\

\b 2) Generic and complete version 
\b0 \
The following files can be used to provide a more generic and complete version for creating a simple geo-location layer: \
4. 
\b abstract_class.php
\b0  \
5. 
\b commonFuncs.php
\b0 \
6. 
\b POI.php
\b0 \
7. 
\b Layer.php
\b0 \
8.
\b  firstTutorial_complete.php 
\b0 - The link to this file should be put in the API endpoint URL field on the publishing site. 
\b \

\b0 \
You can simply upload all files from 2-8 to the same directory on your server. \
\
}